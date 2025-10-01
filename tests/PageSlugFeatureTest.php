<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Models\Slug;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;

class PageSlugFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_find_a_page_via_its_slugs()
    {
        // Arrange
        // $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $otherPage = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $slug = Slug::create([
            'model_type' => 'fabriq_page',
            'model_id' => $page->id,
            'slug' => 'a-slug',
            'source_string' => 'none',
            'source_key' => 'source_key',
            'locale' => 'sv',
        ]);
        $otherSlug = Slug::create([
            'model_type' => 'fabriq_page',
            'model_id' => $otherPage->id,
            'slug' => 'a-slug',
            'source_string' => 'none',
            'source_key' => 'source_key',
            'locale' => 'sv',
        ]);

        // Act
        $response = $this->json('GET', '/pages/'.$slug->slug.'/live');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'slug' => $slug->slug,
        ]);
    }
}
