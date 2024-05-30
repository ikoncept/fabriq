<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Infab\TranslatableRevisions\Models\RevisionTemplate;

class RevisionTemplateFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_get_all_templates()
    {
        // Arrange
        RevisionTemplate::all()->each(function ($item) {
            $item->delete();
        });
        // RevisionTemplate::truncate();
        $templates = RevisionTemplate::factory()->count(3)->create([
            'locked' => true,
        ]);

        // Act
        $response = $this->json('GET', '/templates');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'locked',
                    'source_model_id',
                ],
            ],
        ]);
    }
}
