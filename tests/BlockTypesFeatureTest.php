<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;

class BlockTypesFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_get_all_active_block_types()
    {
        // Arrange
        $blockTypes = \Ikoncept\Fabriq\Models\BlockType::factory()->count(2)->create();
        $blockTypes = \Ikoncept\Fabriq\Models\BlockType::factory()->count(2)->create([
            'active' => true,
        ]);

        // Act
        $response = $this->json('GET', '/block-types');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    /** @test **/
    public function it_can_sort_active_blocks_by_name()
    {
        $blockType = \Ikoncept\Fabriq\Models\BlockType::factory()->create([
            'name' => 'BBBlock',
            'active' => true,
        ]);
        $blockType = \Ikoncept\Fabriq\Models\BlockType::factory()->create([
            'name' => 'AABlock',
            'active' => true,
        ]);

        // Act
        $response = $this->json('GET', '/block-types?sort=name');

        // Assert
        $response->assertOk();
        $this->assertEquals('AABlock', $response->json()['data'][0]['name']);
    }
}
