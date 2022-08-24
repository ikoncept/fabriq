<?php

namespace Ikoncept\Fabriq\Tests\Feature;

use Ikoncept\Fabriq\Models\User;
use Ikoncept\Fabriq\Notifications\AskToLeaveNotification;
use Ikoncept\Fabriq\Notifications\LeaveDeclinedNotification;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Illuminate\Support\Facades\Notification;

class AskToLeaveFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_send_a_request_to_leave_the_room_to_another_user()
    {
        // Arrange
        Notification::fake(AskToLeaveNotification::class);
        $otherUser = User::factory()->create();

        // Act
        $response = $this->json('POST', route('notifications.ask-to-leave', [$otherUser->id]), [
            'path' => '/pages/7/edit',
        ]);

        // Assert
        $response->assertOk();
        Notification::assertSentTo($otherUser, AskToLeaveNotification::class, function ($notifiable) {
            return $notifiable->pageIdentifier === '/pages/7/edit'
                && $notifiable->causer->email === $this->user->email;
        });
    }

    /** @test **/
    public function it_can_decline_a_ask_to_leave_request()
    {
        // Arrange
        Notification::fake(LeaveDeclinedNotification::class);
        $otherUser = User::factory()->create();

        // Act
        $response = $this->json('POST', route('notifications.decline-to-leave', [$otherUser->id]), [
            'path' => '/pages/7/edit',
        ]);

        // Assert
        $response->assertOk();
        Notification::assertSentTo($otherUser, LeaveDeclinedNotification::class, function ($notifiable) {
            return $notifiable->pageIdentifier === '/pages/7/edit'
                && $notifiable->causer->email === $this->user->email;
        });
    }
}
