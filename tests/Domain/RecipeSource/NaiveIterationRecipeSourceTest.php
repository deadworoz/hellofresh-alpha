<?php

declare(strict_types=1);

namespace App\Tests\Domain\RecipeSource;

use App\Domain\RecipeSource\NaiveRecipeSource;
use Generator;
use PHPUnit\Framework\TestCase;

class NaiveIterationRecipeSourceTest extends TestCase
{
    /**
     * @dataProvider provider
     *
     * @param array $originalRecipes
     *
     * @return void
     */
    public function test(array $originalRecipes): void
    {
        $memory = fopen('php://memory', 'rb+');
        assert($memory !== false);

        $written = fwrite($memory, json_encode($originalRecipes, JSON_PRETTY_PRINT));
        assert($written !== false);
        fseek($memory, 0);

        $recipeSource = new NaiveRecipeSource($memory);
        $this->assertEquals($originalRecipes, iterator_to_array($recipeSource));
    }

    public static function provider(): Generator
    {
        yield 'base case' => [
            'originalRecipes' => [
                ['postcode' => '10224', 'recipe' => 'Creamy Dill Chicken', 'delivery' => 'Wednesday 1AM - 7PM'],
                ['postcode' => '10225', 'recipe' => 'Speedy Steak Fajitas', 'delivery' => 'Wednesday 3AM - 7PM'],
                ['postcode' => '10226', 'recipe' => 'Cherry Balsamic Pork Chops', 'delivery' => 'Friday 1AM - 7PM'],
                ['postcode' => '10224', 'recipe' => 'Creamy Dill Chicken', 'delivery' => 'Wednesday 1AM - 7PM'],
            ]
        ];

        yield 'empty array' => [
            'originalRecipes' => [],
        ];
    }
}
