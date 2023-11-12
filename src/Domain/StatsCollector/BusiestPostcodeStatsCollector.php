<?php

declare(strict_types=1);

namespace App\Domain\StatsCollector;

use App\Domain\Recipe;

class BusiestPostcodeStatsCollector implements StatsCollectorInterface
{
    /** @var array<string,int> */
    private array $deliveriesByPostcode = [];

    private string $busiestPostcode = '';
    private int $maxDeliveryCount = 0;

    public function consume(Recipe $recipe): void
    {
        $key = $recipe->getPostcode();
        if (isset($this->deliveriesByPostcode[$key])) {
            $this->deliveriesByPostcode[$key]++;
        } else {
            $this->deliveriesByPostcode[$key] = 1;
        }

        if ($this->deliveriesByPostcode[$key] > $this->maxDeliveryCount) {
            $this->maxDeliveryCount = $this->deliveriesByPostcode[$key];
            $this->busiestPostcode = $key;
        }
    }

    public function getStats(): array
    {
        if ($this->busiestPostcode !== '') {
            $busiestPostcode = [
                'postcode' => $this->busiestPostcode,
                'delivery_count' => $this->maxDeliveryCount,
            ];
        } else {
            $busiestPostcode = null;
        }

        return [
            'busiest_postcode' => $busiestPostcode,
        ];
    }
}
