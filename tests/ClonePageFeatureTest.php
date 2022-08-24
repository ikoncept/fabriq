<?php

namespace Tests\Feature;

use Ikoncept\Fabriq\Database\Seeders\DatabaseSeeder;
use Ikoncept\Fabriq\Database\Seeders\PageTemplateSeeder;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Infab\TranslatableRevisions\Models\RevisionTemplate;

class ClonePageFeatureTest extends AdminUserTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->pageData = json_decode('{"localizedContent": {"sv":{"title":"Att klona","page_title":"Att klona","boxes":[{"name":"El blocko","block_type":{"id":1,"name":"Demo-block","component_name":"DemoBlock","has_children":true},"children":[{"id":"if0i8d5","name":"Kort 1","newlyAdded":false,"hasImage":true,"image":{"id":12,"uuid":"bb866334-c696-40f4-a4de-ad134f67a6b1","name":"76a57431-6a3c-4636-840e-0891fe3a9e11","c_name":"76a57431-6a3c-4636-840e-0891fe3a9e11.jpg","extension":"jpg","file_name":"76a57431-6a3c-4636-840e-0891fe3a9e11.jpg","thumb_src":"https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/c/76a57431-6a3c-4636-840e-0891fe3a9e11-thumb.webp","webp_src":"https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/c/76a57431-6a3c-4636-840e-0891fe3a9e11-webp.webp","src":"https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/76a57431-6a3c-4636-840e-0891fe3a9e11.jpg","srcset":"https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_1024_681.webp 1024w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_856_569.webp 856w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_716_476.webp 716w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_599_398.webp 599w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_501_333.webp 501w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_419_278.webp 419w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_351_233.webp 351w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_293_194.webp 293w, data:image/svg+xml;base64,PCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHg9IjAiCiB5PSIwIiB2aWV3Qm94PSIwIDAgMTAyNCA2ODEiPgoJPGltYWdlIHdpZHRoPSIxMDI0IiBoZWlnaHQ9IjY4MSIgeGxpbms6aHJlZj0iZGF0YTppbWFnZS9qcGVnO2Jhc2U2NCwvOWovNEFBUVNrWkpSZ0FCQVFBQUFRQUJBQUQvMndCREFBTUNBZ01DQWdNREF3TUVBd01FQlFnRkJRUUVCUW9IQndZSURBb01EQXNLQ3dzTkRoSVFEUTRSRGdzTEVCWVFFUk1VRlJVVkRBOFhHQllVR0JJVUZSVC8yd0JEQVFNRUJBVUVCUWtGQlFrVURRc05GQlFVRkJRVUZCUVVGQlFVRkJRVUZCUVVGQlFVRkJRVUZCUVVGQlFVRkJRVUZCUVVGQlFVRkJRVUZCUVVGQlFVRkJUL3dBQVJDQUFWQUNBREFSRUFBaEVCQXhFQi84UUFGd0FCQVFFQkFBQUFBQUFBQUFBQUFBQUFCUVlIQ1AvRUFDSVFBQUVFQWdJQ0F3RUFBQUFBQUFBQUFBRUNBd1FGRVFBR0VpRXhFeFJCVWYvRUFCb0JBQUlEQVFFQUFBQUFBQUFBQUFBQUFBWUZCQWNEQVFML3hBQWZFUUFDQXdFQUFnTUJBQUFBQUFBQUFBQUNBUUFERVFRU0lSUUZFekgvMmdBTUF3RUFBaEVERVFBL0FJNTJ5bU16bHJRczRKMnZ1SHRBeTF5eE9xd3Z6OElQY1I1RjFJN0t5Y2FWZkpESUtmSEp2WEtMaFBLWEttYTNES3NKeUFkZ2RQVmdQSk1xb0VYczJrWEVSK0duQkhjanpvY2ZiNG5zZER1VG5XUEprdUZUaFNTblV2T1hnOFUzc1dyM0QzZVp0VjdyaVY0enBsejEyV0RzVldYQUNhazNWOHlRL3dBZzdCV0JuZCt1c2xWSWxObW5ObHArUkIxaEorWFBqKzdYbC9raWNmRG1RbSs2MWRLNHBwQUo2L3V5K1FFZGkyZWVnbUkrcGhFaVN1emx5Rk9ISGsrdHRYaXFFYTBvSDNFeUtGeFdmckxkZFFvOWsrdDB2cUUxam5heVkrMUZLRG5saTNMTFFPVWp4NzBZdSt0cEl0aklPay81UC8vWiI+Cgk8L2ltYWdlPgo8L3N2Zz4= 32w","alt_text":null,"caption":null,"mime_type":"image/jpeg","custom_crop":false,"x_position":"50%","y_position":"50%","size":130431,"width":1024,"height":681,"updated_at":"2022-05-30T09:37:02.000000Z","created_at":"2022-05-30T09:37:02.000000Z"},"header":"Test","subheader":"Test","body":"<p>Aiii</p>"}],"newlyAdded":false,"id":"i3d32a8","image":{"id":12,"uuid":"bb866334-c696-40f4-a4de-ad134f67a6b1","name":"76a57431-6a3c-4636-840e-0891fe3a9e11","c_name":"76a57431-6a3c-4636-840e-0891fe3a9e11.jpg","extension":"jpg","file_name":"76a57431-6a3c-4636-840e-0891fe3a9e11.jpg","thumb_src":"https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/c/76a57431-6a3c-4636-840e-0891fe3a9e11-thumb.webp","webp_src":"https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/c/76a57431-6a3c-4636-840e-0891fe3a9e11-webp.webp","src":"https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/76a57431-6a3c-4636-840e-0891fe3a9e11.jpg","srcset":"https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_1024_681.webp 1024w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_856_569.webp 856w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_716_476.webp 716w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_599_398.webp 599w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_501_333.webp 501w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_419_278.webp 419w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_351_233.webp 351w, https://media.fabriq-cms.se/tebbe/bb866334-c696-40f4-a4de-ad134f67a6b1/responsive-images/76a57431-6a3c-4636-840e-0891fe3a9e11___media_library_original_293_194.webp 293w, data:image/svg+xml;base64,PCFET0NUWVBFIHN2ZyBQVUJMSUMgIi0vL1czQy8vRFREIFNWRyAxLjEvL0VOIiAiaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkIj4KPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHhtbDpzcGFjZT0icHJlc2VydmUiIHg9IjAiCiB5PSIwIiB2aWV3Qm94PSIwIDAgMTAyNCA2ODEiPgoJPGltYWdlIHdpZHRoPSIxMDI0IiBoZWlnaHQ9IjY4MSIgeGxpbms6aHJlZj0iZGF0YTppbWFnZS9qcGVnO2Jhc2U2NCwvOWovNEFBUVNrWkpSZ0FCQVFBQUFRQUJBQUQvMndCREFBTUNBZ01DQWdNREF3TUVBd01FQlFnRkJRUUVCUW9IQndZSURBb01EQXNLQ3dzTkRoSVFEUTRSRGdzTEVCWVFFUk1VRlJVVkRBOFhHQllVR0JJVUZSVC8yd0JEQVFNRUJBVUVCUWtGQlFrVURRc05GQlFVRkJRVUZCUVVGQlFVRkJRVUZCUVVGQlFVRkJRVUZCUVVGQlFVRkJRVUZCUVVGQlFVRkJRVUZCUVVGQlFVRkJUL3dBQVJDQUFWQUNBREFSRUFBaEVCQXhFQi84UUFGd0FCQVFFQkFBQUFBQUFBQUFBQUFBQUFCUVlIQ1AvRUFDSVFBQUVFQWdJQ0F3RUFBQUFBQUFBQUFBRUNBd1FGRVFBR0VpRXhFeFJCVWYvRUFCb0JBQUlEQVFFQUFBQUFBQUFBQUFBQUFBWUZCQWNEQVFML3hBQWZFUUFDQXdFQUFnTUJBQUFBQUFBQUFBQUNBUUFERVFRU0lSUUZFekgvMmdBTUF3RUFBaEVERVFBL0FJNTJ5bU16bHJRczRKMnZ1SHRBeTF5eE9xd3Z6OElQY1I1RjFJN0t5Y2FWZkpESUtmSEp2WEtMaFBLWEttYTNES3NKeUFkZ2RQVmdQSk1xb0VYczJrWEVSK0duQkhjanpvY2ZiNG5zZER1VG5XUEprdUZUaFNTblV2T1hnOFUzc1dyM0QzZVp0VjdyaVY0enBsejEyV0RzVldYQUNhazNWOHlRL3dBZzdCV0JuZCt1c2xWSWxObW5ObHArUkIxaEorWFBqKzdYbC9raWNmRG1RbSs2MWRLNHBwQUo2L3V5K1FFZGkyZWVnbUkrcGhFaVN1emx5Rk9ISGsrdHRYaXFFYTBvSDNFeUtGeFdmckxkZFFvOWsrdDB2cUUxam5heVkrMUZLRG5saTNMTFFPVWp4NzBZdSt0cEl0aklPay81UC8vWiI+Cgk8L2ltYWdlPgo8L3N2Zz4= 32w","alt_text":null,"caption":null,"mime_type":"image/jpeg","custom_crop":false,"x_position":"50%","y_position":"50%","size":130431,"width":1024,"height":681,"updated_at":"2022-05-30T09:37:02.000000Z","created_at":"2022-05-30T09:37:02.000000Z"},"size":"large","button":{"text":"","linkType":"internal","page_id":null},"hasButton":true,"header":"block rubbe","subheader":"underrubbe"}]},"en":{"title":"Att klona","page_title":"Att klona"}}}', true);

        app(DatabaseSeeder::class)->call(PageTemplateSeeder::class);
    }

    /** @test **/
    public function it_can_clone_another_page()
    {
        // Arrange
        $page = \Ikoncept\Fabriq\Models\Page::factory()->create([
            'name' => 'Den första startsidan',
            'template_id' => RevisionTemplate::all()->first()->id,
        ]);
        $image = \Ikoncept\Fabriq\Models\Image::factory()->create([
            'id' => 12,
        ]);
        $image->addMediaFromString('A nice media')
            ->toMediaCollection('profile_image');
        $image->save();
        $page->localizedContent = $this->pageData['localizedContent'];
        $page->save();

        // Act
        $response = $this->json('POST', route('pages.clone.store', $page->id).'?include=localizedContent', [
            'localizedContent' => $this->pageData['localizedContent'],
        ]);

        // Assert
        $response->assertStatus(201);
        $this->assertDatabaseHas('pages', [
            'name' => 'Kopia av Den första startsidan',
            'template_id' => 1,
        ]);
        $response->assertJson([
            'data' => [
                'localizedContent' => [
                    'data' => [
                        'sv' => [
                            'content' => [
                                'page_title' => 'Att klona',
                                'boxes' => [
                                    [
                                        'name' => 'El blocko',
                                        'block_type' => [
                                            'name' => 'Demo-block',
                                            'has_children' => true,
                                        ],
                                        'children' => [
                                            [
                                                'id' => 'if0i8d5',
                                                'hasImage' => true,
                                                'image' => [
                                                    'id' => 12,
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
