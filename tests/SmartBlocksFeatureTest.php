<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\ContentGetters\FileGetter;
use Ikoncept\Fabriq\ContentGetters\ImageGetter;
use Ikoncept\Fabriq\ContentGetters\VideoGetter;

use Illuminate\Foundation\Testing\WithFaker;
use Infab\TranslatableRevisions\Models\RevisionMeta;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Ikoncept\Fabriq\Tests\TestCase;

class SmartBlocksFeatureTest extends AdminUserTestCase
{


    /** @test **/
    public function it_can_store_a_new_smart_block()
    {
        // Arrange
        $this->withoutExceptionHandling();

        // Act
        $response = $this->json('POST', '/smart-blocks', [
            'name' => 'Find store smart block',
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('smart_blocks', [
            'name' => 'Find store smart block',
        ]);
    }


    /** @test **/
    public function it_can_update_a_smart_block()
    {
        // Arrange
        $smartBlock = \Ikoncept\Fabriq\Models\SmartBlock::factory()->create();
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create();
        $video = \Ikoncept\Fabriq\Models\Video::factory()->create();
        $file = \Ikoncept\Fabriq\Models\File::factory()->create();
        $file2 = \Ikoncept\Fabriq\Models\File::factory()->create();


        // Act
        $response = $this->json('PATCH', '/smart-blocks/' . $smartBlock->id, [
            'name' => 'Another name',
            'localizedContent' => [
                'sv' => [
                    'boxes' => [
                        [
                            'name' => 'Superblocket',
                            'image' => $this->getParsedImage($image),
                            'video' => $this->getParsedVideo($video),
                            'file' => $this->getParsedFile($file2),
                        ]
                    ]
                ],
                'en' => [
                    'boxes' => [
                        [
                            'name' => 'The super block'
                        ]
                    ]
                ]
            ]
        ]);

        // Assert
        $response->assertOk();
        $this->assertDatabaseHas('smart_blocks', [
            'id' => $smartBlock->id,
            'name' => 'Another name',
        ]);
        $this->assertDatabaseHas('i18n_definitions', [
            'content' => json_encode([
                [
                    'name' => 'Superblocket',
                    'image' => [$image->id],
                    'video' => [$video->id],
                    'file' => [$file2->id]
                ]
            ]),
            'locale' => 'sv'
        ]);
        $this->assertDatabaseHas('i18n_definitions', [
            'content' => json_encode([['name' => 'The super block']]),
            'locale' => 'en'
        ]);
    }

    /** @test **/
    public function it_can_get_all_smart_blocks()
    {
        // Arrange
        $smartBlocks = \Ikoncept\Fabriq\Models\SmartBlock::factory()->count(3)->create();

        // Act
        $response = $this->json('GET', '/smart-blocks');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    /** @test **/
    public function it_can_sort_smart_blocks()
    {
        // Arrange
        $smartBlocks = \Ikoncept\Fabriq\Models\SmartBlock::factory()->count(3)->create();
        $smartBlock = \Ikoncept\Fabriq\Models\SmartBlock::factory()->create([
            'name' => '1st :)'
        ]);

        // Act
        $response = $this->json('GET', '/smart-blocks?sort=name');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(4, 'data');
        $this->assertEquals('1st :)', $response->json()['data'][0]['name']);
    }

    /** @test **/
    public function it_can_search_for_a_smart_block()
    {
        // Arrange
        $smartBlocks = \Ikoncept\Fabriq\Models\SmartBlock::factory()->count(3)->create();
        $smartBlock = \Ikoncept\Fabriq\Models\SmartBlock::factory()->create([
            'name' => 'Find me'
        ]);

        // Act
        $response = $this->json('GET', '/smart-blocks?filter[search]=Find me');

        // Assert
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    /** @test **/
    public function it_can_get_a_single_smart_block()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $smartBlock = \Ikoncept\Fabriq\Models\SmartBlock::factory()->create();
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create([
            'alt_text' => 'Image alt text'
        ]);
        $video = \Ikoncept\Fabriq\Models\Video::factory()->create([
            'alt_text' => 'Video alt text'
        ]);
        $file = \Ikoncept\Fabriq\Models\File::factory()->create([
            'caption' =>  'File caption'
        ]);
        $smartBlock->localizedContent = [
            'sv' => [
                'boxes' => [
                    [
                        'name' => 'Superblocket',
                        'image' => $this->getParsedImage($image),
                        'video' => $this->getParsedVideo($video),
                        'file' => $this->getParsedFile($file)
                    ]
                ]
            ],
            'en' => [
                'boxes' => [
                    [
                        'name' => 'The super block'
                    ]
                ]
            ]
        ];
        $smartBlock->save();

        // Act
        $response = $this->json('GET', '/smart-blocks/' . $smartBlock->id . '?include=localizedContent,content');

        // Assert
        $response->assertOk();
        $response->assertJsonFragment([
            'name' => 'Superblocket'
        ]);
        $response->assertJsonFragment([
            'alt_text' => 'Image alt text'
        ]);
        $response->assertJsonFragment([
            'alt_text' => 'Video alt text'
        ]);
        $response->assertJsonFragment([
            'caption' =>  'File caption'
        ]);
    }

    /** @test **/
    public function it_can_destroy_a_smart_block()
    {
        // Arrange
        $this->withoutExceptionHandling();
        $smartBlock = \Ikoncept\Fabriq\Models\SmartBlock::factory()->create();

        // Act
        $response = $this->json('DELETE', '/smart-blocks/' . $smartBlock->id);

        // Assert
        $response->assertOk();
        $this->assertDatabaseMissing('smart_blocks', [
            'id' => $smartBlock->id
        ]);
    }

    protected function getParsedImage($image)
    {
        $metaImage = RevisionMeta::make([
            'meta_value' => [$image->id]
        ]);

        return ImageGetter::get($metaImage);
    }

    protected function getParsedVideo($video)
    {
        $metaVideo = RevisionMeta::make([
            'meta_value' => [$video->id]
        ]);

        return VideoGetter::get($metaVideo);
    }

    protected function getParsedFile($file)
    {
        $metaFile = RevisionMeta::make([
            'meta_value' => [$file->id]
        ]);

        return FileGetter::get($metaFile);
    }
}
