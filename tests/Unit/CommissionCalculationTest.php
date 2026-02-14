<?php

namespace Tests\Unit;

use App\Actions\Financial\CalculateCommissionAction;
use App\Data\Offerings\OfferingData;
use Tests\TestCase;

class CommissionCalculationTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_it_calculates_commission_correctly(): void
    {
        // ARRANGE
        // Mock Offering Data (DTO)
        $offering = new OfferingData(
            id: 1,
            name: 'Test Offering',
            description: 'Desc',
            type: 'service',
            category: 'general',
            base_commission: 10.0, // 10%
            is_active: true
        );

        $transactionAmount = 500.0;

        // ACTION
        $action = new CalculateCommissionAction;
        $commission = $action->execute($offering, $transactionAmount);

        // ASSERT
        // 10% of 500.0 is 50.0
        $this->assertEquals(50.0, $commission);
    }

    public function test_it_returns_zero_if_base_commission_is_null(): void
    {
        $offering = new OfferingData(
            id: 2,
            name: 'Null Comm Offering',
            type: 'service',
            category: 'general',
            description: 'Desc',
            base_commission: null,
            is_active: true
        );

        $action = new CalculateCommissionAction;
        $commission = $action->execute($offering, 500.0);

        $this->assertEquals(0.0, $commission);
    }
}
