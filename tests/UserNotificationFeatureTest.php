<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;

class UserNotificationFeatureTest extends AdminUserTestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_can_show_a_users_notifications()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();
        $comment = $page->commentAs($user, '<p>This is my special comment! <span data-mention="" class="mention" data-email="roger@pontare.se">@Roger Pontare</span> <span data-mention="" class="mention" data-email="'.$user->email.'">@'.$user->name.'</span><p>');

        // Act
        $response = $this->actingAs($user)->json('GET', '/user/notifications?include=notifiable');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'commentable_type' => 'Ikoncept\Fabriq\Models\Page',
            'comment' => $comment->comment,
        ]);
        $this->assertDatabaseHas('comments', [
            'comment' => $comment->comment,
            'user_id' => $user->id,
        ]);
    }

    /** @test **/
    public function it_can_clear_a_notification()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();
        $comment = $page->commentAs($user, '<p>This is my special comment! <span data-mention="" class="mention" data-email="roger@pontare.se">@Roger Pontare</span> <span data-mention="" class="mention" data-email="'.$user->email.'">@'.$user->name.'</span><p>');

        // Act
        $response = $this->actingAs($user)->json('PATCH', '/user/notifications/' . 1, [
            'clear' => true
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseMissing('notifications', [
            'id' => 1,
            'cleared_at' => null
        ]);
    }
}
