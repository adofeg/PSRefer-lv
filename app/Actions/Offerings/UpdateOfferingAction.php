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
        $oldData = $offering->only(['name', 'base_commission', 'commission_type', 'is_active']);

        // Handle Schema Versioning
        $currentSchema = $offering->form_schema;
        $newSchema = $data->form_schema;
        
        // If schema content changed (ignoring version/revision fields for comparison)
        $currentGroups = $currentSchema['groups'] ?? [];
        $newGroups = $newSchema['groups'] ?? [];
        
        if (json_encode($currentGroups) !== json_encode($newGroups)) {
            // Increment Version
            $currentVersion = (int)($currentSchema['version'] ?? 1);
            $newVersion = $currentVersion + 1;
            
            $newSchema['version'] = $newVersion;
            
            // Save History
            $history = $currentSchema['history'] ?? [];
            $history[] = [
                'version' => $currentVersion,
                'groups' => $currentGroups,
                'archived_at' => now()->toIso8601String(),
            ];
            $newSchema['history'] = $history;
        } else {
            // Restore existing version/history if no change in groups
            $newSchema['version'] = $currentSchema['version'] ?? 1;
            $newSchema['history'] = $currentSchema['history'] ?? [];
        }

        $offering->update([
            'name' => $data->name,
            'category_id' => $data->category_id,
            'category' => $data->category,
            'description' => $data->description,
            'base_commission' => $data->base_commission,
            'commission_type' => $data->commission_type,
            'form_schema' => $newSchema,
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
            $offering->only(['name', 'base_commission', 'commission_type', 'is_active'])
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
