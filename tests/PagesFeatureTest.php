<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Models\Page;
use Ikoncept\Fabriq\Database\Seeders\DatabaseSeeder;
use Ikoncept\Fabriq\Database\Seeders\PageTemplateSeeder;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Infab\TranslatableRevisions\Models\I18nTerm;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;

class PagesFeatureTest extends AdminUserTestCase
{



    public function setUp() : void
    {
        parent::setUp();

        app(DatabaseSeeder::class)->call(PageTemplateSeeder::class);
    }

    /** @test **/
    public function it_can_get_a_single_page()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'Den första startsidan',
            'template_id' => RevisionTemplate::all()->first()->id
        ]);

        // Act
        $response = $this->json('GET', '/pages/' . $page->id);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $page->id
        ]);
    }

    /** @test **/
    public function it_can_include_content_for_a_single_page()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'Den första startsidan',
            'template_id' => RevisionTemplate::all()->first()->id,
            'revision' => 1,
            'published_version' => null
        ]);
        $arr = ['page_title' => 'The page title for the page',
        'page_header' => 'The page title for the page',
        'page_content' => '<h1>Wow, a header</h1><p>Ok lets see</p>',
        'meta_title' => 'Meta title',
        'meta_description' => 'Describing the page',
        'meta_og_image' => 'https://placehold.it/40',
        'boxes' => [
            ['title' => 'Box 1 title!', 'url' => 'https://google.com'],
            ['title' => 'Box 2 title!', 'url' => 'https://bog.com'],
            ['title' => 'Box 3 title!', 'url' => 'http://flank.se'],
        ]];
        $page->updateContent([
            'page_title' => 'The page title for the page',
            'page_header' => 'The page title for the page',
            'page_content' => '<h1>Wow, a header</h1><p>Ok lets see</p>',
            'meta_title' => 'Meta title',
            'meta_description' => 'Describing the page',
            'boxes' => [
                ['title' => 'Box 1 title!', 'url' => 'https://google.com'],
                ['title' => 'Box 2 title!', 'url' => 'https://bog.com'],
                ['title' => 'Box 3 title!', 'url' => 'http://flank.se'],
            ]
        ], 'en');

        // Act
        $response = $this->json('GET', '/pages/' . $page->id . '?include=content');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'page_title' => 'The page title for the page',
            'page_header' => 'The page title for the page',
            'page_content' => '<h1>Wow, a header</h1><p>Ok lets see</p>',
            'meta_title' => 'Meta title',
            'meta_description' => 'Describing the page',
        ]);
        $response->assertJsonCount(3, 'data.content.data.boxes');
    }

    /** @test **/
    public function it_can_include_all_template_fields()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'Den första startsidan',
            'template_id' => RevisionTemplate::where('slug', 'startsida')->first()->id,
            'revision' => 1,
            'published_version' => null
        ]);


        // Act
        $response = $this->json('GET', '/pages/' . $page->id . '?include=template.fields');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(8, 'data.template.data.fields.data');
        $response->assertJsonStructure([
            'data' => [
                'template' => [
                    'data' => [
                        'fields' => []
                    ]
                ]
            ]
        ]);
    }

    /** @test **/
    public function it_can_get_all_pages()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'Den första startsidan',
            'template_id' => RevisionTemplate::all()->first()->id
        ]);
        $otherPage = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'Den andra startsidan',
            'template_id' => RevisionTemplate::all()->first()->id
        ]);

        // Act
        $response = $this->json('GET', '/pages');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $page->id
        ]);
        $response->assertJsonFragment([
            'id' => $otherPage->id
        ]);
    }

    /** @test **/
    public function it_can_update_page_content()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'Den första startsidan',
            'template_id' => RevisionTemplate::all()->first()->id
        ]);
        DB::table('slugs')->insert([
            'model_id' => \Ikoncept\Fabriq\Models\Page::factory()->create()->id,
            'model_type' => \Ikoncept\Fabriq\Models\Page::class,
            'slug' => 'en-mycket-bra-titel',
            'locale' => app()->getLocale(),
            'source_string' => 'En mycket bra titel',
            'source_key' => 'page_1_1_page_title'
        ]);

        // Act
        $response = $this->json('PATCH', '/pages/' . $page->id . '?include=content', [
            'name' => 'Same as before',
            'localizedContent' => [
                'sv' => [
                    'page_title' => 'En mycket bra titel',
                    'boxes' => [['title' => 'One box']]
                ],
                'en' => [
                    'page_title' => 'English title',
                    'page_header' => 'The new header',
                    'page_content' => '<p>sweet</p>',
                    'boxes' => [['title' => 'One box']]
                ]
            ]
        ]);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'page_title' => 'English title',
            'page_header' => 'The new header',
            'page_content' => '<p>sweet</p>',
            'boxes' => [['title' => 'One box']]
        ]);
        $this->assertDatabaseHas('slugs', [
            'model_id' => $page->id,
            'model_type' => \Ikoncept\Fabriq\Models\Page::class,
            'locale' => 'sv',
            'slug' => 'en-mycket-bra-titel-1'
        ]);
        $this->assertDatabaseHas('i18n_definitions', [
            'locale' => 'en',
            'content' => json_encode('English title')
        ]);
        $this->assertDatabaseHas('i18n_definitions', [
            'locale' => 'sv',
            'content' => json_encode('En mycket bra titel')
        ]);

    }

    /** @test **/
    public function it_can_store_a_new_a_page()
    {
        // Act
        $root = \Ikoncept\Fabriq\Models\Page::factory()->create(['name' => 'root']);
        $response = $this->json('POST', '/pages', [
            'name' => 'Ny sida',
            'template_id' => 1,
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('pages', [
            'name' => 'Ny sida',
            'template_id' => 1,
            'parent_id' => $root->id
        ]);
    }

    /** @test **/
    public function it_can_delete_a_page()
    {
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'Den första startsidan',
            'template_id' => RevisionTemplate::all()->first()->id
        ]);

        // Act
        $response = $this->json('delete', '/pages/' . $page->id);


        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseMissing('pages', [
            'id' => $page->id,
        ]);
    }

    /** @test **/
    public function it_can_get_a_page_via_slug()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $page->updateContent([
            'page_title' => 'The page title for the page',
        ], $page->revision, 'sv');
        $otherPage = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $otherPage->updateContent([
            'page_title' => 'The page title for the page',
        ], $page->revision, 'sv');


        // Act
        $foundPage = Page::whereSlug('the-page-title-for-the-page')->first();

        // Assert
        $this->assertEquals($page->id, $foundPage->id);
    }

    /** @test **/
    public function it_can_include_slugs()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'Den första startsidan',
            'template_id' => RevisionTemplate::all()->first()->id,
            'revision' => 1,
            'published_version' => null
        ]);
        $page->updateContent([
            'page_title' => 'The page title for the page',
        ], 'sv');


        // Act
        $response = $this->json('GET', '/pages/' . $page->id . '?include=slugs');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'slug' => 'the-page-title-for-the-page',
            'source_key' => 'pages-' . $page->id . '-1-page_title'
        ]);
    }

    public function testIncludeGroupedFields()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'Den första startsidan',
            'template_id' => RevisionTemplate::where('slug', 'startsida')->first()->id,
            'revision' => 1,
            'published_version' => null
        ]);

        // Act
        $response = $this->json('GET', '/pages/' . $page->id . '?include=template.groupedFields');

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'template' => [
                    'data' => [
                        'groupedFields' => [
                            'data' => [
                                'meta' => [],
                                'main_content' => []
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }

    /** @test **/
    public function it_can_publish_a_page()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'En sida som ska publiceras',
            'template_id' => RevisionTemplate::all()->first()->id,
            'revision' => 1
        ]);
        $page->updateContent([
            'page_title' => 'The page title for the page',
            'page_header' => 'The page title for the page',
            'page_content' => '<h1>Wow, a header</h1><p>Ok lets see</p>',
            'meta_title' => 'Meta title',
            'meta_description' => 'Describing the page',
            'meta_og_image' => 'https://placehold.it/40',
            'boxes' => [
                ['title' => 'Box 1 title!', 'url' => 'https://google.com'],
                ['title' => 'Box 2 title!', 'url' => 'https://bog.com'],
                ['title' => 'Box 3 title!', 'url' => 'http://flank.se'],
            ]
        ]);

        // Act
        $response = $this->json('POST', "/pages/{$page->id}/publish");

        // Assert
        $this->markTestIncomplete('Not sure why this is not passing');
        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'revision' => 2,
            'published_version' => 1
        ]);
        $this->assertDatabaseHas('i18n_terms', [
            'key' => 'pages_1_2_page_title'
        ]);
    }

    /** @test **/
    public function it_can_search_for_a_page()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()
            ->create([
                'name' => 'Landningssida',
            ]);
        $otherPage = \Ikoncept\Fabriq\Models\Page::factory()
            ->create([
                'name' => 'Fooll',
            ]);
        $template = RevisionTemplate::factory()->create([
            'name' => 'Landningssida'
        ]);
        $page = \Ikoncept\Fabriq\Models\Page::factory()
            ->create([
                'name' => 'Första sidan',
                'template_id' => $template->id
            ]);

        // Act
        $response = $this->json('GET', '/pages?filter[search]=landningssida');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    /** @test **/
    public function it_can_include_content_for_all_enabled_locales()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'template_id' => RevisionTemplate::all()->first()->id,
            'revision' => 1
        ]);
        $page->updateContent([
            'page_title' => 'Svensk titel',
        ], 'sv');
        $page->updateContent([
            'page_title' => 'English title',
        ], 'en');

        // Act
        $response = $this->json('GET', '/pages/' . $page->id . '?include=localizedContent');

        // Assert
        $response->assertOk();
        $response->assertJsonPath('data.localizedContent.data.en.content.page_title', 'English title');
        $response->assertJsonPath('data.localizedContent.data.sv.content.page_title', 'Svensk titel');
    }

    /** @test **/
    public function it_can_have_a_tree_structure()
    {
        // Arrange
        $parentPage = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $page->parent_id = $parentPage->id;
        $page->save();

        // Act
        $response = $this->json('GET', '/pages?include=children');

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                0  => [
                    'children' => [
                        'data' => []
                    ]
                ]
            ]
        ]);
    }
}
