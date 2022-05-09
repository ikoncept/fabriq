<?php


namespace Ikoncept\Fabriq\Tests;

use Ikoncept\Fabriq\Mail\AccountInvitation;
use Ikoncept\Fabriq\Models\Image;
use Ikoncept\Fabriq\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserInvitationsFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_will_allow_an_admin_to_create_and_send_an_invitation_to_a_user()
    {
        // Arrange
        // Mail::fake(AccountInvitation::class);
        $this->user->assignRole('admin');
        $otherUser = User::factory()->create();

        // Act
        $response = $this->json('POST', route('invitations.store', [$otherUser->id]));

        // Assert
        $response->assertStatus(201);
        Mail::assertQueued(AccountInvitation::class);
    }

    /** @test **/
    public function it_will_delete_old_invitations_when_a_new_one_is_created()
    {
        // Arrange
        Mail::fake(AccountInvitation::class);
        $this->user->assignRole('admin');
        $otherUser = User::factory()->create();
        $res = $otherUser->createInvitation();

        // Act
        $response = $this->json('POST', route('invitations.store', [$otherUser->id]));

        // Assert
        $response->assertStatus(201);
        Mail::assertQueued(AccountInvitation::class);
        $this->assertDatabaseCount('invitations', 1);
    }

    /** @test **/
    public function it_can_delete_an_invitation()
    {
        // Arrange
        $this->user->assignRole('admin');
        $otherUser = User::factory()->create();
        $res = $otherUser->createInvitation();

        // Act
        $response = $this->json('DELETE', route('invitations.destroy', [$otherUser->id]));

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseCount('invitations', 0);
    }

    /** @test **/
    public function it_will_allow_a_user_to_accept_the_invitation()
    {
        // Arrange
        $this->user->assignRole('admin');
        $otherUser = User::factory()->create([
                 'email_verified_at' => null
            ]);

        $invitation = $otherUser->createInvitation();

        // Act
        $response = $this->json('POST', route('invitation.accept.store', [$invitation->uuid]), [
                'password' => 'my-new_password',
                'password_confirmation' => 'my-new_password',
            ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'id' => $invitation->user_id,
            'email_verified_at' => now()
        ]);
        $this->assertDatabaseMissing('invitations', [
            'user_id' => $invitation->user_id,
        ]);
    }
}
