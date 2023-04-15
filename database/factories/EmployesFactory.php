<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employes>
 */
class EmployesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $phone = sprintf(fake()->regexify('08[0-9]{10}'));
        return [
            'name' => fake()->name(),
            'phone' => $phone,
            'email' => fake()->email(),
            'address' => fake()->address(),
            'gender' => 'male',
            'created_at' => date('Y-m-d H:i:s', time()),
            'updated_at' => date('Y-m-d H:i:s', time()),
        ];
    }
}
