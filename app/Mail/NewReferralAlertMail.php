<?php

namespace App\Mail;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewReferralAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Referral $referral,
        public User $referrer
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 Nuevo Referido Recibido: ' . $this->referral->client_name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.referrals.new-alert',
            with: [
                'clientName' => $this->referral->client_name,
                'offeringName' => $this->referral->offering?->name ?? 'General',
                'referrerName' => $this->referrer->name,
                'notes' => $this->referral->notes,
                'url' => url('/admin/referrals/' . $this->referral->id),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
