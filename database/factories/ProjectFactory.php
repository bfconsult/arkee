<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'pm_user_id' => User::factory(),
            'project_descriptor' => fake()->streetName() . ' Residence',
            'site_name' => fake()->streetName(),
            'site_address' => fake()->address(),
            'site_contact_name' => fake()->name(),
            'next_po_sequence' => 1,
        ];
    }
}
