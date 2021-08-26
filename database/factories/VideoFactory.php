<?php

namespace Database\Factories;

use Ikoncept\Fabriq\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Video::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'alt_text' => $this->faker->sentence,
            'caption' => $this->faker->sentence,
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Video $video) {
            //
        })->afterCreating(function (Video $video) {
            $media = $video->addMediaFromString('A nice video media')
                    ->toMediaCollection('videos');
        });
    }
}
