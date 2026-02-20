<?php

namespace App\Actions\Commissions;

use App\Models\Commission;
use App\Enums\CommissionStatus;
use Illuminate\Support\Facades\DB;

use App\Models\FileAsset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdateCommissionAction
{
    public function execute(Commission $commission, array $data): Commission
    {
        return DB::transaction(function () use ($commission, $data) {
            $originalStatus = $commission->status;
            $originalAmount = $commission->amount;

            $updateData = [
                'associate_id' => $data['associate_id'],
                'referral_id' => $data['referral_id'] ?? null,
                'amount' => $data['amount'],
                'commission_type' => $data['commission_type'],
                'status' => $data['status'],
                'metadata' => array_merge($commission->metadata ?? [], ['notes' => $data['notes'] ?? '']),
            ];

            if (isset($data['receipt_file'])) {
                // Remove old receipt if exists
                if ($commission->receipt) {
                    Storage::disk('public')->delete($commission->receipt->path);
                    $commission->receipt->delete();
                }

                $file = $data['receipt_file'];
                $path = $file->store('receipts', 'public');
                
                // Create FileAsset
                $commission->receipt()->create([
                    'uuid' => (string) Str::uuid(),
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'purpose' => 'receipt',
                    'category' => 'financial',
                    'uploaded_by' => auth()->id(),
                ]);
            }

            $commission->update($updateData);

            // Refined Logic for Balance Updates:
            $effectOnBalance = 0;

            // Revert old state
            if ($originalStatus === CommissionStatus::Paid->value) {
                $effectOnBalance -= $originalAmount;
            }

            // Apply new state
            if ($data['status'] === CommissionStatus::Paid->value) {
                $effectOnBalance += $data['amount'];
                if (!$commission->paid_at) {
                    $commission->update(['paid_at' => now()]);
                }
            }

            if ($effectOnBalance != 0) {
                if ($effectOnBalance > 0) {
                    $commission->associate()->increment('balance', $effectOnBalance);
                } else {
                    $commission->associate()->decrement('balance', abs($effectOnBalance));
                }
            }

            return $commission->refresh();
        });
    }
}
