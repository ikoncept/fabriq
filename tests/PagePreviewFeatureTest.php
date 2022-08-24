<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;

class PagePreviewFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_get_a_preview_with_a_signed_link()
    {
        // Arrange
        $template = RevisionTemplate::factory()->create([
            'name' => 'Sidmall',
        ]);
        $titleField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Titel',
            'key' => 'page_title',
            'group' => 'main_content',
            'options' => ['classes' => 'hidden'],
            'type' => 'text',
            'sort_index' => 40,
            'translated' => true,
        ]);
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'Den första startsidan',
            'template_id' => $template->id,
            'revision' => 1,
        ]);
        $page->updateContent([
            'page_title' => 'the unborn title',
        ]);
        $page->publish(1);

        $page->updateContent([
            'page_title' => 'the first title',
        ]);

        $signedUrl = $this->json('GET', '/pages/'.$page->id.'/signed-url');

        // Act
        $response = $this->json('GET', url($signedUrl->json()['signed_url']));

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Den första startsidan',
        ]);
        $response->assertJsonFragment([
            'page_title' => 'the first title',
        ]);
    }
}
