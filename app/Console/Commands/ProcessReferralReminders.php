<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessReferralReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-referral-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move "Contact Later" referrals to "Prospecto" on their reminder date.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $count = \App\Models\Referral::query()
            ->where('status', 'Contactar más tarde')
            ->whereDate('reminder_date', '<=', now())
            ->update([
                'status' => 'Prospecto',
                'reminder_date' => null,
            ]);

        $this->info("Processed {$count} referral reminders.");
    }
}
