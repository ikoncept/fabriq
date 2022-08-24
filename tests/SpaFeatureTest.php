<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;

class SpaFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_will_return_a_message_if_request_wants_json()
    {
        // Arrange
        $this->markTestSkipped();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();

        // Act
        $response = $this->actingAs($user, 'api')->json('GET', '/something/something');

        // Assert
        $response->assertStatus(404);
        $response->assertJsonFragment(['Get outta here!']);
    }

    /** @test **/
    public function it_will_return_the_index_view_if_the_request_doesnt_want_json()
    {
        // Arrange
        $this->markTestSkipped();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();

        // Act
        $response = $this->actingAs($user)->get('/something/something');

        // Assert
        $response->assertOk();
        $response->assertSee('Fabriq CMS');
    }
}
