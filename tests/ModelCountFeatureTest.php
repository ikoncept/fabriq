<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\WithFaker;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;

class ModelCountFeatureTest extends AdminUserTestCase
{


    /** @test **/
    public function it_will_return_an_error_if_the_model_isnt_countable()
    {
        // Arrange

        // Act
        $response = $this->json('GET', '/enmodell/count');

        // Assert
        $response->assertStatus(400);
    }
}
