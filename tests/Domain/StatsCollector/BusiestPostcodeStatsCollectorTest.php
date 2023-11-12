<?php

declare(strict_types=1);

namespace App\Tests\Domain\StatsCollector;

use App\Domain\Recipe;
use App\Domain\StatsCollector\BusiestPostcodeStatsCollector;
use Generator;
use PHPUnit\Framework\TestCase;

class BusiestPostcodeStatsCollectorTest extends TestCase
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
        $statsCollector = new BusiestPostcodeStatsCollector();
        foreach ($recipes as $recipe) {
            $statsCollector->consume($recipe);
        }
        $this->assertEquals($expectedStats, $statsCollector->getStats());
    }

    public function provider(): Generator
    {
        yield 'no recipes' => [
            'recipes' => [],
            'expectedStats' => [
                'busiest_postcode' => null,
            ],
        ];

        yield 'busiest postcode 10208' => [
            'recipes' => [
                new Recipe('10208', 'Creamy Dill Pork', 'Wednesday 1AM - 7PM'),
                new Recipe('10208', 'Speedy Steak Fajitas', 'Thursday 7AM - 5PM'),
                new Recipe('10186', 'Cherry Balsamic Pork Chops', 'Thursday 7AM - 9PM'),
                new Recipe('10208', 'Cherry Balsamic Pork Chops', 'Saturday 1AM - 8PM'),
            ],
            'expectedStats' => [
                'busiest_postcode' => [
                    'postcode' => '10208',
                    'delivery_count' => 3,
                ],
            ],
        ];
    }
}
