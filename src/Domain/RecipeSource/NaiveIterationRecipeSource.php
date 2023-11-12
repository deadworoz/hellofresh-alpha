<?php

declare(strict_types=1);

namespace App\Domain\RecipeSource;

use InvalidArgumentException;
use Traversable;

class NaiveIterationRecipeSource implements RecipeSourceInterface
{
    private $file;
    private ?array $current = [];

    public function __construct($file)
    {
        if (!is_resource($file)) {
            throw new InvalidArgumentException('Resource type was expected');
        }
        $this->file = $file;
        fgets($this->file); // skip [
    }

    public function next(): void
    {
        $s = '';
        $s .= fgets($this->file); // {
        $s .= fgets($this->file); // postcode
        $s .= fgets($this->file); // recipe
        $s .= fgets($this->file); // delivery
        $s .= fgets($this->file); // }
        $s = rtrim($s, "\n\r ,");
        $this->current = json_decode($s, associative: true);
    }

    public function getIterator(): Traversable
    {
        while (true) {
            $this->next();
            if ($this->current === null) {
                break;
            }

            yield $this->current;
        }
    }
}
