<?php

namespace Ikoncept\Fabriq\Database\Factories;

use Ikoncept\Fabriq\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'caption' => $this->faker->sentence,
            'alt_text' => $this->faker->sentence(3)
        ];
    }


    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Image $image) {
            //
        })->afterCreating(function (Image $image) {
            $media = $image->addMediaFromString('A nice media')
                    ->toMediaCollection('images');
        });
    }

}
