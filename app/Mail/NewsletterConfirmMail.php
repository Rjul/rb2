<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email transactionnel : lien de confirmation (double opt-in) de la newsletter.
 * L'URL est une URL signée à durée limitée (voir NewsletterController::confirm).
 */
class NewsletterConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $url,
        public int $ttlHours = 48,
    ) {}

    public function build(): self
    {
        return $this
            ->subject('Confirmez votre inscription à la newsletter — Radio Bastides')
            ->view('emails.newsletter-confirm');
    }
}
