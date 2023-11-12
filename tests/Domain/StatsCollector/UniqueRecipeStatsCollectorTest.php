<?php

declare(strict_types=1);

namespace App\Tests\Domain\StatsCollector;

use App\Domain\Recipe;
use App\Domain\StatsCollector\UniqueRecipeStatsCollector;
use Generator;
use PHPUnit\Framework\TestCase;

class UniqueRecipeStatsCollectorTest extends TestCase
{
    /**
     * @dataProvider provider
     *
     * @param Recipe[] $recipes
     * @param array<string,array> $expectedStats
     *
     * @return void
     */
    public function test(array $recipes, array $expectedStats): void
    {
        $statsCollector = new UniqueRecipeStatsCollector();
        foreach ($recipes as $recipe) {
            $statsCollector->consume($recipe);
        }
        $this->assertEquals($expectedStats, $statsCollector->getStats());
    }

    public static function provider(): Generator
    {
        yield 'no recipes' => [
            'recipes' => [],
            'expectedStats' => [
                'unique_recipe_count' => 0,
                'count_per_recipe' => [],
            ],
        ];

        yield 'recipe names with duplicates' => [
            'recipes' => [
                new Recipe('10224', 'Creamy Dill Pork', 'Wednesday 1AM - 7PM'),
                new Recipe('10208', 'Speedy Steak Fajitas', 'Thursday 7AM - 5PM'),
                new Recipe('10120', 'Cherry Balsamic Pork Chops', 'Thursday 7AM - 9PM'),
                new Recipe('10186', 'Cherry Balsamic Pork Chops', 'Saturday 1AM - 8PM'),
            ],
            'expectedStats' => [
                'unique_recipe_count' => 3,
                'count_per_recipe' => [
                    'Cherry Balsamic Pork Chops' => 2,
                    'Creamy Dill Pork' => 1,
                    'Speedy Steak Fajitas' => 1,
                ],
            ],
        ];
    }
}
