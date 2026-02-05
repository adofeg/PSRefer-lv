<?php

namespace App\Actions\Offerings;

use App\Data\Offerings\OfferingUpsertData;
use App\Models\Offering;

class UpdateOfferingAction
{
    public function execute(Offering $offering, OfferingUpsertData $data): Offering
    {
        $offering->update([
            'name' => $data->name,
            'category_id' => $data->category_id,
            'category' => $data->category,
            'description' => $data->description,
            'base_price' => $data->base_price,
            'commission_rate' => $data->commission_rate,
            'form_schema' => $data->form_schema,
            'commission_config' => $data->commission_config,
            'commission_rules' => $data->commission_rules,
            'is_active' => $data->is_active ?? $offering->is_active,
        ]);

        return $offering->refresh();
    }
}
