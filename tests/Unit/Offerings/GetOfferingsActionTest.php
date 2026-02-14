<?php

namespace Tests\Unit\Offerings;

use App\Actions\Offerings\GetOfferingsAction;
use App\Enums\RoleName;
use App\Models\Associate;
use App\Models\Category;
use App\Models\Offering;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetOfferingsActionTest extends TestCase
{
    use RefreshDatabase;

    private GetOfferingsAction $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new GetOfferingsAction();
    }

    public function test_it_excludes_offerings_from_associates_own_category_by_name()
    {
        // 1. Create a category
        $category = Category::factory()->create(['name' => 'Marketing']);

        // 2. Create offerings
        $offeringInCat = Offering::factory()->create(['category' => 'Marketing', 'is_active' => true]);
        $offeringOther = Offering::factory()->create(['category' => 'Legal', 'is_active' => true]);

        // 3. Create associate user with category 'Marketing'
        $associate = Associate::factory()->create(['category' => 'Marketing']);
        $user = User::factory()->create([
            'profileable_id' => $associate->id,
            'profileable_type' => Associate::class,
        ]);
        $user->assignRole(RoleName::Associate->value);

        // 4. Execute action
        $results = $this->action->execute($user, false, [], false);

        // 5. Assertions
        $this->assertTrue($results->contains('id', $offeringOther->id));
        $this->assertFalse($results->contains('id', $offeringInCat->id));
    }

    public function test_it_excludes_offerings_from_associates_own_category_by_id()
    {
        // 1. Create categories
        $category1 = Category::factory()->create(['name' => 'Marketing']);
        $category2 = Category::factory()->create(['name' => 'Legal']);

        // 2. Create offerings using category_id
        $offeringInCat = Offering::factory()->create(['category_id' => $category1->id, 'is_active' => true]);
        $offeringOther = Offering::factory()->create(['category_id' => $category2->id, 'is_active' => true]);

        // 3. Create associate user with category 'Marketing'
        // Using relationship logic (profile->category)
        $associate = Associate::factory()->create(['category' => 'Marketing']);
        $user = User::factory()->create([
            'profileable_id' => $associate->id,
            'profileable_type' => Associate::class,
        ]);
        $user->assignRole(RoleName::Associate->value);

        // 4. Execute action
        $results = $this->action->execute($user, false, [], false);

        // 5. Assertions
        $this->assertTrue($results->contains('id', $offeringOther->id));
        $this->assertFalse($results->contains('id', $offeringInCat->id));
    }

    public function test_it_excludes_inactive_offerings_for_associates()
    {
        $activeOff = Offering::factory()->create(['is_active' => true]);
        $inactiveOff = Offering::factory()->create(['is_active' => false]);

        $associate = Associate::factory()->create();
        $user = User::factory()->create([
            'profileable_id' => $associate->id,
            'profileable_type' => Associate::class,
        ]);
        $user->assignRole(RoleName::Associate->value);

        $results = $this->action->execute($user, false, [], false);

        $this->assertTrue($results->contains('id', $activeOff->id));
        $this->assertFalse($results->contains('id', $inactiveOff->id));
    }

    public function test_admins_can_see_all_offerings()
    {
        $offering1 = Offering::factory()->create(['is_active' => true]);
        $offering2 = Offering::factory()->create(['is_active' => false]);

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $results = $this->action->execute($admin, true, [], false);

        $this->assertEquals(2, $results->count());
        $this->assertTrue($results->contains('id', $offering1->id));
        $this->assertTrue($results->contains('id', $offering2->id));
    }
}
