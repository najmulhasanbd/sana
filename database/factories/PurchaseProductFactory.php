<?php

namespace Database\Factories;

use App\Models\PurchaseProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseProduct>
 */
class PurchaseProductFactory extends Factory
{
    protected $model = PurchaseProduct::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'রুই পোনা', 'কাতলা পোনা', 'মৃগেল পোনা', 'পাঙ্গাশ পোনা',
                'তেলাপিয়া পোনা', 'শিং মাছের পোনা', 'মাগুর পোনা', 'কার্পিও পোনা',
                'সিলভার কার্প', 'ব্ল্যাক কার্প', 'চিতল মাছের পোনা', 'আইড় মাছের পোনা',
            ]),
        ];
    }
}
