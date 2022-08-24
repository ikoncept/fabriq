<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;

class VideoFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_get_a_single_video()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $video = \Ikoncept\Fabriq\Models\Video::factory()->create();

        // Act
        $response = $this->json('GET', '/videos/'.$video->id);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'id' => $video->id,
            'caption' => $video->caption,
            'alt_text' => $video->alt_text,
        ]);
    }

    /** @test **/
    public function it_can_get_an_index_of_videos()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $videos = \Ikoncept\Fabriq\Models\Video::factory()->count(5)->create();

        // Act
        $response = $this->json('GET', '/videos');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    /** @test **/
    public function it_can_have_tags()
    {
        // Arrange
        $video = \Ikoncept\Fabriq\Models\Video::factory()->create();
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('PATCH', '/videos/'.$video->id, [
            'name' => 'Wow',
            'tags' => [
                'Logo', 'Administration',
            ],
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('tags', [
            'type' => 'videos',
        ]);
    }

    /** @test **/
    public function it_can_update_a_video()
    {
        // Arrange
        $video = \Ikoncept\Fabriq\Models\Video::factory()->create();
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('PATCH', '/videos/'.$video->id, [
            'name' => 'Fet video',
            'alt_text' => 'Det är en fet video',
            'caption' => 'Filmad av Jorge',
            'tags' => [],
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
            'alt_text' => 'Det är en fet video',
            'caption' => 'Filmad av Jorge',
        ]);
        $this->assertDatabaseHas('media', [
            'name' => 'Fet video',
        ]);
    }

    /** @test **/
    public function it_can_search_for_a_video()
    {
        // Arrange
        $videos = \Ikoncept\Fabriq\Models\Video::factory()->count(5)->create();

        // Act
        $response = $this->json('GET', '/videos?filter[search]='.$videos->first()->media->first()->name);

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'file_name' => $videos->first()->media->first()->file_name,
        ]);
    }

    /** @test **/
    public function it_can_delete_a_video()
    {
        // Arrange
        $video = \Ikoncept\Fabriq\Models\Video::factory()->create();

        // Act/Assert
        $this->json('DELETE', '/videos/'.$video->id)
            ->assertOk();

        // Assert
        $this->assertDatabaseMissing('videos', [
            'id' => $video->id,
        ]);
        $this->assertDatabaseMissing('media', [
            'model_id' => $video->id,
            'model_type' => 'Ikoncept\Fabriq\Models\Video',
        ]);
    }

    /** @test **/
    public function it_can_sort_an_index_of_videos()
    {
        // Arrange
        $videos = \Ikoncept\Fabriq\Models\Video::factory()->count(5)->create();

        // Act
        $response = $this->json('GET', '/videos?sort=-id');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $this->assertEquals(collect($response->json()['data'])->last()['id'], $videos->first()->id);
        $this->assertEquals(collect($response->json()['data'])->first()['id'], $videos->last()->id);
    }

    /** @test **/
    public function it_can_sort_on_a_media_field()
    {
        // Arrange
        $videos = \Ikoncept\Fabriq\Models\Video::factory()->count(2)->create();

        // Act
        $response = $this->json('GET', '/videos?sort=-file_name');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    /** @test **/
    public function it_will_return_all_if_the_search_is_empty()
    {
        // Arrange
        $videos = \Ikoncept\Fabriq\Models\Video::factory()->count(2)->create();

        // Act
        $response = $this->json('GET', '/videos?filter[search]');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }
}
