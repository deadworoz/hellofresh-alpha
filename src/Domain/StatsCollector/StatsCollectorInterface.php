<?php

declare(strict_types=1);

namespace App\Domain\StatsCollector;

use App\Domain\Recipe;

interface StatsCollectorInterface
{
    public function consume(Recipe $recipe): void;

    public function getStats(): array;
}
