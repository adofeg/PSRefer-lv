<?php

namespace App\Console\Commands;

use App\Enums\CommissionStatus;
use App\Models\Commission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProcessRecurringCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commissions:process-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process monthly recurring commissions (Migration Parity)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for due recurring commissions...');

        // Find parent commissions that are "recurring", "paid", and due for next payout
        // A commission is due if it was created more than 1 month ago
        // AND no child commission has been created in the last 1 month.
        $parents = Commission::where('recurrence_type', 'recurring')
            ->where('status', CommissionStatus::Paid->value)
            ->where(function ($query) {
                $query->whereNull('recurrence_end_date')
                    ->orWhere('recurrence_end_date', '>', now());
            })
            ->where('created_at', '<', now()->subMonth())
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('commissions as child')
                    ->whereColumn('child.parent_commission_id', 'commissions.id')
                    ->where('child.created_at', '>', now()->subMonth());
            })
            ->get();

        $count = 0;
        foreach ($parents as $parent) {
            DB::transaction(function () use ($parent, &$count) {
                $newCommission = Commission::create([
                    'referral_id' => $parent->referral_id,
                    'associate_id' => $parent->associate_id,
                    'amount' => $parent->amount,
                    'commission_percentage' => 0,
                    'commission_type' => 'monthly',
                    'recurrence_type' => 'recurring',
                    'recurrence_interval' => 'monthly',
                    'recurrence_end_date' => $parent->recurrence_end_date,
                    'parent_commission_id' => $parent->id,
                    'status' => CommissionStatus::Pending->value, // New ones start as pending
                ]);
                $count++;
            });
        }

        $this->info("Successfully created {$count} new recurring commission entries.");
    }
}
