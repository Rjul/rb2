<?php

namespace App\Console\Commands;

use App\Mail\WeeklyNewsletterMail;
use App\Models\Emision;
use App\Models\NewsletterSubscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * Newsletter hebdomadaire : envoie « à la une de la semaine » aux abonnés
 * CONFIRMÉS et NON désinscrits. Chaque email a son propre lien de désinscription
 * signé. Ne rien envoyer s'il n'y a aucune émission à mettre en avant.
 *
 * Test manuel :   php artisan newsletter:send-weekly --to=moi@example.com
 * Vérif à blanc : php artisan newsletter:send-weekly --dry
 */
class SendWeeklyNewsletter extends Command
{
    protected $signature = 'newsletter:send-weekly
                            {--to= : Envoyer uniquement à cette adresse (test du template)}
                            {--dry : N\'envoie rien, affiche seulement le nombre de destinataires}';

    protected $description = 'Envoie la newsletter hebdomadaire (à la une de la semaine) aux abonnés confirmés.';

    public function handle(): int
    {
        $emissions = Emision::getWeeklyHighlights(6);

        if ($emissions->isEmpty()) {
            $this->warn('Aucune émission à mettre en avant — newsletter non envoyée.');
            return self::SUCCESS;
        }

        $label = 'du ' . now()->subDays(6)->format('d/m') . ' au ' . now()->format('d/m');

        // --- Envoi de test à une seule adresse ---
        if ($to = $this->option('to')) {
            $url = URL::signedRoute('newsletter.unsubscribe', ['subscriber' => 0]);
            Mail::to($to)->send(new WeeklyNewsletterMail($emissions, $url, $label));
            $this->info("Newsletter de test envoyée à {$to} ({$emissions->count()} émission(s)).");
            return self::SUCCESS;
        }

        $query = NewsletterSubscriber::query()
            ->whereNotNull('verified_at')
            ->whereNull('unsubscribed_at');

        $total = $query->count();

        if ($this->option('dry')) {
            $this->info("[dry] {$total} destinataire(s) — {$emissions->count()} émission(s) à la une.");
            return self::SUCCESS;
        }

        if ($total === 0) {
            $this->warn('Aucun abonné confirmé — rien à envoyer.');
            return self::SUCCESS;
        }

        $sent = 0;
        // chunkById : ne charge pas tous les abonnés en mémoire d'un coup.
        $query->orderBy('id')->chunkById(100, function ($subscribers) use ($emissions, $label, &$sent) {
            foreach ($subscribers as $sub) {
                $url = URL::signedRoute('newsletter.unsubscribe', ['subscriber' => $sub->id]);
                Mail::to($sub->email)->send(new WeeklyNewsletterMail($emissions, $url, $label));
                $sent++;
            }
        });

        $this->info("Newsletter envoyée à {$sent} abonné(s) confirmé(s).");

        return self::SUCCESS;
    }
}
