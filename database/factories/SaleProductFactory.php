<?php

namespace Database\Factories;

use App\Models\SaleProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SaleProduct>
 */
class SaleProductFactory extends Factory
{
    protected $model = SaleProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true) . ' ' . fake()->unique()->numberBetween(1, 9999),
        ];
    }
}
