<?php

namespace Phiki\TextMate;

use Phiki\Contracts\GrammarRepositoryInterface;
use Phiki\Contracts\PatternInterface;
use Phiki\Exceptions\FailedToInitializePatternSearchException;
use Phiki\Exceptions\FailedToSetSearchPositionException;
use Phiki\Grammar\MatchedPattern;
use Phiki\Grammar\ParsedGrammar;
use Phiki\Grammar\WhilePattern;

class PatternSearcher
{
    /**
     * Create a new instance.
     */
    public function __construct(
        protected PatternInterface $pattern,
        protected ParsedGrammar $grammar,
        protected GrammarRepositoryInterface $grammars,
        protected bool $allowA,
        protected bool $allowG,
    ) {}

    /**
     * Find the next closest match in the given text.
     */
    public function findNextMatch(string $lineText, int $linePos, bool $while = false): ?MatchedPattern
    {
        $patterns = $while && $this->pattern instanceof WhilePattern
            ? [
                [$this->pattern, $this->pattern->while->get($this->allowA, $this->allowG)],
            ] : $this->pattern->compile($this->grammar, $this->grammars, $this->allowA, $this->allowG);
        $bestLocation = null;
        $bestLength = null;
        $bestMatches = null;
        $bestPattern = null;

        if (! mb_ereg_search_init($lineText)) {
            throw new FailedToInitializePatternSearchException;
        }

        foreach ($patterns as [$pattern, $regex]) {
            if (! mb_ereg_search_setpos($linePos)) {
                throw new FailedToSetSearchPositionException;
            }

            $result = @mb_ereg_search_pos($regex);

            if ($result === false) {
                continue;
            }

            [$start, $length] = $result;

            if ($start === $linePos) {
                $bestLocation = $start;
                $bestMatches = mb_ereg_search_getregs();
                $bestLength = $length;
                $bestPattern = $pattern;

                break;
            }

            if ($start < $bestLocation || $bestLocation === null) {
                $bestLocation = $start;
                $bestLength = $length;
                $bestMatches = mb_ereg_search_getregs();
                $bestPattern = $pattern;

                continue;
            }
        }

        if ($bestPattern === null) {
            return null;
        }

        // Since we know the start position and length of the match, we can
        // extract the relevant portion of the input string to reduce the
        // search grid for subsequent matches.
        $substr = mb_substr($lineText, $bestLocation, $bestLength);
        $keyToIndexMap = array_flip(array_keys($bestMatches));
        $wellFormedMatches = [];

        foreach ($bestMatches as $key => $match) {
            // The first match is the full match, so we can just use the start position.
            if ($key === 0) {
                $wellFormedMatches[$key] = [$match, $bestLocation];

                continue;
            }

            $key = is_string($key) ? $keyToIndexMap[$key] : $key;

            // If the capture group is empty, we need to use the same format as PCRE's PREG_OFFSET_CAPTURE,
            // which is an array with an empty match and -1 as the offset.
            if (! $match) {
                $wellFormedMatches[$key] = ['', -1];

                continue;
            }

            // For subsequent matches, we can use the reduced search grid to find the position
            // of the match within the substring. We need to adjust the position based on the
            // original input string's start position.
            $pos = mb_strpos($substr, $match);

            // We can then store the value in the matches array with the adjusted position.
            $wellFormedMatches[$key] = [$match, $bestLocation + $pos];
        }

        return new MatchedPattern($bestPattern, $wellFormedMatches);
    }
}
