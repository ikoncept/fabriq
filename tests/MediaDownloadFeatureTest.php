<?php

namespace Ikoncept\Fabriq\Tests;

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Illuminate\Http\UploadedFile;

class MediaDownloadFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_download_a_single_media_file()
    {
        // Arrange
        $response = $this->json('POST', '/uploads/images', [
            'image' => UploadedFile::fake()->image('new-image.png', 230, 120)->size(300),
        ]);

        // Act
        $response = $this->json('GET', 'media/downloads/'.$response->json('uuid'));

        // Assert
        $response->assertOk();
        $response->assertHeader('Content-Disposition');
        $response->assertHeader('Content-Type', 'image/png');
    }
}
