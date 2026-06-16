<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $otp;
    public int $expirationMinutes;

    /**
     * Create a new message instance.
     */
    public function __construct(string $otp)
    {
        $this->otp = $otp;
        $this->expirationMinutes = 10;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Código de recuperación de contraseña')
            ->view('emails.password-reset-otp')
            ->with([
                'otp' => $this->otp,
                'expirationMinutes' => $this->expirationMinutes,
            ]);
    }
}
