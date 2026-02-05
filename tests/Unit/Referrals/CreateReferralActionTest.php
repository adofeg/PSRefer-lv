<?php

namespace Tests\Unit\Referrals;

use Tests\TestCase;
use App\Actions\Referrals\CreateReferralAction;
use App\Data\Referrals\ReferralData;
use App\Models\Referral;
use App\Models\User;
use App\Models\Associate;
use App\Models\Offering;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateReferralActionTest extends TestCase
{
  use RefreshDatabase;

  public function test_it_creates_referral_correctly(): void
  {
    // ARRANGE
    // Ideally we use factories, but for Unit DTO we can just mock IDs if DB is mocked.
    // But Action uses DB::transaction and Referral::create, so it hits DB.
    // We need real User and Offering from Factories, or just IDs if no constraint checks (but usually there are FKs).
    // Since we are using RefreshDatabase with sqlite :memory:, we can create them.

    // However, User and Offering factories might not exist yet. Let's check or create minimal.
    // For now, let's try assuming they exist or create manual.

    $associate = Associate::factory()->create();
    $user = $associate->user()->create([
      'name' => 'Test Associate',
      'email' => 'associate@test.local',
      'password' => bcrypt('password'),
      'is_active' => true,
    ]);
    $user->assignRole('associate');
    // Offering factory? If not, create manual.
    // Let's rely on standard factories being available or create generic.
    // If Offering model exists, it might not have factory.

    // Quick check: does Offering has factory?
    // Proceeding with assumption we can create Offering manually if needed.

    $offering = Offering::create([
      'name' => 'Offer 1',
      'type' => 'service',
      'base_price' => 100,
      'commission_rate' => 10,
    ]);

    $data = new ReferralData(
      associate_id: $associate->id,
      offering_id: $offering->id,
      client_name: 'John Doe',
      client_contact: 'john@example.com',
      status: 'pending',
      metadata: ['source' => 'web'],
      notes: 'Test note'
    );

    // ACT
    $action = new CreateReferralAction();
    $referral = $action->execute($data);

    // ASSERT
    $this->assertInstanceOf(Referral::class, $referral);
    $this->assertDatabaseHas('referrals', [
      'id' => $referral->id,
      'client_name' => 'John Doe',
      'status' => 'pending',
    ]);
    $this->assertEquals($associate->id, $referral->associate_id);
  }
}
