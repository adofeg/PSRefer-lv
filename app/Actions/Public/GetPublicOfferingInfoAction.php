<?php

namespace App\Actions\Public;

use App\Models\Offering;
use App\Services\OfferingSchemaService;

class GetPublicOfferingInfoAction
{
    public function __construct(
        protected OfferingSchemaService $schemaService
    ) {}

    public function execute(int $offeringId): array
    {
        $offering = Offering::query()
            ->with('category:id,name')
            ->where('id', $offeringId)
            ->where('is_active', true)
            ->firstOrFail(['id', 'name', 'description', 'category', 'category_id', 'form_schema']);

        return [
            'id' => (int) $offering->id,
            'name' => $offering->name,
            'description' => $offering->description,
            'category' => $offering->category_id ? $offering->category?->name : $offering->category,
            'form_schema' => $this->schemaService->getSchemaForOffering($offering->form_schema),
        ];
    }
}
