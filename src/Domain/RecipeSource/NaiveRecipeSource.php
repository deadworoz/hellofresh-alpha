<?php

declare(strict_types=1);

namespace App\Domain\RecipeSource;

use ArrayIterator;
use InvalidArgumentException;
use Traversable;

class NaiveRecipeSource implements RecipeSourceInterface
{
    private array $recipes;

    public function __construct($file)
    {
        if (!is_resource($file)) {
            throw new InvalidArgumentException('Resource type was expected');
        }
        $content = stream_get_contents($file) ?: '';
        $recipes = json_decode($content, associative: true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($recipes)) {
            throw new InvalidArgumentException('Incorrect file format');
        }
        $this->recipes = $recipes;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->recipes);
    }
}
