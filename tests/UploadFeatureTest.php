<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Illuminate\Http\UploadedFile;

class UploadFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_upload_an_image_and_persist_it()
    {
        // Arrange

        // Act
        $response = $this->json('POST', '/uploads/images', [
            'image' => UploadedFile::fake()->image('new-image.png', 230, 120)->size(300),
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('media', [
            'file_name' => 'new-image.png',
            'model_type' => 'fabriq_image',
        ]);
    }

    /** @test **/
    public function it_can_upload_a_file_and_persist_it()
    {
        // Arrange

        // Act
        $response = $this->json('POST', '/uploads/files', [
            'file' => UploadedFile::fake()->create('document.txt', 240),
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('media', [
            'file_name' => 'document.txt',
            'model_type' => 'fabriq_file',
        ]);
    }

    /** @test **/
    public function it_can_upload_a_video_and_persist_it()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $fixturePath = __DIR__.'/_fixtures/';
        copy($fixturePath.'fixture-video.mov', $fixturePath.'video.mov');

        // Act
        $response = $this->json('POST', '/uploads/videos', [
            'video' => new UploadedFile($fixturePath.'video.mov', 'video.mov'),
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('media', [
            'file_name' => 'video.mov',
            'model_type' => 'fabriq_video',
        ]);
    }
}
