<?php

namespace App\Actions\Offerings;

use App\Data\Offerings\OfferingUpsertData;
use App\Models\Offering;

class UpdateOfferingAction
{
    public function __construct(
        protected \App\Services\AuditService $auditService
    ) {}

    public function execute(Offering $offering, OfferingUpsertData $data): Offering
    {
        $oldData = $offering->only(['name', 'base_price', 'commission_rate', 'is_active']);

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
            'notification_emails' => $data->notification_emails,
            'is_active' => $data->is_active ?? $offering->is_active,
        ]);

        $this->auditService->logAction(
            $offering,
            'UPDATE',
            "Offering '{$offering->name}' updated",
            $oldData,
            $offering->only(['name', 'base_price', 'commission_rate', 'is_active'])
        );

        return $offering->refresh();
    }

    public function updateStatus(Offering $offering, bool $isActive): Offering
    {
        $oldStatus = $offering->is_active;
        $offering->update(['is_active' => $isActive]);

        $this->auditService->logAction(
            $offering,
            'UPDATE',
            "Offering '{$offering->name}' status changed to ".($isActive ? 'Active' : 'Inactive'),
            ['is_active' => $oldStatus],
            ['is_active' => $isActive]
        );

        return $offering->refresh();
    }
}
