<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class WhereLikeMacroFeatureTest extends AdminUserTestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_can_search_for_a_model()
    {
        // Arrange
        $user = \Ikoncept\Fabriq\Models\User::factory()->create([
            'name' => 'Ralf'
        ]);
        $user = \Ikoncept\Fabriq\Models\User::factory()->create([
            'name' => 'Lars'
        ]);

        // Act
        $users = \Ikoncept\Fabriq\Models\User::whereLike(['name', 'email'], 'Lars')->get();

        // Assert
        $this->assertEquals('Lars', $users->first()->name);
        $this->assertCount(1, $users);
    }
}
