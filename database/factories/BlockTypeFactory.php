<?php

namespace Database\Factories;

use Ikoncept\Fabriq\Models\BlockType;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlockTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BlockType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name . ' -block' ,
            'component_name' => $this->faker->domainWord
        ];
    }
}
