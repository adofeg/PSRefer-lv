<?php

namespace App\Actions\Public;

use App\Models\Offering;

class GetPublicOfferingInfoAction
{
    public function execute(int $offeringId): array
    {
        $offering = Offering::query()
            ->with('category:id,name')
            ->where('id', $offeringId)
            ->where('is_active', true)
            ->firstOrFail(['id', 'name', 'description', 'category', 'category_id', 'form_schema', 'base_price']);

        return [
            'id' => (int) $offering->id,
            'name' => $offering->name,
            'description' => $offering->description,
            'category' => $offering->category_id ? $offering->category?->name : $offering->category,
            'form_schema' => $offering->form_schema,
            'base_price' => $offering->base_price,
        ];
    }
}