<?php

namespace App\Mail;

use App\Models\EmailOtp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email transactionnel : code de vérification (OTP) à l'inscription.
 * Template Blade brandé Radio Bastides (resources/views/emails/otp-code.blade.php).
 */
class OtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public string $name = '',
        public int $ttl = EmailOtp::TTL_MINUTES,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Votre code de vérification — Radio Bastides')
            ->view('emails.otp-code');
    }
}
