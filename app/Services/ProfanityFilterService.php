<?php

namespace App\Services;

class ProfanityFilterService
{
    /**
     * List of profane words to filter
     * Note: This is a basic list. You may want to expand this or load from a database/config file
     */
    private static $profaneWords = [
        // Common profanities (exact words only - no variations to avoid false positives)
        'fuck',
        'shit',
        'bitch',
        'asshole',
        'bastard',
        'piss',
        'dick',
        'pussy',
        'cock',
        'whore',
        'slut',
        'fag',
        'nigger',
        'nigga',
        'retard',
        // Filipino/Tagalog profanities (exact words only)
        'putang',
        'puta',
        'gago',
        'tangina',
        'bobo',
        'ulol',
        'tarantado',
    ];

    /**
     * Check if text contains profanity
     * Only matches exact words (case insensitive) with word boundaries
     *
     * @param string $text
     * @return bool
     */
    public static function containsProfanity(string $text): bool
    {
        if (empty($text)) {
            return false;
        }
        
        // Convert to lowercase for case-insensitive matching
        $lowerText = mb_strtolower($text);
        
        // Check each profane word as an exact whole word match only
        foreach (self::$profaneWords as $profaneWord) {
            // Use word boundaries to match exact words only
            // \b matches word boundaries (between word and non-word characters)
            $pattern = '/\b' . preg_quote(mb_strtolower($profaneWord), '/') . '\b/u';
            if (preg_match($pattern, $lowerText)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Filter profanity from text by replacing with asterisks
     *
     * @param string $text
     * @param string $replacement
     * @return string
     */
    public static function filterProfanity(string $text, string $replacement = '***'): string
    {
        $originalText = $text;
        $text = mb_strtolower($text);
        
        foreach (self::$profaneWords as $profaneWord) {
            // Case-insensitive replacement
            $pattern = '/\b' . preg_quote($profaneWord, '/') . '\b/i';
            $originalText = preg_replace($pattern, $replacement, $originalText);
        }
        
        return $originalText;
    }

    /**
     * Get list of profane words found in text
     * Only matches exact words (case insensitive) with word boundaries
     *
     * @param string $text
     * @return array
     */
    public static function getProfaneWordsFound(string $text): array
    {
        $found = [];
        
        if (empty($text)) {
            return $found;
        }
        
        $lowerText = mb_strtolower($text);
        
        foreach (self::$profaneWords as $profaneWord) {
            // Use word boundaries to match exact words only
            $pattern = '/\b' . preg_quote(mb_strtolower($profaneWord), '/') . '\b/u';
            if (preg_match($pattern, $lowerText) && !in_array($profaneWord, $found)) {
                $found[] = $profaneWord;
            }
        }
        
        return $found;
    }

    /**
     * Validate content and header for profanity
     *
     * @param string|null $content
     * @param string|null $header
     * @return array ['is_valid' => bool, 'message' => string, 'found_words' => array]
     */
    public static function validateContent(?string $content = null, ?string $header = null): array
    {
        $foundWords = [];
        
        if ($content && self::containsProfanity($content)) {
            $foundWords = array_merge($foundWords, self::getProfaneWordsFound($content));
        }
        
        if ($header && self::containsProfanity($header)) {
            $foundWords = array_merge($foundWords, self::getProfaneWordsFound($header));
        }
        
        $foundWords = array_unique($foundWords);
        
        if (!empty($foundWords)) {
            return [
                'is_valid' => false,
                'message' => 'Your post contains inappropriate language. Please remove offensive words before posting.',
                'found_words' => $foundWords
            ];
        }
        
        return [
            'is_valid' => true,
            'message' => '',
            'found_words' => []
        ];
    }
}

