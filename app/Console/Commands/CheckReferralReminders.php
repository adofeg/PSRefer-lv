<?php

namespace App\Console\Commands;

use App\Enums\ReferralStatus;
use App\Models\Referral;
use App\Models\User;
use App\Notifications\ReferralReminderNotification;
use Illuminate\Console\Command;

class CheckReferralReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'referrals:check-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for referrals with reminders today and notify admins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        // Find referrals in "ContactLater" status with reminder_date <= today
        $referrals = Referral::where('status', ReferralStatus::ContactLater->value)
            ->whereDate('reminder_date', '<=', $today)
            ->get();

        if ($referrals->isEmpty()) {
            $this->info('No reminders for today.');

            return;
        }

        $admins = User::role(['admin', 'psadmin'])->get();

        foreach ($referrals as $referral) {
            $this->info("Notifying for referral: {$referral->id}");

            foreach ($admins as $admin) {
                $admin->notify(new ReferralReminderNotification($referral));
            }

            // Automatically return to "Prospect" status so it appears in the active pipeline
            $referral->update([
                'status' => ReferralStatus::Prospect->value,
                'reminder_date' => null, // Clear reminder date once notified
            ]);
        }

        $this->info('All reminders processed.');
    }
}
