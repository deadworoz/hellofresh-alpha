<?php

declare(strict_types=1);

namespace App\Domain\StatsCollector;

use App\Domain\Recipe;
use App\Domain\TimeRange;

class CountPerPostcodeAndTimeStatsCollector implements StatsCollectorInterface
{
    private int $deliveryCount = 0;

    public function __construct(private readonly string $postcode, private readonly TimeRange $timeRange)
    {
    }

    public function consume(Recipe $recipe): void
    {
        if ($this->postcode === $recipe->getPostcode() && $this->timeRange->contains($recipe->getDeliveryTimeRange())) {
            $this->deliveryCount++;
        }
    }

    public function getStats(): array
    {
        return [
            'count_per_postcode_and_time' => [
                'postcode' => $this->postcode,
                'from' => $this->timeRange->getFrom(),
                'to' => $this->timeRange->getTo(),
                'delivery_count' => $this->deliveryCount,
            ],
        ];
    }
}
