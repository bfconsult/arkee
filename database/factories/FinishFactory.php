<?php

namespace Database\Factories;

use App\Models\Finish;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Finish>
 */
class FinishFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Natural Oak', 'Walnut Stain', 'Matte Black', 'Brushed Brass']),
            'type' => fake()->randomElement([Finish::TYPE_TIMBER_STAIN, Finish::TYPE_FABRIC, Finish::TYPE_OTHER]),
        ];
    }
}
