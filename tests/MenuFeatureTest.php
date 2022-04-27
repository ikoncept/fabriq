<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\WithFaker;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;

class MenuFeatureTest extends AdminUserTestCase
{


    /** @test **/
    public function it_can_get_all_menus()
    {
        // Arrange
        $menus = \Ikoncept\Fabriq\Models\Menu::factory()->count(2)->create();

        // Act
        $response = $this->json('GET', '/menus');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    /** @test **/
    public function it_can_get_a_single_menu()
    {
        // Arrange
        $menus = \Ikoncept\Fabriq\Models\Menu::factory()->count(2)->create();

        // Act
        $response = $this->json('GET', '/menus/' . $menus->first()->id);

       // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => $menus->first()->name,
            'slug' => (string) $menus->first()->slug
        ]);
    }

    /** @test **/
    public function it_can_store_a_new_menu()
    {
        // Arrange
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('POST', '/menus', [
            'name' => 'Ny meny'
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('menus', [
            'name' => 'Ny meny'
        ]);
        $this->assertDatabaseHas('menu_items', [
            '_lft' => 1,
            '_rgt' => 2
        ]);
    }


    /** @test **/
    public function it_can_update_a_menu()
    {
        // Arrange
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create();

        // Act
        $response = $this->json('PATCH', '/menus/' . $menu->id, [
            'name' => 'Nytt namn'
        ]);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('menus', [
            'name' => 'Nytt namn'
        ]);
    }

    /** @test **/
    public function it_can_delete_a_menu()
    {
        // Arrange
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create();

        // Act
        $response = $this->json('DELETE', '/menus/' . $menu->id);


        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseMissing('menus', [
            'id' => $menu->id
        ]);
    }

    /** @test **/
    public function it_can_get_a_public_menu()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create([
            'slug' => 'main_menu',
            'name' => 'Huvudmeny'
        ]);
        $root = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
        ]);
        $field = RevisionTemplateField::factory()->create([
            'template_id' => 2,
            'key' => 'page_title',
            'translated' => true
        ]);
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create(['template_id' => 2]);
        $page->updateContent(['page_title' => 'En titel'], 'en');
        $page->save();

        $page2 = \Ikoncept\Fabriq\Models\Page::factory()->create(['template_id' => 2]);
        $page2->updateContent(['page_title' => 'En annan titel'], 'en');
        $page2->save();
        $first = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'sortindex' => 10,
            'parent_id' => $root->id,
            'type' => 'internal',
            'page_id' => $page->id
        ]);
        $second = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'sortindex' => 10,
            'parent_id' => $first->id,
            'type' => 'internal',
            'page_id' => $page2->id
        ]);

        // Act
        $response = $this->json('GET', '/menus/' . $menu->slug . '/public/' . '?include=children');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'path' => '/en-titel'
        ]);
        $response->assertJsonFragment([
            'path' => '/en-titel/en-annan-titel'
        ]);
    }
}
