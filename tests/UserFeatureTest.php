<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;

class UserFeatureTest extends AdminUserTestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_can_get_all_users()
    {
        // Arrange
        $users = \Ikoncept\Fabriq\Models\User::factory()->count(3)->create();

        // Act
        $response = $this->json('GET', '/users');

        // Assert
        $response->assertOk();
        // 4 because the requester is one as well
        $response->assertJsonCount(4, 'data');
    }

    /** @test **/
    public function it_can_sort_users()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create([
            'name' => 'Zebra Green'
        ]);
        $users = \Ikoncept\Fabriq\Models\User::factory()->count(1)->create();

        // Act
        $response = $this->json('GET', '/users?sort=-name');

        // Assert
        $response->assertOk();
        // 4 because the requester is one as well
        $response->assertJsonCount(3, 'data');
        $this->assertEquals('Zebra Green', $response->json()['data'][0]['name']);
    }

    /** @test **/
    public function it_can_store_a_new_user()
    {
        // Arrange

        // Act
        $response = $this->json('POST', '/users', [
            'name' => 'Ralf Edström',
            'email' => 'ralf@spray.se',
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'Ralf Edström',
            'email' => 'ralf@spray.se',
        ]);
        $this->assertDatabaseMissing('users', [
            'name' => 'Ralf Edström',
            'email' => 'ralf@spray.se',
            'email_verified_at' => null
        ]);
    }

    /** @test **/
    public function it_will_validate_input_before_creating_a_user()
    {
        // Arrange
        app()->setLocale('sv');
        $user = \Ikoncept\Fabriq\Models\User::factory()->create([
            'email' => 'ralf@spray.se'
        ]);

        // Act
        $response = $this->json('POST', '/users', [
            'email' => 'ralf@spray.se',
        ]);

        // Assert
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'name']);
        $response->assertJsonFragment([
            'E-post används redan.'
        ]);
    }

    /** @test **/
    public function it_will_only_allow_admins_to_view_other_users()
    {
        // Arrange
        $this->markTestSkipped('Skipped since there is no auth middleware');
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();

        // Act
        $response = $this->actingAs($user)
            ->json('GET', '/users');

        // Assert
        $response->assertStatus(403);
    }

    /** @test **/
    public function it_can_show_a_single_user()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();
        $adminRole = DB::table('roles')->insert([
            'name' => 'peasant',
            'display_name' => 'Peasant',
            'description' => '',
        ]);
        $user->assignRole('peasant');


        // Act
        $response = $this->json('GET', '/users/' . $user->id . '?include=roles');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'role_list' => ['peasant']
        ]);
    }

    /** @test **/
    public function it_can_update_a_user()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();

        DB::table('roles')->insert([
            'name' => 'editor',
            'display_name' => 'Editor',
            'description' => 'Redaktörer',
        ]);
        $editorRole = Role::where('name', 'editor')->first();

        // Act
        $response = $this->json('PATCH', '/users/' . $user->id, [
            'name' => 'Alfons Åberg',
            'email' => 'alfons@aaberg.se',
            'role_list' => ['editor']
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('users', [
            'name' => 'Alfons Åberg',
            'email' => 'alfons@aaberg.se',
        ]);
        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $editorRole->id,
            'model_id' => $user->id
        ]);
    }

    /** @test **/
    public function it_can_search_for_a_specific_user()
    {
        // Arrange
        $user = \Ikoncept\Fabriq\Models\User::factory()->create([
            'name' => 'Alfons'
        ]);
        $user = \Ikoncept\Fabriq\Models\User::factory()->create([
            'name' => 'Jörgen'
        ]);

        // Act
        $response = $this->json('GET', '/users?filter[search]=alfons');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['name' => 'Alfons']);
    }

    /** @test **/
    public function it_can_delete_a_user()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $user = \Ikoncept\Fabriq\Models\User::factory()->create();

        // Act
        $response = $this->json('DELETE', '/users/' . $user->id);

        // Assert
        $response->assertOk();
    }
}
