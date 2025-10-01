<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Illuminate\Support\Facades\Storage;

class FileFeatureTest extends AdminUserTestCase
{
    protected $endpoint = '/files/';

    /** @test **/
    public function it_can_get_an_index_of_all_files()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $files = \Ikoncept\Fabriq\Models\File::factory()
            ->count(5)
            ->create();

        // Act
        $response = $this->json('GET', $this->endpoint);

        // Assert
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    /** @test **/
    public function it_can_get_a_single_file()
    {
        // Arrange
        $file = \Ikoncept\Fabriq\Models\File::factory()->create();

        // Act
        $response = $this->json('GET', $this->endpoint.$file->id);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'caption' => $file->caption,
            'readable_name' => $file->readable_name,
            'mime_type' => 'text/plain',
            'file_name' => 'text.txt',
        ]);
    }

    /** @test **/
    public function it_can_update_a_file()
    {
        // Arrange
        $file = \Ikoncept\Fabriq\Models\File::factory()->create();
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('PATCH', '/files/'.$file->id, [
            'name' => 'Wow',
            'readable_name' => 'Ett läsbart namn',
            'caption' => 'Okej',
            'tags' => [
                'Logo', 'Administration',
            ],
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('tags', [
            'type' => 'files',
        ]);
    }

    /** @test **/
    public function it_can_delete_a_file()
    {
        // Arrange
        $file = \Ikoncept\Fabriq\Models\File::factory()->create();

        // Act
        $response = $this->json('DELETE', '/files/'.$file->id);

        // Assert
        $response->assertOk();
        $this->assertDatabaseMissing('files', [
            'id' => $file->id,
        ]);
        $this->assertDatabaseMissing('media', [
            'model_id' => $file->id,
            'model_type' => 'fabriq_file',
        ]);
        // $test = Storage::disk('public')->exists('file1.jpg');
        $test = Storage::disk('__test')->allFiles();
        $this->assertCount(0, $test);
    }

    /** @test **/
    public function it_can_search_for_an_file()
    {
        // Arrange
        $files = \Ikoncept\Fabriq\Models\File::factory()->count(5)->create();

        // Act
        $response = $this->json('GET', '/files?filter[search]='.$files->first()->media->first()->name);

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'file_name' => $files->first()->media->first()->file_name,
        ]);
    }

    /** @test **/
    public function it_can_sort_on_a_media_field()
    {
        // Arrange
        $files = \Ikoncept\Fabriq\Models\File::factory()->count(2)->create();

        // Act
        $response = $this->json('GET', '/files?sort=-file_name');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
    }
}
