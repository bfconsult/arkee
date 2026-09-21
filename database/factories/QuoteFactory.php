<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quote>
 */
class QuoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'quote_number' => 'Q-' . fake()->unique()->numberBetween(1000, 9999),
            'version' => 1,
            'date' => fake()->date(),
            'status' => fake()->randomElement(['quote', 'complete', 'approved', 'cancelled']),
        ];
    }
}
