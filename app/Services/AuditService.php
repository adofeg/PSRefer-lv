<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Log;

class AuditService
{
  const ACTION_CREATE = 'CREATE';
  const ACTION_UPDATE = 'UPDATE';
  const ACTION_DELETE = 'DELETE';
  const ACTION_STATUS_CHANGE = 'STATUS_CHANGE';

  const ENTITY_USER = 'USER';
  const ENTITY_REFERRAL = 'REFERRAL';
  const ENTITY_OFFERING = 'OFFERING';
  const ENTITY_COMMISSION = 'COMMISSION';

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
        'user_id' => $data['user_id'],
        'previous_data' => $data['previous_data'] ?? null,
        'new_data' => $data['new_data'] ?? null,
        'description' => $data['description'] ?? '',
        'created_at' => now(),
      ]);
    } catch (\Exception $e) {
      Log::error('[AUDIT] Failed to create audit log: ' . $e->getMessage());
      return null;
    }
  }

  public function logReferralStatusChange($referralId, $userId, $previousStatus, $newStatus, $notes = '')
  {
    return $this->log([
      'entity' => self::ENTITY_REFERRAL,
      'entity_id' => $referralId,
      'action' => self::ACTION_STATUS_CHANGE,
      'user_id' => $userId,
      'previous_data' => ['status' => $previousStatus],
      'new_data' => ['status' => $newStatus],
      'description' => "Status changed from {$previousStatus} to {$newStatus}. {$notes}",
    ]);
  }

  public function logOfferingCreate($offeringId, $userId, $name)
  {
    return $this->log([
      'entity' => self::ENTITY_OFFERING,
      'entity_id' => $offeringId,
      'action' => self::ACTION_CREATE,
      'user_id' => $userId,
      'new_data' => ['name' => $name],
      'description' => "Offering \"{$name}\" created",
    ]);
  }
}
