<?php

namespace Ikoncept\Fabriq\Database\Factories;

use Ikoncept\Fabriq\Models\SmartBlock;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmartBlockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SmartBlock::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
