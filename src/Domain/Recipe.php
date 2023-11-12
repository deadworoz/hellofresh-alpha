<?php

declare(strict_types=1);

namespace App\Domain;

readonly class Recipe
{
    private TimeRange $deliveryTimeRange;

    public function __construct(
        private string $postcode,
        private string $recipe,
        string $delivery,
    ) {
        $timeRange = strpbrk($delivery, ' '); // skip day of week
        $this->deliveryTimeRange = new TimeRange($timeRange);
    }

    public function getPostcode(): string
    {
        return $this->postcode;
    }

    public function getRecipe(): string
    {
        return $this->recipe;
    }

    public function getDeliveryTimeRange(): TimeRange
    {
        return $this->deliveryTimeRange;
    }
}
