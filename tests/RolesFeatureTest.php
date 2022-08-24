<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;

class RolesFeatureTest extends AdminUserTestCase
{
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
