<?php

declare(strict_types=1);

namespace App\Domain\StatsCollector;

use App\Domain\Recipe;

class UniqueRecipeStatsCollector implements StatsCollectorInterface
{
    /**
     * @var array<string,int>
     */
    private array $uniqueRecipeNames = [];

    public function consume(Recipe $recipe): void
    {
        $key = $recipe->getRecipe();
        if (isset($this->uniqueRecipeNames[$key])) {
            $this->uniqueRecipeNames[$key]++;
        } else {
            $this->uniqueRecipeNames[$key] = 1;
        }
    }

    public function getStats(): array
    {
        ksort($this->uniqueRecipeNames);

        return [
            'unique_recipe_count' => count($this->uniqueRecipeNames),
            'count_per_recipe' => $this->uniqueRecipeNames,
        ];
    }
}
