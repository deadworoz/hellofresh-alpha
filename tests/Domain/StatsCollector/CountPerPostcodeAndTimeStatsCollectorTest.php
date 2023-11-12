<?php

declare(strict_types=1);

namespace App\Tests\Domain\StatsCollector;

use App\Domain\Recipe;
use App\Domain\StatsCollector\CountPerPostcodeAndTimeStatsCollector;
use App\Domain\TimeRange;
use Generator;
use PHPUnit\Framework\TestCase;

class CountPerPostcodeAndTimeStatsCollectorTest extends TestCase
{
    /**
     * @dataProvider provider
     *
     * @param string $postcode
     * @param TimeRange $timeRange
     * @param Recipe[] $recipes
     * @param array<string,array> $expectedStats
     *
     * @return void
     */
    public function test(string $postcode, TimeRange $timeRange, array $recipes, array $expectedStats): void
    {
        $statsCollector = new CountPerPostcodeAndTimeStatsCollector($postcode, $timeRange);
        foreach ($recipes as $recipe) {
            $statsCollector->consume($recipe);
        }
        $this->assertEquals($expectedStats, $statsCollector->getStats());
    }

    public static function provider(): Generator
    {
        yield 'base case' => [
            'postcode' => '10224',
            'timeRange' => new TimeRange('10AM - 6PM'),
            'recipes' => [
                new Recipe('10224', 'Speedy Steak Fajitas', 'Thursday 1PM - 2PM'),
                new Recipe('10224', 'A lot of steaks', 'Wednesday 11AM - 5PM'),
                new Recipe('99999', 'Cherry Balsamic Pork Chops', 'Thursday 1PM - 2PM'),
            ],
            'expectedStats' => [
                'count_per_postcode_and_time' => [
                    'postcode' => '10224',
                    'from' => '10AM',
                    'to' => '6PM',
                    'delivery_count' => 2,
                ],
            ],
        ];

        yield 'no recipes' => [
            'postcode' => '10224',
            'timeRange' => new TimeRange('10AM - 3PM'),
            'recipes' => [],
            'expectedStats' => [
                'count_per_postcode_and_time' => [
                    'postcode' => '10224',
                    'from' => '10AM',
                    'to' => '3PM',
                    'delivery_count' => 0,
                ],
            ],
        ];

        yield 'afternoon' => [
            'postcode' => '10224',
            'timeRange' => new TimeRange('12PM - 6PM'),
            'recipes' => [
                new Recipe('10224', 'Speedy Steak Fajitas', 'Thursday 1PM - 2PM'),
                new Recipe('10224', 'A lot of steaks', 'Wednesday 1PM - 5PM'),
                new Recipe('99999', 'Cherry Balsamic Pork Chops', 'Thursday 1PM - 2PM'),
            ],
            'expectedStats' => [
                'count_per_postcode_and_time' => [
                    'postcode' => '10224',
                    'from' => '12PM',
                    'to' => '6PM',
                    'delivery_count' => 2,
                ],
            ],
        ];
    }
}
