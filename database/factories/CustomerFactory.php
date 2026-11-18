<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'mobile' => '01' . fake()->unique()->numerify('#########'),
            'email' => fake()->unique()->safeEmail(),
            'address' => fake()->address(),
            'note' => fake()->sentence(),
            'status' => true,
        ];
    }
}
