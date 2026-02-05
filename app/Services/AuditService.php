<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Enums\AuditEntity;
use App\Models\AuditLog;
use App\Models\Referral;
use Exception;
use Illuminate\Support\Facades\Log;

class AuditService
{
  /**
   * Create an audit log entry
   */
  public function log(array $data)
  {
    try {
      return AuditLog::create([
        'entity' => $data['entity'],
        'entity_id' => $data['entity_id'],
        'action' => $data['action'],
        'actorable_type' => $data['actorable_type'] ?? null,
        'actorable_id' => $data['actorable_id'] ?? null,
        'previous_data' => $data['previous_data'] ?? null,
        'new_data' => $data['new_data'] ?? null,
        'description' => $data['description'] ?? '',
        'created_at' => now(),
      ]);
    } catch (Exception $e) {
      Log::error('[AUDIT] Failed to create audit log: ' . $e->getMessage());
      return null;
    }
  }

  public function logReferralStatusChange($referralId, $actor, $previousStatus, $newStatus, $notes = '')
  {
    return AuditLog::create([
      'auditable_type' => Referral::class,
      'auditable_id' => $referralId,
      'event_type' => 'status_change',
      'actorable_type' => $actor ? get_class($actor) : null,
      'actorable_id' => $actor?->id,
      'old_value' => $previousStatus,
      'new_value' => $newStatus,
      'metadata' => [
        'notes' => $notes,
        'note' => $notes, // Compatibility with frontend
      ],
      'created_at' => now(),
    ]);
  }

  public function logOfferingCreate($offeringId, $actor, $name)
  {
    return $this->log([
      'entity' => AuditEntity::Offering->value,
      'entity_id' => $offeringId,
      'action' => AuditAction::Create->value,
      'actorable_type' => $actor ? get_class($actor) : null,
      'actorable_id' => $actor?->id,
      'new_data' => ['name' => $name],
      'description' => "Offering \"{$name}\" created",
    ]);
  }
}
