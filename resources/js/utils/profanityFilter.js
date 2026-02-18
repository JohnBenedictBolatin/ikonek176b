/**
 * Client-side profanity filter utility
 * Note: This is for immediate user feedback. Server-side validation is the real security layer.
 */

// List of profane words (exact words only - same as server-side for consistency)
const profaneWords = [
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
 * @param {string} text - Text to check
 * @returns {boolean}
 */
export function containsProfanity(text) {
    if (!text || typeof text !== 'string') {
        return false;
    }

    const lowerText = text.toLowerCase();

    // Check each profane word as an exact whole word match only
    for (const profaneWord of profaneWords) {
        // Use word boundaries to match exact words only
        // \b matches word boundaries (between word and non-word characters)
        const pattern = new RegExp(`\\b${profaneWord.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\b`, 'i');
        if (pattern.test(lowerText)) {
            return true;
        }
    }

    return false;
}

/**
 * Get list of profane words found in text
 * Only matches exact words (case insensitive) with word boundaries
 * @param {string} text - Text to check
 * @returns {string[]}
 */
export function getProfaneWordsFound(text) {
    if (!text || typeof text !== 'string') {
        return [];
    }

    const found = [];
    const lowerText = text.toLowerCase();

    for (const profaneWord of profaneWords) {
        // Use word boundaries to match exact words only
        const pattern = new RegExp(`\\b${profaneWord.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}\\b`, 'i');
        if (pattern.test(lowerText) && !found.includes(profaneWord)) {
            found.push(profaneWord);
        }
    }

    return found;
}

/**
 * Validate content for profanity
 * @param {string|null} content - Post content
 * @param {string|null} header - Post header
 * @returns {{isValid: boolean, message: string, foundWords: string[]}}
 */
export function validateContent(content = null, header = null) {
    const foundWords = [];

    if (content && containsProfanity(content)) {
        foundWords.push(...getProfaneWordsFound(content));
    }

    if (header && containsProfanity(header)) {
        foundWords.push(...getProfaneWordsFound(header));
    }

    const uniqueFoundWords = [...new Set(foundWords)];

    if (uniqueFoundWords.length > 0) {
        return {
            isValid: false,
            message: 'Your post contains inappropriate language. Please remove offensive words before posting.',
            foundWords: uniqueFoundWords
        };
    }

    return {
        isValid: true,
        message: '',
        foundWords: []
    };
}

