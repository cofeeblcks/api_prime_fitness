<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public ?string $senderPhone,
        public string $inquiryMessage,
        public string $companyName,
    ) {}

    public function build(): self
    {
        return $this
            ->replyTo($this->senderEmail, $this->senderName)
            ->subject("Nuevo contacto desde la landing — {$this->senderName}")
            ->view('emails.contact-inquiry');
    }
}
