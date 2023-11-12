<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\RecipeSource\RecipeSourceInterface;
use App\Domain\StatsCollector\StatsCollectorInterface;

readonly class RecipeStatsCalculator
{
    /**
     * @var list<StatsCollectorInterface>
     */
    private array $statsCollectors;

    public function __construct(StatsCollectorInterface ...$statsCollectors)
    {
        $this->statsCollectors = $statsCollectors;
    }

    public function calculate(RecipeSourceInterface $recipeSource): array
    {
        /** @var Recipe $recipe */
        foreach ($recipeSource as $recipe) {
            foreach ($this->statsCollectors as $collector) {
                $collector->consume($recipe);
            }
        }

        return array_merge(
            array_map(
                static fn (StatsCollectorInterface $collector) => $collector->getStats(),
                $this->statsCollectors,
            )
        );
    }
}
