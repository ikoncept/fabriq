<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\WithFaker;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;

class RevisionTemplateFeatureTest extends AdminUserTestCase
{


    /** @test **/
    public function it_can_get_all_templates()
    {
        // Arrange
        // RevisionTemplate::truncate();
        $templates = RevisionTemplate::factory()->count(3)->create();

        // Act
        $response = $this->json('GET', '/templates');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }
}
