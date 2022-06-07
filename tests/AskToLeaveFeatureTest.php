<?php

namespace Ikoncept\Fabriq\Tests\Feature;

use Ikoncept\Fabriq\Models\User;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;
use Ikoncept\Fabriq\Tests\AdminUserTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AskToLeaveFeatureTest extends AdminUserTestCase
{
    /** @test **/
    public function it_can_send_a_request_to_leave_the_room_to_another_user()
    {
        // Arrange
        $otherUser = User::factory()->create();
        dd($otherUser->toArray());

        // Act

        // Assert
    }

}
