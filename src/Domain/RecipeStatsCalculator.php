<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\RecipeSource\RecipeSourceInterface;
use App\Domain\StatsCollector\StatsCollectorInterface;

readonly class RecipeStatsCalculator
{
    /**
     * @var array<StatsCollectorInterface>
     */
    private array $statsCollectors;

    public function __construct(StatsCollectorInterface ...$statsCollectors)
    {
        $this->statsCollectors = $statsCollectors;
    }

    public function calculate(RecipeSourceInterface $recipeSource): array
    {
        /** @var array<string, string> $rawRecipe */
        foreach ($recipeSource as $rawRecipe) {
            $recipe = new Recipe(
                $rawRecipe['postcode'],
                $rawRecipe['recipe'],
                $rawRecipe['delivery'],
            );
            foreach ($this->statsCollectors as $collector) {
                $collector->consume($recipe);
            }
        }

        return array_reduce(
            array_map(
                static fn (StatsCollectorInterface $collector) => $collector->getStats(),
                $this->statsCollectors,
            ),
            (static fn (array $agg, array $stat) => $agg+$stat),
            []
        );
    }
}
