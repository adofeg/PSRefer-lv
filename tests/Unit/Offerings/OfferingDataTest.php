<?php

namespace Tests\Unit\Offerings;

use App\Data\Offerings\OfferingData;
use Tests\TestCase;

class OfferingDataTest extends TestCase
{
    public function test_it_can_be_instantiated_from_array(): void
    {
        $data = [
            'id' => 123,
            'name' => 'Premium Service',
            'type' => 'service',
            'category' => 'consulting',
            'description' => 'A great service',
            'base_price' => 150.00,
            'commission_rate' => 10.0,
            'is_active' => true,
        ];

        $offering = OfferingData::from($data);

        $this->assertInstanceOf(OfferingData::class, $offering);
        $this->assertEquals('Premium Service', $offering->name);
        $this->assertEquals(150.00, $offering->base_price);
        $this->assertTrue($offering->is_active);
    }

    public function test_it_handles_null_optional_fields(): void
    {
        $data = [
            'id' => null,
            'name' => 'Basic Service',
            'type' => 'product',
            'category' => null,
            'description' => null,
            'base_price' => null,
            'commission_rate' => 5.0,
        ];

        $offering = OfferingData::from($data);

        $this->assertNull($offering->id);
        $this->assertNull($offering->category);
        $this->assertTrue($offering->is_active); // Default value
    }
}
