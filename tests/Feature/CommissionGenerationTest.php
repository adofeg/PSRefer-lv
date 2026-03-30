<?php

use App\Enums\ReferralStatus;
use App\Models\Associate;
use App\Models\Category;
use App\Models\Offering;
use App\Models\Referral;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->associateUser = User::factory()->create(['role' => 'associate']);
    $this->associate = Associate::factory()->create(['user_id' => $this->associateUser->id]);
    
    // Create base data
    $this->category = Category::create(['name' => 'Test Category', 'slug' => 'test-category']);
    $this->sector = Sector::create(['name' => 'Contadores', 'slug' => 'contadores']);
});

test('it creates a fixed commission when referral is closed', function () {
    $offering = Offering::factory()->create([
        'category_id' => $this->category->id,
        'commission_type' => 'fixed',
        'base_commission' => 50.00,
    ]);

    $referral = Referral::factory()->create([
        'associate_id' => $this->associate->id,
        'offering_id' => $offering->id,
        'sector_id' => $this->sector->id,
        'status' => ReferralStatus::InProcess->value,
    ]);

    $data = new \App\Data\Referrals\ReferralStatusUpdateData(
        status: ReferralStatus::Closed,
        notes: 'Test closing'
    );

    $action = app(\App\Actions\Referrals\UpdateReferralStatusAction::class);
    $action->execute($referral, $data, $this->admin);

    $this->assertDatabaseHas('commissions', [
        'referral_id' => $referral->id,
        'associate_id' => $this->associate->id,
        'commission_type' => 'fixed',
        'amount' => 50.00,
        'status' => 'pending',
    ]);
});

test('it creates a zero dollar variable commission when referral is closed', function () {
    $offering = Offering::factory()->create([
        'category_id' => $this->category->id,
        'commission_type' => 'variable',
        'base_commission' => 0.00,
    ]);

    $referral = Referral::factory()->create([
        'associate_id' => $this->associate->id,
        'offering_id' => $offering->id,
        'sector_id' => $this->sector->id,
        'status' => ReferralStatus::InProcess->value,
    ]);

    $data = new \App\Data\Referrals\ReferralStatusUpdateData(
        status: ReferralStatus::Closed,
        notes: 'Test closing'
    );

    $action = app(\App\Actions\Referrals\UpdateReferralStatusAction::class);
    $action->execute($referral, $data, $this->admin);

    $this->assertDatabaseHas('commissions', [
        'referral_id' => $referral->id,
        'associate_id' => $this->associate->id,
        'commission_type' => 'variable',
        'amount' => 0.00,
        'status' => 'pending',
    ]);
});
