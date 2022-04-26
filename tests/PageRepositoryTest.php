<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Page;
use Ikoncept\Fabriq\Models\Slug;
use Ikoncept\Fabriq\Repositories\EloquentPageRepository;

use Illuminate\Foundation\Testing\WithFaker;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;

class PageRepositoryTest extends AdminUserTestCase
{


    /** @test **/
    public function it_can_find_pages_with_ids()
    {
        // Arrange
        $repo = new EloquentPageRepository(Fabriq::getModelClass('slug'));
        Page::factory()->count(2)->create();
        $pages = Page::factory()->count(3)->create();

        // Act
        $result = $repo->findByIds($pages->pluck('id')->toArray());

        // Assert
        $this->assertCount(3, $result->toArray());
    }
}
