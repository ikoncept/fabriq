<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Tests\AdminUserTestCase;

class ContactsFeatureTest extends AdminUserTestCase
{
    protected $endpoint = '/contacts/';

    /** @test **/
    public function it_can_get_all_contacts()
    {
        // Arrange
        $contacts = \Ikoncept\Fabriq\Models\Contact::factory()->count(5)->create();

        // Act
        $response = $this->json('GET', $this->endpoint);

        // Assert
        $response->assertOk();
        $response->assertJsonCount(5, 'data');
    }

    /** @test **/
    public function it_can_get_a_single_contact()
    {
        // Arrange
        $contact = \Ikoncept\Fabriq\Models\Contact::factory()->create();

        // Act
        $response = $this->json('GET', $this->endpoint.$contact->id);

        // Assert
        $response->assertOk();
    }

    /** @test **/
    public function it_can_store_a_new_contact()
    {
        // Arrange
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('POST', $this->endpoint, [
            'name' => 'Rolf Lassgård',
        ]);

        // Assert
        $response->assertStatus(201);
    }

    /** @test **/
    public function it_can_update_a_contact()
    {
        // Arrange
        $contact = \Ikoncept\Fabriq\Models\Contact::factory()->create();
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('PATCH', $this->endpoint.$contact->id.'?include=localizedContent', [
            'name' => 'Janne Josefsson',
            'email' => 'janne@svt.se',
            'phone' => '070-991100',
            'published' => true,
            'locale' => 'sv',
            'sortindex' => 100,
            'content' => [
                'body' => '<p>a nice text</p>',
            ],
            'localizedContent' => [
                'sv' => [
                    'body' => '<p>en fin text text</p>',
                ],
                'en' => [
                    'body' => '<p>a nice text</p>',
                ],
            ],
        ]);

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Janne Josefsson',
            'email' => 'janne@svt.se',
            'phone' => '070-991100',
            'published' => true,
        ]);
        $response->assertJsonFragment([
            'body' => '<p>en fin text text</p>',
        ]);
        $this->assertDatabaseHas('contacts', [
            'name' => 'Janne Josefsson',
            'email' => 'janne@svt.se',
            'phone' => '070-991100',
            'published' => true,
            'sortindex' => 100,
        ]);
        $this->assertDatabaseHas('i18n_definitions', [
            'content' => json_encode('<p>en fin text text</p>'),
            'locale' => 'sv',
        ]);
        $this->assertDatabaseHas('i18n_definitions', [
            'content' => json_encode('<p>a nice text</p>'),
            'locale' => 'en',
        ]);
    }

    /** @test **/
    public function it_can_delete_a_contact()
    {
        // Arrange
        $contact = \Ikoncept\Fabriq\Models\Contact::factory()->create();

        // Act
        $response = $this->json('DELETE', $this->endpoint.$contact->id);

        // Assert
        $response->assertOk();
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }

    /** @test **/
    public function it_can_attach_tags_to_a_contact()
    {
        // Arrange
        $contact = \Ikoncept\Fabriq\Models\Contact::factory()->create();
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('PATCH', $this->endpoint.$contact->id, [
            'name' => 'Chris Moltisanti',
            'tags' => [
                'Reception', 'Administration',
            ],
            'content' => [],
            'localizedContent' => [],
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('tags', [
            'type' => 'contacts',
        ]);
    }
}
