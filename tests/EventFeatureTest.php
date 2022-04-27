<?php

namespace Tests\Feature;

use Carbon\CarbonImmutable;

use Illuminate\Foundation\Testing\WithFaker;
use Infab\TranslatableRevisions\Models\I18nLocale;
use Infab\TranslatableRevisions\Models\I18nTerm;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;
use Infab\TranslatableRevisions\Models\I18nDefinition;
use Infab\TranslatableRevisions\Models\RevisionMeta;

class EventFeatureTest extends AdminUserTestCase
{


    public function setUp() : void
    {
        parent::setUp();
        $template = RevisionTemplate::factory()->create([
            'name' => 'Event item',
            'slug' => 'event-item'
        ]);
        $templateField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'translated' => true,
            'name' => 'title',
            'key' => 'title',
            'type' => 'text'
        ]);
        $templateField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'translated' => true,
            'name' => 'description',
            'key' => 'description',
            'type' => 'text'
        ]);
        $templateField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'translated' => true,
            'name' => 'location',
            'key' => 'location',
            'type' => 'text'
        ]);
    }


    /** @test **/
    public function it_can_store_a_new_event()
    {
        // Arrange
        $this->withoutExceptionHandling();
        // $enabledLocales = I18nLocale::all();
        // dd($enabledLocales->toArray());

        // Act
        $response = $this->json('POST', '/events', [
            'date' => [
                'start' => now()->subWeek()->startOfDay()->toDateTimeString(),
                'end' => now()->addDays(2)->startOfDay()->toDateTimeString(),
            ],
            'start_time' => '08:00',
            'end_time' => '10:00',
            'full_day' => false,
            'localizedContent' => [
                'en' => [
                    'title' => 'Shop is closed',
                    'description' => 'Shop is closed for the public',
                    'location' => 'By the lake'
                ],
                'sv' => [
                    'title' => 'Banan är stängd',
                    'description' => 'Banan är stängd för allmänheten',
                    'location' => 'Vid sjön'
                ]
            ]
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('events', [
            'start' => now()->subWeek()->startOfDay()->toDateTimeString(),
            'end' => now()->addDays(2)->startOfDay()->toDateTimeString(),
            'start_time' => '08:00',
            'end_time' => '10:00',
            'full_day' => 1
        ]);
        $this->assertDatabaseHas('i18n_definitions', [
            'content' => json_encode('Shop is closed'),
            'locale' => 'en'
        ]);
    }

    /** @test **/
    public function it_can_get_all_events()
    {
        // Arrange
        $event0 = \Ikoncept\Fabriq\Models\Event::factory()->create();
        $event0->updateContent([
            'title' => 'Öppet hus',
        ], 'sv');
        $event1 = \Ikoncept\Fabriq\Models\Event::factory()->create();
        $event1->updateContent([
            'title' => 'Stängt hus',
        ], 'sv');
        $event2 = \Ikoncept\Fabriq\Models\Event::factory()->create([
            'start' => now()->startOfDay()->subYears(30)->toDateTimeString()
        ]);
        $event2->updateContent([
            'title' => 'Rivet hus',
        ], 'sv');

        // Act
        $response = $this->json('GET', '/events?append=title&filter[dateRange]=1999-01-01,' . now()->toDateString());

        // Assert
        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment([
            'title' => 'Stängt hus',
        ]);
        $response->assertJsonFragment([
            'title' => 'Öppet hus',
        ]);
    }

    /** @test **/
    public function it_can_get_a_single_event()
    {
        // Arrange
        $event0 = \Ikoncept\Fabriq\Models\Event::factory()->create();
        $event0->updateContent([
            'title' => 'Öppet hus',
            'description' => 'Öppet hus hela dagen'
        ]);

        // Act
        $response = $this->json('GET', '/events/' . $event0->id . '?include=content');


        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'title' => 'Öppet hus',
            'description' => 'Öppet hus hela dagen'
        ]);
    }

    /** @test **/
    public function it_can_delete_an_event()
    {
        // Arrange
        $event0 = \Ikoncept\Fabriq\Models\Event::factory()->create();
        $event0->updateContent([
            'title' => 'Öppet hus',
            'description' => 'Öppet hus hela dagen'
        ]);

        // Act
        $response = $this->json('DELETE', '/events/' . $event0->id);

        // Assert
        $response->assertOk();
        $delimiter = $event0->getDelimiter();
        $this->assertDatabaseMissing('events', [
            'id' => $event0->id
        ]);
        $this->assertDatabaseMissing('i18n_terms', [
            'key' => "events{$delimiter}1{$delimiter}{$delimiter}title"
        ]);

    }

    /** @test **/
    public function it_can_update_an_event()
    {
        // Arrange
        $event0 = \Ikoncept\Fabriq\Models\Event::factory()->create();
        $event0->updateContent([
            'title' => 'Öppet hus',
            'description' => 'Öppet hus hela dagen'
        ]);

        // Act
        $response = $this->json('PATCH', '/events/' . $event0->id . '?include=localizedContent', [
            'date' => [
                'start' => now()->subWeek()->startOfDay()->toDateTimeString(),
                'end' => now()->addDays(2)->startOfDay()->toDateTimeString(),
            ],
            'start_time' => '08:00',
            'end_time' => '10:00',
            'full_day' => false,
            'localizedContent' => [
                'en' => [
                    'title' => 'Shop is closed',
                    'description' => 'Shop is closed for the public',
                    'location' => 'By the lake'
                ],
                'sv' => [
                    'title' => 'Banan är stängd',
                    'description' => 'Banan är stängd för allmänheten',
                    'location' => 'Vid sjön'
                ]
            ]
        ]);


        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'start_time' => '08:00',
            'end_time' => '10:00',
        ]);
        $response->assertJsonFragment([
            'title' => 'Shop is closed',
            'description' => 'Shop is closed for the public',
            'location' => 'By the lake'
        ]);
        $response->assertJsonFragment([
            'title' => 'Banan är stängd',
            'description' => 'Banan är stängd för allmänheten',
            'location' => 'Vid sjön'
        ]);
    }

    /** @test **/
    public function it_can_get_repeated_events()
    {
        // Arrange
        $start = CarbonImmutable::make('1999-01-01')->startOfDay();
        $event = \Ikoncept\Fabriq\Models\Event::factory()->create([
            'start' => $start->addDays(5),
            'daily_interval' => 7
        ]);
        $start = CarbonImmutable::make('1999-01-01')->startOfDay();
        $end = $start->addMonths(3);

        // Act
        $response = $this->json('GET', "/events?filter[dateRange]={$start->toDateString()},{$end->toDateString()}");

        // Assert
        $response->assertOk();
        $response->assertJsonCount(14, 'data');
        $response->assertJsonFragment([
            'id' => $event->id,
            'start' => $start->addDays(7+5)->toISOString()
        ]);
        $response->assertJsonFragment([
            'id' => $event->id,
            'start' => $start->addDays(7+7+5)->toISOString()
        ]);
    }
}
