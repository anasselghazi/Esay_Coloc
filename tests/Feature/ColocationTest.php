<?php

namespace Tests\Feature;

use App\Models\Colocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ColocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_colocation(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post('/colocations', [
                'name' => 'My House',
            ]);

        $colocation = Colocation::first();

        $response->assertRedirect(route('colocations.show', $colocation));
        $this->assertNotNull($colocation);
        $this->assertSame('My House', $colocation->nom);
        $this->assertSame($user->id, $colocation->owner_id);

        // pivot record exists with owner role
        $this->assertDatabaseHas('colocation_user', [
            'colocation_id' => $colocation->id,
            'user_id' => $user->id,
            'role' => 'owner',
        ]);
    }

    public function test_user_cannot_have_two_active_colocations(): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->post('/colocations', ['name' => 'First']);

        $response = $this
            ->actingAs($user)
            ->post('/colocations', ['name' => 'Second']);

        $response->assertSessionHasErrors();
        $this->assertCount(1, Colocation::where('owner_id', $user->id)->get());
    }

    public function test_user_can_join_and_leave_a_colocation(): void
    {
        $owner = User::factory()->create();
        $colocation = Colocation::factory()->create(['owner_id' => $owner->id]);
        $colocation->member()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);

        $member = User::factory()->create();

        // join
        $this
            ->actingAs($member)
            ->post(route('colocations.join', $colocation));

        $this->assertDatabaseHas('colocation_user', [
            'colocation_id' => $colocation->id,
            'user_id' => $member->id,
            'role' => 'member',
            'left_at' => null,
        ]);

        // leave
        $this
            ->actingAs($member)
            ->post(route('colocations.leave', $colocation));

        $this->assertDatabaseMissing('colocation_user', [
            'colocation_id' => $colocation->id,
            'user_id' => $member->id,
            'left_at' => null,
        ]);

        $this->assertDatabaseHas('colocation_user', [
            'colocation_id' => $colocation->id,
            'user_id' => $member->id,
        // left_at is not null so we just assert the other fields
        ]);
    }

    public function test_only_owner_can_cancel_colocation(): void
    {
        $owner = User::factory()->create();
        $colocation = Colocation::factory()->create(['owner_id' => $owner->id]);

        // owner cancels
        $this
            ->actingAs($owner)
            ->post(route('colocations.cancel', $colocation));

        $this->assertSame('cancelled', $colocation->fresh()->status);

        // another user cannot
        $other = User::factory()->create();
        $response = $this
            ->actingAs($other)
            ->post(route('colocations.cancel', $colocation));
        $response->assertStatus(403);
    }

    public function test_owner_can_invite_and_user_accepts(): void
    {
        $owner = User::factory()->create();
        $colocation = Colocation::factory()->create(['owner_id' => $owner->id]);
        $colocation->member()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);

        $recipient = User::factory()->create();

        $this->actingAs($owner)
            ->post(route('invitations.store', $colocation), ['email' => $recipient->email]);

        $inv = $colocation->invitations()->first();
        $this->assertNotNull($inv);
        $this->assertDatabaseHas('invitations', ['email' => $recipient->email, 'status' => 'pending']);

        // simulate recipient clicking link
        $this->actingAs($recipient)
            ->post(route('invitations.accept'), [
                'token' => $inv->token,
                'email' => $recipient->email,
            ]);

        $this->assertDatabaseHas('colocation_user', [
            'colocation_id' => $colocation->id,
            'user_id' => $recipient->id,
        ]);
        $this->assertSame('accepted', $inv->fresh()->status);
    }

    public function test_expense_and_settlement_calculation(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $colocation = Colocation::factory()->create(['owner_id' => $owner->id]);
        $colocation->member()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $colocation->member()->attach($member->id, ['role' => 'member', 'joined_at' => now()]);

        // two expenses, one by owner 100, one by member 50
        $colocation->expenses()->create(["payer_id" => $owner->id,"title"=>"A","amount"=>100,"expense_date"=>now(), 'category_id' => null]);
        $colocation->expenses()->create(["payer_id" => $member->id,"title"=>"B","amount"=>50,"expense_date"=>now(), 'category_id' => null]);

        // calculate balances via controller
        $settCtrl = new \App\Http\Controllers\SettlementController();
        $result = \Closure::bind(function() use ($colocation, $settCtrl) {
            return $settCtrl->calculateSettlements($colocation);
        }, null, $settCtrl)();

        // each share = 75, owner paid 100 => +25, member paid 50 => -25
        $this->assertEquals(25, $result['balances'][$owner->id]);
        $this->assertEquals(-25, $result['balances'][$member->id]);
        $this->assertCount(1, $result['settlements']);
        $this->assertEquals($member->id, $result['settlements'][0]['from']);
        $this->assertEquals($owner->id, $result['settlements'][0]['to']);
        $this->assertEquals(25, $result['settlements'][0]['amount']);
    }

    public function test_reputation_changes_on_leave_with_debt(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $colocation = Colocation::factory()->create(['owner_id' => $owner->id]);
        $colocation->member()->attach($owner->id, ['role' => 'owner', 'joined_at' => now()]);
        $colocation->member()->attach($member->id, ['role' => 'member', 'joined_at' => now()]);

        // add expense only owner paid 100
        $colocation->expenses()->create(["payer_id" => $owner->id,"title"=>"A","amount"=>100,"expense_date"=>now()]);

        // member owes 50, leaves
        $this->actingAs($member)
            ->post(route('colocations.leave', $colocation));

        $member->refresh();
        $this->assertLessThan(0, $member->reputation);
    }
}

