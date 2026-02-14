<?php

namespace App\Actions\Offerings;

use App\Data\Offerings\OfferingUpsertData;
use App\Models\Offering;

class CreateOfferingAction
{
    public function __construct(
        protected \App\Services\AuditService $auditService
    ) {}

    public function execute(OfferingUpsertData $data, int $ownerEmployeeId): Offering
    {
        $offering = Offering::create([
            'name' => $data->name,
            'category_id' => $data->category_id,
            'category' => $data->category,
            'type' => $data->type ?? 'service',
            'description' => $data->description,
            'base_price' => $data->base_price,
            'base_commission' => $data->base_commission,
            'commission_rate' => $data->commission_rate,
            'form_schema' => $data->form_schema,
            'commission_config' => $data->commission_config,
            'commission_rules' => $data->commission_rules,
            'notification_emails' => $data->notification_emails,
            'owner_employee_id' => $ownerEmployeeId,
            'is_active' => true,
        ]);

        $this->auditService->logAction(
            $offering,
            'CREATE',
            "Offering '{$offering->name}' created",
            null,
            ['name' => $offering->name, 'type' => $offering->type]
        );

        return $offering;
    }
}
