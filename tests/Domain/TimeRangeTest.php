<?php

declare(strict_types=1);

namespace Domain;

use App\Domain\TimeRange;
use Generator;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class TimeRangeTest extends TestCase
{
    /**
     * @dataProvider exceptionProvider
     *
     * @param string $timeRange
     *
     * @return void
     */
    public function testException(string $timeRange): void
    {
        $this->expectException(InvalidArgumentException::class);
        new TimeRange($timeRange);
    }

    /**
     * @dataProvider timeRangePairProvider
     */
    public function testContains(TimeRange $a, TimeRange $b, bool $contains): void
    {
        $this->assertEquals($a->contains($b), $contains);
    }

    public static function exceptionProvider(): Generator
    {
        yield 'empty string' => [
            'timeRange' => '',
        ];

        yield 'no start time' => [
            'timeRange' => ' - 12PM',
        ];

        yield 'no end time' => [
            'timeRange' => '12AM - ',
        ];

        yield 'start time > end time' => [
            'timeRange' => '11AM - 12AM',
        ];

        yield 'start time == end time' => [
            'timeRange' => '11AM - 11AM',
        ];
    }

    public static function timeRangePairProvider(): Generator
    {
        yield 'contains the time range completely' => [
            'a' => new TimeRange('10AM - 3PM'),
            'b' => new TimeRange('11AM - 2PM'),
            'contains' => true,
        ];

        yield 'check boundaries' => [
            'a' => new TimeRange('10AM - 3PM'),
            'b' => new TimeRange('10AM - 3PM'),
            'contains' => true,
        ];

        yield 'does not contain left boundary' => [
            'a' => new TimeRange('10AM - 3PM'),
            'b' => new TimeRange('9AM - 3PM'),
            'contains' => false,
        ];

        yield 'does not contain right boundary' => [
            'a' => new TimeRange('10AM - 3PM'),
            'b' => new TimeRange('10AM - 4PM'),
            'contains' => false,
        ];
    }
}
