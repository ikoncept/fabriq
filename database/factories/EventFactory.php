<?php

namespace Ikoncept\Fabriq\Database\Factories;

use Ikoncept\Fabriq\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'start' => $this->faker->dateTimeBetween('-2weeks'),
            'end' => $this->faker->dateTimeBetween('now', '+1week'),
        ];
    }
}
