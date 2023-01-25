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
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'options',
                ],
            ],
        ]);
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

    /** @test **/
    public function it_can_update_a_block()
    {
        $blockType = \Ikoncept\Fabriq\Models\BlockType::factory()->create([
            'name' => 'BBBlock',
            'active' => true,
        ]);

        // Act
        $response = $this->json('PATCH', route('block-types.update', $blockType->id), [
            'name' => 'Change my mind!',
            'component_name' => 'Whatever',
            'base_64_svg' => 'PHN',
            'has_children' => true,
            'options' => [
                'visible_for' => [
                    'one', 'two',
                ],
                'recommended_for' => [
                    'ay',
                ],
            ],
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('block_types', [
            'name' => 'Change my mind!',
            'base_64_svg' => 'PHN',
            'has_children' => true,
            'options->visible_for' => json_encode(['one', 'two']),
            'options->recommended_for' => json_encode(['ay']),
        ]);
    }

    /** @test **/
    public function it_can_delete_a_block_type()
    {
        // Arrange
        $blockType = \Ikoncept\Fabriq\Models\BlockType::factory()->create([
            'name' => 'BBBlock',
            'active' => true,
        ]);

        // Act
        $response = $this->json('DELETE', route('block-types.destroy', $blockType->id));

        // Assert
        $response->assertOk();
        $this->assertDatabaseMissing('block_types', [
            'id' => $blockType->id,
        ]);
    }

    /** @test **/
    public function it_can_store_a_new_block_type()
    {
        // Arrange

        // Act
        $response = $this->json('POST', route('block-types.store'), [
            'name' => 'Kontaktformulär',
            'component_name' => 'ContactFormBlock',
            'has_children' => true,
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('block_types', [
            'name' => 'Kontaktformulär',
            'component_name' => 'ContactFormBlock',
            'type' => 'block',
            'has_children' => true,
        ]);
    }
}
