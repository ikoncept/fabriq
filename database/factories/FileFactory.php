<?php

namespace Database\Factories;

use Ikoncept\Fabriq\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

class FileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'caption' => $this->faker->sentence,
            'readable_name' => $this->faker->sentence(3)
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (File $file) {
            //
        })->afterCreating(function (File $file) {
            $media = $file->addMediaFromString('A nice file media')
                    ->toMediaCollection('files');
        });
    }
}
