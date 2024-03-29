<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Spatie\Tags\Tag;

class TagFeatureTest extends AdminUserTestCase
{
    protected $endpoint = '/tags/';

    /** @test **/
    public function it_can_get_all_tags_with_a_specific_type()
    {
        // Arrange
        // $this->withoutExceptionHandling();
        $tag = Tag::findOrCreate('my tag');
        $contact = \Ikoncept\Fabriq\Models\Contact::factory()->create();
        $contact->contactTags = ['One', 'Two', 'Three'];
        $contact->save();

        // Act
        $response = $this->json('GET', $this->endpoint.'?filter[type]=contacts');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonFragment([
            'name' => 'One',
        ]);
        $response->assertJsonFragment([
            'name' => 'Two',
        ]);
        $response->assertJsonFragment([
            'name' => 'Three',
        ]);
    }
}
