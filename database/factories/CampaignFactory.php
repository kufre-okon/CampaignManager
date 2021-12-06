<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(30),
            'date_from' => $this->faker->date('Y-m-d', 'now'),
            'date_to' => date('Y-m-d'),
            'total_budget' => $this->faker->numberBetween(1000, 2000),
            'daily_budget' => $this->faker->numberBetween(50, 80),
        ];
    }
}
