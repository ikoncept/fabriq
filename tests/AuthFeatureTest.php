<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // dd(base_path('tests/_fixtures/distFixture/mix-manifest.json'));
        $fixturePath = __DIR__.'/_fixtures/';
        if (! file_exists(base_path('public/dist'))) {
            mkdir(base_path('public/dist'));
        }
        if (! file_exists(base_path('public/dist/mix-manifest.json'))) {
            copy($fixturePath.'mix-manifest.json', base_path('public/dist/mix-manifest.json'));
        }

        if (! file_exists(base_path('public/build'))) {
            mkdir(base_path('public/build'));
        }
        if (! file_exists(base_path('public/build/manifest.json'))) {
            copy($fixturePath.'manifest.json', base_path('public/build/manifest.json'));
        }
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
