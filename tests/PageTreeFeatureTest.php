<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\WithFaker;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;

class PageTreeFeatureTest extends AdminUserTestCase
{


    public function setUp() : void
    {
        parent::setUp();
        \Ikoncept\Fabriq\Models\Page::factory()->create(['name' => 'root']);
    }

    /** @test **/
    public function it_can_get_a_tree_representation_of_the_pages()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'parent_id' => 1
        ]);
        $subpage = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'subpage',
            'parent_id' => $page->id
        ]);

        // Act
        $response = $this->json('GET', '/pages-tree');

        // Assert
        $response->assertOk();
        // dd($response->json());
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'parent_id' => $subpage->parent_id,
            'id' => $subpage->id
        ]);
    }

    /** @test **/
    public function it_can_update_the_page_tree()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'parent_id' => 1
        ]);
        $subpage = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'subpage',
            'parent_id' => 1
        ]);


        // Act
        $response = $this->json('PATCH', '/pages-tree', [
            'tree' => [
                [
                    'id' => $page->id,
                    'name' => $page->name,
                    'children' => [
                        0 => [
                            'id' => $subpage->id,
                            'name' => $subpage->name,
                            'sortindex' => 10
                        ]
                    ]
                ]
            ]
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('pages', [
            'id' => $subpage->id,
            'name' => $subpage->name,
            'sortindex' => 10,
            'parent_id' => $page->id
        ]);
    }
}
