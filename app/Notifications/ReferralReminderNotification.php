<?php

namespace App\Notifications;

use App\Models\Referral;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReferralReminderNotification extends Notification
{
    use Queueable;

    public function __construct(public Referral $referral) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $baseUrl = match ($notifiable->role) {
            'admin', 'psadmin' => route('admin.referrals.show', $this->referral->id),
            default => route('associate.referrals.index'),
        };

        return [
            'referral_id' => $this->referral->id,
            'client_name' => $this->referral->client_name,
            'message' => "Agenda: Hoy debes contactar a {$this->referral->client_name}.",
            'type' => 'agenda',
            'url' => $baseUrl,
        ];
    }
}
