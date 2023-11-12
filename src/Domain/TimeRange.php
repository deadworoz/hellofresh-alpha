<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

readonly class TimeRange
{
    private int $from;
    private int $to;

    public function __construct(string $timeRange)
    {
        $range = explode('-', $timeRange);
        if (count($range) !== 2) {
            throw new InvalidArgumentException('Wrong time range format');
        }

        list($from, $to) = $range;
        $from = date_parse($from)['hour'] ?: null;
        $to = date_parse($to)['hour'] ?: null;
        if ($from === null || $to === null || $from >= $to) {
            throw new InvalidArgumentException('Wrong time range format');
        }

        $this->from = $from;
        $this->to = $to;
    }

    public function contains(TimeRange $timeRange): bool
    {
        return $this->from <= $timeRange->from && $timeRange->to <= $this->to;
    }

    public function getFrom(): string
    {
        return self::to12ClockFormat($this->from);
    }

    public function getTo(): string
    {
        return self::to12ClockFormat($this->to);
    }

    private static function to12ClockFormat(int $hour): string
    {
        $clock12Hour = $hour % 12;
        if ($clock12Hour === 0) {
            $clock12Hour = 12;
        }

        $suffix = $hour < 12 ? 'AM' : 'PM';
        return $clock12Hour . $suffix;
    }
}
