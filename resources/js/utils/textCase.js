export function normalizeSpaces(value) {
  if (value === null || value === undefined) return ''
  return String(value).replace(/\s+/g, ' ').trim()
}

function capitalizeSegment(segment) {
  if (!segment) return ''
  const lower = segment.toLowerCase()
  return lower.charAt(0).toUpperCase() + lower.slice(1)
}

function titleCaseToken(token) {
  // Keep tokens that are purely roman numerals / abbreviations, e.g. "III", "IV", "Jr."
  const raw = token
  if (!raw) return raw

  const compact = raw.replace(/\./g, '')
  if (/^[A-Z0-9]+$/.test(compact) && compact.length <= 5) {
    return raw
  }

  // Split on hyphens and apostrophes but keep them in output.
  // Examples: "jean-paul" -> "Jean-Paul", "o'connor" -> "O'Connor"
  return raw
    .split(/([\-'])/)
    .map((part) => (part === '-' || part === "'" ? part : capitalizeSegment(part)))
    .join('')
}

/**
 * Title-case a free-text name-ish value.
 * - Trims and collapses whitespace
 * - Capitalizes each space-separated token
 * - Handles hyphens/apostrophes inside tokens
 */
export function toTitleCase(value) {
  const s = normalizeSpaces(value)
  if (!s) return ''
  return s
    .split(' ')
    .map(titleCaseToken)
    .join(' ')
}

