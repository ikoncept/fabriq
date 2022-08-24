<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Illuminate\Support\Facades\Storage;

class ImageFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_get_a_single_image()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create();

        // Act
        $response = $this->json('GET', '/images/'.$image->id);

        // Assert
        $response->assertOk();
    }

    /** @test **/
    public function it_can_associate_an_image_with_a_model()
    {
        // Arrange
        $product = \Ikoncept\Fabriq\Models\Contact::factory()->create();
        $otherImage = \Ikoncept\Fabriq\Models\Image::factory()->create();
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create();

        // Act
        $response = $this->json('POST', '/images/'.$image->id.'/contacts', [
            'model_id' => $product->id,
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('imageables', [
            'imageable_id' => $product->id,
            'image_id' => $image->id,
        ]);
    }

    /** @test **/
    public function it_will_return_an_error_if_the_relation_model_class_was_not_found()
    {
        // Arrange
        // $this->withoutExceptionHandling();
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create();

        // Act
        $response = $this->json('POST', '/images/'.$image->id.'/whatever', [
            'model_id' => 912839,
        ]);

        // Assert
        $response->assertStatus(500);
    }

    /** @test **/
    public function it_will_return_an_error_if_no_relation_was_found()
    {
        // Arrange
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create();

        // Act
        $response = $this->json('POST', '/images/'.$image->id.'/users', [
            'model_id' => $this->user->id,
        ]);

        // Assert
        $response->assertStatus(400);
    }

    /** @test **/
    public function it_can_get_related_images_for_a_model()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $oherImage = \Ikoncept\Fabriq\Models\Image::factory()->create();
        $product = \Ikoncept\Fabriq\Models\Contact::factory()
            ->hasImages(3)
            ->create();

        // Act
        $response = $this->json('GET', '/contacts/'.$product->id.'/images');

        // Assert
        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /** @test **/
    public function it_can_get_the_morph_to_relation()
    {
        // Arrange
        $this->markTestSkipped();
        $contact = \Ikoncept\Fabriq\Models\Contact::factory()->create();
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create();
        $contact->images()->attach($image);

        // Act
        $result = $image->imageable()->get();

        // Assert
    }

    /** @test **/
    public function it_can_get_an_index_of_images()
    {
        // Arrange
        $images = \Ikoncept\Fabriq\Models\Image::factory()->count(5)->create();

        // Act
        $response = $this->json('GET', '/images');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    /** @test **/
    public function it_can_get_the_count_of_all_images()
    {
        // Arrange
        $images = \Ikoncept\Fabriq\Models\Image::factory()->count(5)->create();

        // Act
        $response = $this->json('GET', '/images/count');

        // Assert
        $response->assertJsonFragment([
            'count' => 5,
        ]);
    }

    /** @test **/
    public function it_will_get_only_the_correct_image_collection()
    {
        // Arrange
        \Ikoncept\Fabriq\Models\Image::factory()->count(2)->create();
        $images = \Ikoncept\Fabriq\Models\Image::factory()->count(2)->make();
        $images->each(function (\Ikoncept\Fabriq\Models\Image $image) {
            $image->addMediaFromString('A nice media')
                ->toMediaCollection('profile_image');
            $image->save();
        });

        // Act
        $response = $this->json('GET', '/images');

        // Assert
        $response->assertJsonCount(2, 'data');
    }

    /** @test **/
    public function it_can_sort_an_index_of_images()
    {
        // Arrange
        $images = \Ikoncept\Fabriq\Models\Image::factory()->count(5)->create();

        // Act
        $response = $this->json('GET', '/images?sort=-id');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $this->assertEquals(collect($response->json()['data'])->last()['id'], $images->first()->id);
        $this->assertEquals(collect($response->json()['data'])->first()['id'], $images->last()->id);
    }

    /** @test **/
    public function it_can_search_for_an_image()
    {
        // Arrange
        $images = \Ikoncept\Fabriq\Models\Image::factory()->count(5)->create();

        // Act
        $response = $this->json('GET', '/images?filter[search]='.$images->first()->media->first()->name);

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'file_name' => $images->first()->media->first()->file_name,
        ]);
    }

    /** @test **/
    public function it_can_sort_on_a_media_field()
    {
        // Arrange
        $images = \Ikoncept\Fabriq\Models\Image::factory()->count(2)->create();

        // Act
        $response = $this->json('GET', '/images?sort=-file_name');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }

    /** @test **/
    public function it_can_update_an_image()
    {
        // Arrange
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create();

        // Act
        $response = $this->json('PATCH', '/images/'.$image->id, [
            'name' => 'Nytt bildnamn',
            'alt_text' => 'Beskriver bilden',
            'caption' => 'Fotograf: Arp',
            'custom_crop' => false,
            'x_position' => '50%',
            'y_position' => '50%',
        ]);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'c_name' => 'Nytt bildnamn.txt',
        ]);
        $this->assertDatabaseHas('images', [
            'alt_text' => 'Beskriver bilden',
            'caption' => 'Fotograf: Arp',
        ]);
        $this->assertDatabaseHas('media', [
            'name' => 'Nytt bildnamn',
        ]);
    }

    /** @test **/
    public function it_can_delete_an_image()
    {
        // Arrange
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create();

        // Act
        $response = $this->json('DELETE', '/images/'.$image->id);

        // Assert
        $response->assertOk();
        $this->assertDatabaseMissing('images', [
            'id' => $image->id,
        ]);
        $this->assertDatabaseMissing('media', [
            'model_id' => $image->id,
            'model_type' => 'Ikoncept\Fabriq\Models\Image',
        ]);
        // $test = Storage::disk('public')->exists('image1.jpg');
        $test = Storage::disk('__test')->allFiles();
        $this->assertCount(0, $test);
    }

    /** @test **/
    public function it_can_attach_tags_to_an_image()
    {
        // Arrange
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create();
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('PATCH', '/images/'.$image->id, [
            'name' => 'Wow',
            'custom_crop' => true,
            'x_position' => '40%',
            'y_position' => '100%',
            'tags' => [
                'Logo', 'Administration',
            ],
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('tags', [
            'type' => 'images',
        ]);
    }
}
