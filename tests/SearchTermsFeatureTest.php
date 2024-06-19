<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Database\Seeders\DatabaseSeeder;
use Ikoncept\Fabriq\Database\Seeders\PageTemplateSeeder;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Infab\TranslatableRevisions\Models\RevisionTemplate;

class SearchTermsFeatureTest extends AdminUserTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        app(DatabaseSeeder::class)->call(PageTemplateSeeder::class);
    }

    public function test_it_can_include_content_for_a_single_page()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'En sida som ska publiceras',
            'template_id' => RevisionTemplate::all()->first()->id,
            'revision' => 1,
        ]);

        $page->updateContent([
            'page_title' => 'The page title for the page',
            'page_header' => 'The page title for the page',
            'page_content' => '<h1>Wow, a header</h1><p>Ok lets see</p>',
            'meta_title' => 'Meta title',
            'meta_description' => 'Describing the page',
            'meta_og_image' => 'https://placehold.it/40',
            'boxes' => [
                ['header' => 'EN Box 1 title!', 'url' => 'https://google.com'],
                ['header' => 'EN Box 2 title!', 'url' => 'https://bog.com'],
                ['header' => 'EN Box 3 title!', 'url' => 'http://flank.se'],
            ],
        ], 'en');
        $page->updateContent([
            'page_title' => 'En siee saom skau paublisers',
            'page_header' => 'En siee saom skau paublisers',
            'page_content' => '<h1>Wow</h1><p>Da ska vi se...</p>',
            'meta_title' => 'Meta titel',
            'meta_description' => 'En forklaring av sidan',
            'meta_og_image' => 'https://placehold.it/40',
            'boxes' => [
                ['header' => 'DK fosta titeln!', 'url' => 'https://google.com'],
                ['header' => 'DK andra titeln!', 'url' => 'https://bog.com'],
            ],
        ], 'dk');

        // Act
        $response = $this->json('POST', "/pages/{$page->id}/publish");

        // Assert
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'revision' => 2,
            'published_version' => 1,
        ]);

        $this->assertDatabaseHas('search_terms', [
            'model_id' => $page->id,
            'model_type' => config('fabriq.models.page'),
            'locale' => 'dk',
            'path' => '/en-siee-saom-skau-paublisers',
            'search_string' => 'En siee saom skau paublisers DK fosta titeln! DK andra titeln!',
        ]);

        $this->assertDatabaseHas('search_terms', [
            'model_id' => $page->id,
            'model_type' => config('fabriq.models.page'),
            'locale' => 'en',
            'path' => '/the-page-title-for-the-page',
            'search_string' => 'The page title for the page EN Box 1 title! EN Box 2 title! EN Box 3 title!',
        ]);

        $this->assertDatabaseCount('search_terms', 3);
    }
}
