<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\I18nDefinition;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Illuminate\Support\Facades\Event;
use Infab\TranslatableRevisions\Events\TranslatedRevisionUpdated;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;

class MenuItemFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_get_a_tree_representation_of_the_menu_items()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create();
        $root = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
        ]);
        $first = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'sortindex' => 10,
            'parent_id' => $root->id,
        ]);
        $second = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $first->id,
        ]);
        $third = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $second->id,
        ]);
        $standAlone = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => null,
            'sortindex' => 20,
            'parent_id' => $root->id,
        ]);

        // Act
        $response = $this->json('GET', '/menus/'.$menu->id.'/items/tree');

        // Assert
        $response->assertOk();

        //Expect 2 roots
        $response->assertJsonCount(2, 'data');
    }

    /** @test **/
    public function it_can_update_a_tree()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create();
        $root = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
        ]);
        $first = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'sortindex' => 10,
            'parent_id' => $root->id,
        ]);
        $second = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $first->id,
        ]);

        $treeData = \Ikoncept\Fabriq\Models\MenuItem::descendantsOf($root->id)->toTree();

        // Act
        $response = $this->json('PATCH', '/menus/'.$menu->id.'/items/tree', [
            'tree' => $treeData->toArray(),
        ]);

        // Assert
        $response->assertOk();
    }

    /** @test **/
    public function it_can_update_a_single_menu_item()
    {
        // Arrange
        Event::fake(TranslatedRevisionUpdated::class);

        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create();
        $root = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
        ]);
        $first = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'sortindex' => 10,
            'parent_id' => $root->id,
        ]);

        // Act
        $response = $this->json('PATCH', '/menu-items/'.$first->id, [
            'item' => [
                'type' => 'internal',
                'page_id' => $page->id,
            ],
            'content' => [],
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('menu_items', [
            'page_id' => $page->id,
            'type' => 'internal',
        ]);
        Event::assertDispatchedTimes(TranslatedRevisionUpdated::class, 2);
        Event::assertDispatched(function (TranslatedRevisionUpdated $event) {
            if (get_class($event->model) === Fabriq::getFqnModel('menuItem')) {
                return $event->model->getRevisionOptions()->cacheTagsToFlush[0] === 'cms_menu';
            }
        });
    }

    /** @test **/
    public function it_can_get_single_menu_item()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create();
        $root = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
        ]);
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $first = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'sortindex' => 10,
            'parent_id' => $root->id,
            'page_id' => $page->id,
        ]);

        // Act
        $response = $this->json('GET', '/menu-items/'.$first->id.'?include=page');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => $page->name,
        ]);
    }

    /** @test **/
    public function it_can_store_a_new_menu_item()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create();
        $root = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
        ]);
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();

        // Act
        $response = $this->json('POST', '/menus/'.$menu->id.'/items', [
            'item' => [
                'page_id' => $page->id,
                'type' => 'internal',
            ],
            'content' => [],
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('menu_items', [
            'page_id' => $page->id,
            'type' => 'internal',
        ]);
    }

    /** @test **/
    public function it_can_create_an_item_with_external_url()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create();
        $root = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
        ]);

        $template = RevisionTemplate::factory()->create([
            'slug' => 'menu-item',
        ]);
        $field = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'key' => 'title',
            'type' => 'text',
            'translated' => false,
        ]);
        $field = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'key' => 'external_url',
            'type' => 'text',
            'translated' => false,
        ]);

        // Act
        $response = $this->json('POST', '/menus/'.$menu->id.'/items', [
            'item' => [
                'page_id' => null,
                'type' => 'external',
            ],
            'content' => [
                'title' => 'Sök på google',
                'external_url' => 'https://google.se',
            ],
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('menu_items', [
            'page_id' => null,
            'type' => 'external',
        ]);
        $this->assertDatabaseHas('revision_meta', [
            'meta_value' => json_encode('Sök på google'),
        ]);
        $this->assertDatabaseHas('revision_meta', [
            'meta_value' => json_encode('https://google.se'),
        ]);
    }

    /** @test **/
    public function it_can_delete_an_item()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create();
        $root = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
        ]);
        $item = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $root->id,
        ]);
        $subItem = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $item->id,
        ]);

        // Act
        $response = $this->json('DELETE', '/menu-items/'.$item->id);

        // Assert
        $response->assertOk();
        $this->assertDatabaseMissing('menu_items', [
            'id' => $item->id,
        ]);
        // The childeren shall also be deleted
        $this->assertDatabaseMissing('menu_items', [
            'id' => $subItem->id,
        ]);
    }

    /** @test **/
    public function it_can_include_localized_content()
    {
        // Arrange
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create();
        $root = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
        ]);

        $template = RevisionTemplate::factory()->create([
            'slug' => 'menu-item',
        ]);
        $field = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'key' => 'title',
            'type' => 'text',
            'translated' => true,
        ]);
        $field = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'key' => 'external_url',
            'type' => 'text',
            'translated' => true,
        ]);
        $menuItem = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu,
            'type' => 'external',
        ]);
        $menuItem->updateContent([
            'title' => 'Hello',
        ], 'en');
        $menuItem->updateContent([
            'title' => 'Hej',
        ], 'sv');

        // Act
        $response = $this->json('GET', '/menu-items/'.$menuItem->id.'?include=localizedContent');

        //
        $response->assertOk();
        // dd(I18nDefinition::all()->toArray());
        $response->assertJsonFragment([
            'sv' => [
                'content' => [
                    'title' => 'Hej',
                ],
            ],
        ]);
        $response->assertJsonFragment([
            'en' => [
                'content' => [
                    'title' => 'Hello',
                ],
            ],
        ]);
    }

    /** @test **/
    public function it_can_update_a_menu_item()
    {
        // Arrange
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create();
        $root = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
        ]);

        $template = RevisionTemplate::factory()->create([
            'slug' => 'menu-item',
        ]);
        $field = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'key' => 'title',
            'type' => 'text',
            'translated' => false,
        ]);
        $field = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'key' => 'external_url',
            'type' => 'text',
            'translated' => false,
        ]);
        $menuItem = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu,
            'type' => 'external',
        ]);
        $menuItem->updateContent([
            'title' => 'Hello',
        ], 'en');
        $menuItem->updateContent([
            'title' => 'Hej',
        ], 'sv');

        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('PATCH', '/menu-items/'.$menuItem->id.'?include=content', [
            'item' => [
                'type' => 'external',
            ],
            'content' => [
                'title' => 'En extern',
                'external_url' => 'https://google.se',
            ],
        ]);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'content' => [
                'data' => [
                    'title' => 'En extern',
                    'external_url' => 'https://google.se',
                ],
            ],
        ]);
    }

    /** @test **/
    public function it_will_prepend_a_slash_to_the_relative_url()
    {
        // Arrange
        $menu = \Ikoncept\Fabriq\Models\Menu::factory()->create(['slug' => 'main_menu']);
        $root = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
        ]);
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'template_id' => 3,
        ]);

        RevisionTemplateField::factory()->create([
            'key' => 'page_title',
            'translated' => true,
            'template_id' => 3,
        ]);
        $page->updateContent(['page_title' => 'The root page'], 'en');
        $subPage = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'template_id' => 3,
            'parent_id' => $page->id,
        ]);
        $subPage->updateContent(['page_title' => 'The child page'], 'en');
        $menuItem = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $root->id,
            'page_id' => $page->id,
        ]);
        $subMenuItem = \Ikoncept\Fabriq\Models\MenuItem::factory()->create([
            'menu_id' => $menu->id,
            'parent_id' => $menuItem->id,
            'page_id' => $subPage->id,
        ]);

        // Act
        $response = $this->withHeaders(['X-LOCALE' => 'en'])
                ->json('GET', '/menus/'.$menu->slug.'/public/'.'?include=children');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'path' => '/the-root-page/the-child-page',
        ]);
        $response->assertJsonFragment([
            'localized_path' => '/en/the-root-page/the-child-page',
        ]);
    }
}
