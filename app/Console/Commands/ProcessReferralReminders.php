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
        $referrals = \App\Models\Referral::query()
            ->where('status', 'Contactar más tarde')
            ->whereDate('reminder_date', '<=', now())
            ->get();

        foreach ($referrals as $referral) {
            $referral->update([
                'status' => 'Prospecto',
                'reminder_date' => null,
            ]);

            // Notify Associate
            if ($referral->user_id) {
                $referral->user->notify(new \App\Notifications\ReferralReminderNotification($referral));
            }

            // Notify Admins
            $admins = \App\Models\User::whereIn('role', ['admin', 'psadmin'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\ReferralReminderNotification($referral));
            }
        }

        $this->info("Processed {$referrals->count()} referral reminders.");
    }
}
