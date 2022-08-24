<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Events\CommentDeleted;
use Ikoncept\Fabriq\Events\CommentPosted;
use Ikoncept\Fabriq\Events\NotificationDeleted;
use Ikoncept\Fabriq\Events\UserMentionedInComment;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Illuminate\Support\Facades\Event;

class CommentableFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_attach_commentable_behaviour_to_a_model()
    {
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $this->assertTrue(
            method_exists($page, 'comments'),
            'Class does not have method comments'
        );
    }

    /** @test **/
    public function it_can_attach_a_comment_to_a_commentable_model()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();
        $page->commentAs($user, 'This is my special comment!');

        $this->assertDatabaseHas('comments', [
            'comment' => 'This is my special comment!',
            'user_id' => $user->id,
        ]);
    }

    /** @test **/
    public function it_can_attach_a_new_comment_with_a_specified_parent_id()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();

        // Act
        $comment = $page->commentAs($user, 'This is my special comment!');
        $page->commentAs($user, 'This is the answer on your special comment.', $comment->id);

        // Assert
        $this->assertDatabaseHas('comments', [
            'comment' => 'This is my special comment!',
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('comments', [
            'comment' => 'This is the answer on your special comment.',
            'parent_id' => $comment->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test **/
    public function it_can_get_children_of_a_comment()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();

        // Act
        $parentComment = $page->commentAs($user, 'This is my special comment!');
        $page->commentAs($user, 'This is the answer on your special comment.', $parentComment->id);
        $page->commentAs($user, 'Other answer.', $parentComment->id);

        // Assert
        $this->assertCount(2, $parentComment->children);
        $this->assertEquals('This is the answer on your special comment.', $parentComment->children->first()->comment);
    }

    /** @test **/
    public function it_can_delete_a_parent_comment()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();

        // Act
        $parentComment = $page->commentAs($user, 'This is my special comment!');
        $page->commentAs($user, 'This is the answer on your special comment.', $parentComment->id);
        $page->commentAs($user, 'Other answer.', $parentComment->id);

        // Assert
        $parentComment->delete();
        $this->assertDatabaseHas('comments', [
            'id' => $parentComment->id,
        ]);
        $this->assertDatabaseHas('comments', [
            'comment' => 'This is the answer on your special comment.',
            'parent_id' => $parentComment->id,
        ]);
        // $this->assertCount(2, $parentComment->children);
        // $this->assertEquals('This is the answer on your special comment.', $parentComment->children->first()->comment);
    }

    /** @test **/
    public function it_get_all_comments_for_specific_resource()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $comments = \Ikoncept\Fabriq\Models\Comment::factory()
            ->count(5)
            ->create([
                'commentable_type' => 'Ikoncept\Fabriq\Models\Page',
                'commentable_id' => $page->id,
                'user_id' => \Ikoncept\Fabriq\Models\User::factory()->create(),
            ]);
        $firstComment = $comments->first();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();
        $childComment = $page->commentAs($user, 'This is the answer on your special comment.', $firstComment->id);

        // Act
        $response = $this->json('GET', '/pages/'.$page->id.'/comments?include=children');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
        $response->assertJsonFragment([
            'comment' => 'This is the answer on your special comment.',
        ]);
    }

    /** @test **/
    public function it_will_return_bad_args_response_if_relation_doesnt_exist()
    {
        // Arrange

        // Act
        $response = $this->json('GET', '/pagesrinoe/1/comments');

        // Assert
        $response->assertStatus(400);
    }

    /** @test **/
    public function it_will_return_bad_args_response_if_relation_doesnt_exist_when_creating()
    {
        // Arrange
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('POST', '/pagesrinoe/1/comments', [
            'comment' => 'a comment!',
        ]);

        // Assert
        $response->assertStatus(400);
    }

    /** @test **/
    public function it_can_create_a_new_comment()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();

        // Act
        $response = $this->json('POST', '/pages/'.$page->id.'/comments', [
            'comment' => '<p>I have no idea at all</p>',
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', [
            'comment' => '<p>I have no idea at all</p>',
        ]);
    }

    /** @test **/
    public function it_will_allow_the_creator_to_delete_its_comment()
    {
        // Arrange
        Event::fake(NotificationDeleted::class);
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();
        $otherUser = \Ikoncept\Fabriq\Models\User::factory()->create([
            'name' => 'Roger Pontare',
            'email' => 'roger@pontare.se',
        ]);
        $anotherUser = \Ikoncept\Fabriq\Models\User::factory()->create([
            'name' => 'Sven',
            'email' => 'sven@pontare.se',
        ]);
        $comment = $page->commentAs($user, '<p>This is my special comment! <span data-mention="" class="mention" data-email="roger@pontare.se">@Roger Pontare</span> <span data-mention="" class="mention" data-email="Sven">@Sven</span><p>');

        $this->actingAs($user);

        // Act
        $response = $this->json('DELETE', '/comments/'.$page->comments->first()->id);

        // Assert
        $response->assertOk();
        $this->assertDatabaseMissing('comments', [
            'comment' => 'This is my special comment!',
        ]);
        $this->assertDatabaseMissing('notifications', [
            'type' => 'comment',
            'notifiable_id' => $page->id,
        ]);
        $this->assertDatabaseMissing('notifications', [
            'user_id' => $otherUser->id,
        ]);
        Event::assertDispatched(NotificationDeleted::class);
    }

    /** @test **/
    public function it_will_not_allow_another_user_to_delete_another_users_comment()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();
        $page->commentAs($user, 'This is my special comment!');

        $currentUser = \Ikoncept\Fabriq\Models\User::factory()->create();
        $currentUser->assignRole('admin');
        $this->actingAs($currentUser);

        // Act
        $response = $this->json('DELETE', '/comments/'.$page->comments->first()->id);

        // Assert
        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', [
            'comment' => 'This is my special comment!',
        ]);
    }

    /** @test **/
    public function it_will_allow_another_user_to_delete_another_users_comment_if_has_role_dev()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();
        $page->commentAs($user, 'This is my special comment!');

        $currentUser = \Ikoncept\Fabriq\Models\User::factory()->create();
        $currentUser->assignRole(['admin', 'dev']);
        $this->actingAs($currentUser);

        // Act
        $response = $this->json('DELETE', '/comments/'.$page->comments->first()->id);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseMissing('comments', [
            'comment' => 'This is my special comment!',
        ]);
    }

    /** @test **/
    public function it_will_allow_a_user_to_edit_their_comment()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();
        $user->assignRole('admin');
        $page->commentAs($user, 'This is my special comment!');
        $this->actingAs($user);

        // Act
        $response = $this->json('PATCH', '/comments/'.$page->comments->first()->id, [
            'comment' => 'edited!',
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseMissing('comments', [
            'id' => $page->comments->first()->id,
            'comment' => 'This is my special comment!',
        ]);
        $this->assertDatabaseMissing('comments', [
            'id' => $page->comments->first()->id,
            'comment' => 'edited!',
            'edited_at' => null,
        ]);
        $this->assertDatabaseHas('comments', [
            'id' => $page->comments->first()->id,
            'comment' => 'edited!',
        ]);
    }

    /** @test **/
    public function it_will_create_a_notification_if_a_user_is_mentionend_in_the_comment()
    {
        // Arrange
        Event::fake([CommentPosted::class, UserMentionedInComment::class]);
        // Event::fake(UserMentionedInComment::class);
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $otherUser = \Ikoncept\Fabriq\Models\User::factory()->create([
            'name' => 'Roger Pontare',
            'email' => 'roger@pontare.se',
        ]);
        $anotherUser = \Ikoncept\Fabriq\Models\User::factory()->create([
            'name' => 'Sven',
            'email' => 'sven@pontare.se',
        ]);
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();
        $comment = $page->commentAs($user, '<p>This is my special comment! <span data-mention="" class="mention" data-email="roger@pontare.se">@Roger Pontare</span> <span data-mention="" class="mention" data-email="sven@pontare.se">@Sven</span><p>');

        $this->assertDatabaseHas('comments', [
            'comment' => '<p>This is my special comment! <span data-mention="" class="mention" data-email="roger@pontare.se">@Roger Pontare</span> <span data-mention="" class="mention" data-email="sven@pontare.se">@Sven</span><p>',
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $comment->id,
            'user_id' => $otherUser->id,
        ]);
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $comment->id,
            'user_id' => $anotherUser->id,
        ]);
        Event::assertDispatched(CommentPosted::class);
        Event::assertDispatched(UserMentionedInComment::class);
    }

    /** @test **/
    public function it_will_delete_the_anonymized_parent_if_there_are_no_children_left()
    {
        // Arrange
        Event::fake(CommentDeleted::class);
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();
        $comment = $page->commentAs($user, 'This is my special comment!');
        $childComment = $page->commentAs($user, 'This is the answer on your special comment.', $comment->id);
        $comment->delete();
        $this->actingAs($user);

        // Act
        $response = $this->json('DELETE', '/comments/'.$childComment->id);

        // Assert
        $response->assertOk();
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
        Event::assertDispatched(CommentDeleted::class);
    }
}
