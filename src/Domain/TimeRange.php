<?php

declare(strict_types=1);

namespace App\Domain;

class TimeRange
{
    public function __construct(string $timeRange)
    {
    }

    public function contains(TimeRange $timeRange)
    {
        return true;
    }
}
