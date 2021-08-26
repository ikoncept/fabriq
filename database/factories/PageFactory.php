<?php

namespace Database\Factories;

use Ikoncept\Fabriq\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Infab\TranslatableRevisions\Models\RevisionTemplate;

class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'template_id' => RevisionTemplate::factory()->create()->id
        ];
    }
}
