<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    /** @test **/
    public function it_can_show_a_login_page()
    {
        // Arrange
        $this->markTestSkipped();

        // Act
        $response = $this->get(route('login'));

        // Assert
        $response->assertSee('Logga in');
        $response->assertOk();
    }

    /** @test **/
    public function it_can_show_forgot_password_request_page()
    {
        // Arrange
        $this->markTestSkipped();

        // Act
        $response = $this->get(route('password.request'));

        // Assert
        $response->assertSee('Skicka lösenordsåterställning');
        $response->assertOk();
    }
}
