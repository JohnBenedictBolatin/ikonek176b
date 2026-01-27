<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostPoll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PollController extends Controller
{
    /**
     * Store a vote for a poll option
     */
    public function vote(Request $request, $pollId)
    {
        $validated = $request->validate([
            'option_id' => ['required', 'integer', 'exists:poll_options,id'],
        ]);

        try {
            $userId = Auth::id();
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be logged in to vote.',
                ], 401);
            }

            // Get the poll with options
            $poll = PostPoll::with('options')->findOrFail($pollId);

            // Check if user has already voted
            $existingVote = PollVote::where('poll_id', $pollId)
                ->where('user_id', $userId)
                ->first();

            DB::beginTransaction();
            
            if ($existingVote) {
                // User already voted, update their vote
                $oldOptionId = $existingVote->option_id;
                
                // Update vote to new option
                $existingVote->option_id = $validated['option_id'];
                $existingVote->save();
            } else {
                // New vote
                PollVote::create([
                    'user_id' => $userId,
                    'poll_id' => $pollId,
                    'option_id' => $validated['option_id'],
                ]);
            }
            
            // Recalculate vote counts from actual votes (more reliable)
            $voteCounts = PollVote::where('poll_id', $pollId)
                ->selectRaw('option_id, COUNT(*) as count')
                ->groupBy('option_id')
                ->pluck('count', 'option_id')
                ->toArray();
            
            // Update all poll options with correct vote counts
            foreach ($poll->options as $option) {
                $actualCount = $voteCounts[$option->id] ?? 0;
                $option->vote_count = $actualCount;
                $option->save();
            }
            
            DB::commit();

            // Get updated poll data
            $poll = PostPoll::with(['options' => function($query) {
                $query->orderBy('id', 'asc');
            }])->findOrFail($pollId);

            $totalVotes = $poll->votes()->count();
            $userVote = PollVote::where('poll_id', $pollId)
                ->where('user_id', $userId)
                ->first();

            $options = $poll->options->map(function($option) use ($totalVotes) {
                return [
                    'id' => $option->id,
                    'option_text' => $option->option_text,
                    'vote_count' => max(0, $option->vote_count), // Ensure non-negative
                    'percentage' => $totalVotes > 0 ? round((max(0, $option->vote_count) / $totalVotes) * 100, 1) : 0,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Vote recorded successfully.',
                'poll' => [
                    'id' => $poll->id,
                    'total_votes' => $totalVotes,
                    'user_voted_option_id' => $userVote ? $userVote->option_id : null,
                    'options' => $options,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error voting on poll: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error recording vote: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get poll data for a post
     */
    public function getPoll($postId)
    {
        try {
            $post = Post::with(['poll.options', 'poll.votes'])->findOrFail($postId);
            
            if (!$post->poll) {
                return response()->json([
                    'success' => false,
                    'message' => 'This post does not have a poll.',
                ], 404);
            }

            $userId = Auth::id();
            $poll = $post->poll;
            $totalVotes = $poll->votes()->count();
            
            $userVote = null;
            if ($userId) {
                $userVote = PollVote::where('poll_id', $poll->id)
                    ->where('user_id', $userId)
                    ->first();
            }

            $options = $poll->options->map(function($option) use ($totalVotes) {
                return [
                    'id' => $option->id,
                    'option_text' => $option->option_text,
                    'vote_count' => $option->vote_count,
                    'percentage' => $totalVotes > 0 ? round(($option->vote_count / $totalVotes) * 100, 1) : 0,
                ];
            });

            return response()->json([
                'success' => true,
                'poll' => [
                    'id' => $poll->id,
                    'total_votes' => $totalVotes,
                    'user_voted_option_id' => $userVote ? $userVote->option_id : null,
                    'options' => $options,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching poll: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching poll: ' . $e->getMessage(),
            ], 500);
        }
    }
}

