<?php

declare(strict_types=1);

namespace App\Domain\StatsCollector;

use App\Domain\Recipe;

class MatchByNameStatsCollector implements StatsCollectorInterface
{
    private const DUMMY_VALUE = true;

    private array $foundRecipes = [];

    private readonly string $pattern;

    public function __construct(array $wordsToSearch)
    {
        $this->pattern = count($wordsToSearch) > 0
            ? sprintf(
                '/%s/i', // ignore case
                join('|',
                    // matching on individual words only
                    array_map(static fn (string $w) => '\b' . $w . '\b', $wordsToSearch)
                )
            )
            : '';
    }

    public function consume(Recipe $recipe): void
    {
        if ($this->pattern === '' || isset($this->foundRecipes[$recipe->getRecipe()])) {
            return;
        }

        if (preg_match($this->pattern, $recipe->getRecipe()) === 1) {
            $this->foundRecipes[$recipe->getRecipe()] = self::DUMMY_VALUE;
        }
    }

    public function getStats(): array
    {
        ksort($this->foundRecipes);

        return [
            'match_by_name' => array_keys($this->foundRecipes),
        ];
    }
}
