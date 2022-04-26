<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;

use Illuminate\Foundation\Testing\WithFaker;
use Ikoncept\Fabriq\Tests\TestCase;

class AuthenticatedUserFeatureTest extends AdminUserTestCase
{


    public function testAnAuhtenticatedUserCanGetInfoAboutSelf()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $user = \Ikoncept\Fabriq\Models\User::factory([
            'email_verified_at' => now()->subSeconds(30)
        ])->create();

        // Act
        $response = $this->actingAs($user)
            ->json('GET', '/user');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => $user->name,
            'email' => $user->email
        ]);
    }

    /** @test **/
    public function it_can_update_its_data()
    {
        $this->markTestSkipped();
        // Arrange
        // $this->withoutExceptionHandling();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create([
            'password' => bcrypt('secret'),
            'email_verified_at' => now()->subSeconds(30)
        ]);

        // Act
        $response = $this->actingAs($user)->json('PATCH', '/user/self', [
            'name' => 'Anders Persson',
            'email' => 'foppp@fep.com',
            'password' => 'newsecret12345',
            'password_confirmation' => 'newsecret12345',
            'current_password' => 'secret'
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'name' => 'Anders Persson',
            'email' => 'foppp@fep.com',
        ]);
    }
}
