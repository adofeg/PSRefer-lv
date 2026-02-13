<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewReferralNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $referral;

    public function __construct($referral)
    {
        $this->referral = $referral;
    }

    public function build()
    {
        return $this->subject('Nuevo Referido Recibido: ' . $this->referral->offering->name)
                    ->markdown('emails.referrals.new_referral');
    }
}
