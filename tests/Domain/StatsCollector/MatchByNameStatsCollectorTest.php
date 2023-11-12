<?php

declare(strict_types=1);

namespace App\Tests\Domain\StatsCollector;

use App\Domain\Recipe;
use App\Domain\StatsCollector\MatchByNameStatsCollector;
use Generator;
use PHPUnit\Framework\TestCase;

class MatchByNameStatsCollectorTest extends TestCase
{
    /**
     * @dataProvider provider
     *
     * @param string[] $words
     * @param Recipe[] $recipes
     * @param array<string,array> $expectedStats
     *
     * @return void
     */
    public function test(array $words, array $recipes, array $expectedStats): void
    {
        $statsCollector = new MatchByNameStatsCollector($words);
        foreach ($recipes as $recipe) {
            $statsCollector->consume($recipe);
        }
        $this->assertEquals($expectedStats, $statsCollector->getStats());
    }

    public function provider(): Generator
    {
        yield 'no words' => [
            'words' => [],
            'recipes' => [new Recipe('10224', 'Creamy Dill Chicken', 'Wednesday 1AM - 7PM')],
            'expectedStats' => [
                'match_by_name' => [],
            ],
        ];

        yield 'individual words only' => [
            'words' => ['steak'],
            'recipes' => [
                new Recipe('10208', 'Speedy Steak Fajitas', 'Thursday 7AM - 5PM'),
                new Recipe('10200', 'A lot of steaks', 'Thursday 7AM - 5PM'),
            ],
            'expectedStats' => [
                'match_by_name' => ['Speedy Steak Fajitas'],
            ],
        ];

        yield 'unique recipe names' => [
            'words' => ['pork'],
            'recipes' => [
                new Recipe('10224', 'Creamy Dill Pork', 'Wednesday 1AM - 7PM'),
                new Recipe('10208', 'Speedy Steak Fajitas', 'Thursday 7AM - 5PM'),
                new Recipe('10120', 'Cherry Balsamic Pork Chops', 'Thursday 7AM - 9PM'),
                new Recipe('10186', 'Cherry Balsamic Pork Chops', 'Saturday 1AM - 8PM'),
            ],
            'expectedStats' => [
                'match_by_name' => [
                    'Cherry Balsamic Pork Chops',
                    'Creamy Dill Pork',
                ],
            ],
        ];
    }
}
