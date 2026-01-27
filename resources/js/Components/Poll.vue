<template>
    <div v-if="poll && poll.options && poll.options.length > 0" class="poll-container">
        <div class="poll-header">
            <span class="poll-vote-count">{{ poll.total_votes || 0 }} {{ (poll.total_votes || 0) === 1 ? 'vote' : 'votes' }}</span>
        </div>
        
        <div class="poll-options">
            <div 
                v-for="(option, index) in poll.options" 
                :key="option.id"
                class="poll-option"
                :class="{ 
                    'selected': poll.user_voted_option_id === option.id,
                    'voted': poll.user_voted_option_id !== null,
                    [`option-${index % 4}`]: true
                }"
                @click="handleVote(option.id)"
            >
                <div class="poll-option-content">
                    <div class="poll-option-text">{{ option.option_text || 'Option' }}</div>
                    <div v-if="poll.user_voted_option_id !== null" class="poll-option-stats">
                        <span class="poll-percentage">{{ option.percentage || 0 }}%</span>
                        <span class="poll-vote-count-option">{{ option.vote_count || 0 }} {{ (option.vote_count || 0) === 1 ? 'vote' : 'votes' }}</span>
                    </div>
                </div>
                <div 
                    v-if="poll.user_voted_option_id !== null" 
                    class="poll-progress-bar"
                    :style="{ width: (option.percentage || 0) + '%' }"
                ></div>
            </div>
        </div>
    </div>
    <div v-else-if="poll" class="poll-container poll-error">
        <p>Poll options are not available.</p>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    poll: {
        type: Object,
        default: null
    },
    pollId: {
        type: Number,
        default: null
    }
})

const emit = defineEmits(['vote-updated'])

const isVoting = ref(false)

const handleVote = async (optionId) => {
    if (!props.poll || !props.pollId) {
        console.error('Poll or pollId is missing', { poll: props.poll, pollId: props.pollId })
        return
    }
    if (isVoting.value) return
    
    isVoting.value = true
    
    try {
        const response = await axios.post(route('polls.vote', props.pollId), {
            option_id: optionId
        })
        
        if (response.data.success) {
            // Update poll data
            if (props.poll) {
                Object.assign(props.poll, response.data.poll)
            }
            emit('vote-updated', response.data.poll)
        }
    } catch (error) {
        console.error('Error voting:', error)
        if (error.response?.data?.message) {
            alert(error.response.data.message)
        } else {
            alert('Failed to record vote. Please try again.')
        }
    } finally {
        isVoting.value = false
    }
}

// Debug: Log poll data when component mounts
import { onMounted, watch } from 'vue'
onMounted(() => {
    console.log('🔵 Poll component mounted', {
        pollId: props.pollId,
        hasPoll: !!props.poll,
        poll: props.poll,
        optionsCount: props.poll?.options?.length || 0,
        options: props.poll?.options,
        totalVotes: props.poll?.total_votes || 0
    })
    
    if (!props.poll) {
        console.warn('⚠️ Poll component mounted but poll prop is null/undefined')
    } else if (!props.poll.options || props.poll.options.length === 0) {
        console.warn('⚠️ Poll component mounted but has no options', props.poll)
    }
})

// Watch for poll changes
watch(() => props.poll, (newPoll) => {
    console.log('🔵 Poll data changed:', {
        poll: newPoll,
        optionsCount: newPoll?.options?.length || 0
    })
}, { deep: true })
</script>

<style scoped>
.poll-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-top: 15px;
    border: 1px solid #e0e0e0;
}

.poll-header {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    margin-bottom: 15px;
}

.poll-vote-count {
    font-size: 14px;
    color: #666;
    font-weight: 600;
}

.poll-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.poll-option {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    padding: 12px 15px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    overflow: hidden;
}

.poll-option:hover {
    border-color: #ff8c42;
    background: #fff7ef;
}

.poll-option.selected {
    border-color: #ff8c42;
    background: #fff7ef;
}

/* Different color coding for each option */
.poll-option.option-0 {
    border-left: 4px solid #ff8c42;
}

.poll-option.option-0.selected {
    background: #fff7ef;
    border-color: #ff8c42;
}

.poll-option.option-1 {
    border-left: 4px solid #4a90e2;
}

.poll-option.option-1.selected {
    background: #e8f2ff;
    border-color: #4a90e2;
}

.poll-option.option-2 {
    border-left: 4px solid #50c878;
}

.poll-option.option-2.selected {
    background: #e8f8f0;
    border-color: #50c878;
}

.poll-option.option-3 {
    border-left: 4px solid #9b59b6;
}

.poll-option.option-3.selected {
    background: #f4ecf7;
    border-color: #9b59b6;
}

.poll-option.voted {
    cursor: default;
}

.poll-option.voted:hover {
    border-color: #e0e0e0;
    background: white;
}

.poll-option-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 2;
}

.poll-option-text {
    font-size: 15px;
    font-weight: 600;
    color: #333;
    flex: 1;
}

.poll-option-stats {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
}

.poll-percentage {
    font-weight: 700;
}

/* Different percentage colors for each option */
.poll-option.option-0 .poll-percentage {
    color: #ff8c42;
}

.poll-option.option-1 .poll-percentage {
    color: #4a90e2;
}

.poll-option.option-2 .poll-percentage {
    color: #50c878;
}

.poll-option.option-3 .poll-percentage {
    color: #9b59b6;
}

.poll-vote-count-option {
    color: #666;
    font-weight: 600;
}

.poll-progress-bar {
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    opacity: 0.15;
    transition: width 0.3s ease;
    z-index: 1;
}

/* Different progress bar colors for each option */
.poll-option.option-0 .poll-progress-bar {
    background: linear-gradient(135deg, #ff8c42, #ff7a28);
}

.poll-option.option-1 .poll-progress-bar {
    background: linear-gradient(135deg, #4a90e2, #357abd);
}

.poll-option.option-2 .poll-progress-bar {
    background: linear-gradient(135deg, #50c878, #3da85c);
}

.poll-option.option-3 .poll-progress-bar {
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
}
</style>

