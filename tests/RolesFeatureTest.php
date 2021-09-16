<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;

class RolesFeatureTest extends AdminUserTestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_can_get_all_roles()
    {
        // Arrange

        // Act
        $response = $this->json('GET', '/roles');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'admin',
            'display_name' => 'Admin',
            'description' => 'Administratörer',
        ]);
    }
}
