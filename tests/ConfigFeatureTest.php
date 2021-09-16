<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;

class ConfigFeatureTest extends AdminUserTestCase
{
    use RefreshDatabase;


    public function testGetConfigData()
    {
        // Arrange
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('GET', '/config');

        // Assert
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'modules' => [],
                'supported_locales' => []
            ]
        ]);
    }
}
