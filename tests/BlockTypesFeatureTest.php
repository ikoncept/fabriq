<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\WithFaker;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;

class BlockTypesFeatureTest extends AdminUserTestCase
{


    /** @test **/
    public function it_can_get_all_active_block_types()
    {
        // Arrange
        $blockTypes = \Ikoncept\Fabriq\Models\BlockType::factory()->count(2)->create();
        $blockTypes = \Ikoncept\Fabriq\Models\BlockType::factory()->count(2)->create([
            'active' => true
        ]);

        // Act
        $response = $this->json('GET', '/block-types');

        // Assert
        $response->assertOk();
    }
}
