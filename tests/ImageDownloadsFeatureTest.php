<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;

class ImageDownloadsFeatureTest extends AdminUserTestCase
{
    use RefreshDatabase;


    /** @test **/
    public function it_can_download_a_bunch_of_files_as_a_zip()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $images = \Ikoncept\Fabriq\Models\Image::factory()->count(5)->create();

        // Act
        $response = $this->json('GET', '/downloads', ['type' => 'images', 'items' => $images->pluck('id')->toArray()]);

        // Assert
        $response->assertOk();
    }

    /** @test **/
    public function it_can_download_a_single_file()
    {
        // Arrange
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create();

        // Act
        $response = $this->json('GET', '/downloads/' . $image->id, ['type' => 'images']);

        // Assert
        $response->assertOk();
    }
}
