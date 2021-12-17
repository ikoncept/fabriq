<?php

namespace Ikoncept\Fabriq\Tests;

use Ikoncept\Fabriq\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class UserImageFeatureTest extends AdminUserTestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_can_upload_and_attach_image_to_an_user()
    {
        $user = Auth::user();
        $route = route('user.image.store');

        $response = $this->json('POST', $route, [
            'image' => UploadedFile::fake()->image('new-image.png', 230, 120)->size(300)
        ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas('users', [
            'id'        => $user->id,
            'image_id'  => 1
        ]);
        $this->assertDatabaseHas('images', [
            'id' => 1,
        ]);
    }

    /** @test **/
    public function it_can_delete_and_detach_an_users_image()
    {
        // Arrange
        $user = Auth::user();
        $image = Image::factory()->create();
        $user->image_id = $image->id;
        $user->save();

        $route = route('user.image.destroy');
        $response = $this->json('DELETE', $route);
        $response->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'id'        => $user->id,
            'image_id'  => null
        ]);

        $this->assertDatabaseMissing('images', [
            'id' => $image->id,
        ]);
    }
}
