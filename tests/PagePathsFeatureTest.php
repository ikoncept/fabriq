<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Models\Menu;
use Ikoncept\Fabriq\Models\MenuItem;
use Ikoncept\Fabriq\Models\Page;

use Illuminate\Foundation\Testing\WithFaker;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class PagePathsFeatureTest extends AdminUserTestCase
{

    /** @test **/
    public function it_can_get_page_paths()
    {
        // Arrange
        $menu = Menu::factory()->create();
        $parent = Page::factory()->create([
            'name' => 'parent 1'
        ]);
        $parentItem = MenuItem::create([
            'page_id' => $parent->id,
            'menu_id' => $menu->id
        ]);
        $childOne = Page::factory()->create([
            'name' => 'child 2'
        ]);
        $subItem = MenuItem::create([
            'page_id' => $childOne->id,
            'menu_id' => $menu->id,
            'parent_id' => $parentItem->id
        ]);
        $childChildOne = Page::factory()->create([
            'name' => 'child 1 of child 1'
        ]);
        MenuItem::create([
            'page_id' => $childChildOne->id,
            'menu_id' => $menu->id,
            'parent_id' => $subItem->id
        ]);

        // $terms = DB::table('i18n_terms')->get();
        // $slugs = DB::table('slugs')->get();
        // dd($terms->toArray(), $slugs->toArray());
        // dd($page->fresh()->getFieldContent($page->revision, 'sv')->toArray());
        // dd($page->toArray());
        // Act
        $response = $this
            ->withHeaders(['X-LOCALE' => 'sv'])
            ->json('GET', route('pages.paths.index', [$childChildOne]));

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'absolute_path' => config('fabriq.front_end_domain') . '/' . App::currentLocale() . '/parent-1/child-2/child-1-of-child-1',
            'permalink' => config('fabriq.front_end_domain') .  '/permalink/' . hash('sha1', $childChildOne->id)
        ]);
    }

    /** @test **/
    public function it_can_figure_out_a_path_given_an_sha_hash()
    {
        // Arrange
        $menu = Menu::factory()->create();
        $parent = Page::factory()->create([
            'name' => 'parent 1'
        ]);
        $parentItem = MenuItem::create([
            'page_id' => $parent->id,
            'menu_id' => $menu->id
        ]);
        $childOne = Page::factory()->create([
            'name' => 'child 2'
        ]);
        $subItem = MenuItem::create([
            'page_id' => $childOne->id,
            'menu_id' => $menu->id,
            'parent_id' => $parentItem->id
        ]);
        $childChildOne = Page::factory()->create([
            'name' => 'child 1 of child 1'
        ]);
        MenuItem::create([
            'page_id' => $childChildOne->id,
            'menu_id' => $menu->id,
            'parent_id' => $subItem->id
        ]);
        App::setLocale('sv');
        $paths = $childChildOne->transformPaths();

        // Act
        $response = $this->get($paths['permalink']);

        // Assert
        $response->assertRedirect(config('fabriq.front_end_domain') . '/' . App::currentLocale() . '/parent-1/child-2/child-1-of-child-1');
    }

}
