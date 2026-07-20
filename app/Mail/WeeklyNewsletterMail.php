<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

/**
 * Newsletter hebdomadaire : la sélection « à la une » de la semaine.
 * Template Blade brandé Radio Bastides (emails/weekly-newsletter.blade.php).
 * $unsubscribeUrl est propre à chaque destinataire (lien signé).
 */
class WeeklyNewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $emissions,
        public string $unsubscribeUrl,
        public ?string $weekLabel = null,
    ) {}

    public function build(): self
    {
        $subject = $this->weekLabel
            ? "Radio Bastides — à la une {$this->weekLabel}"
            : 'Radio Bastides — la sélection de la semaine';

        return $this->subject($subject)->view('emails.weekly-newsletter');
    }
}
