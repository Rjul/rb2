<?php

namespace App\Console\Commands;

use App\Mail\WeeklyNewsletterMail;
use App\Models\Emision;
use App\Models\NewsletterSubscriber;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * Newsletter hebdomadaire, envoyée par VAGUES pour respecter le quota quotidien
 * du fournisseur (Brevo gratuit = 300/jour).
 *
 * Mode automatique (cron OVH « toutes les heures » → cron/newsletter.php) :
 *   - fenêtre : vendredi, samedi ou dimanche, à partir de 8h (heure de Paris) ;
 *   - UNE vague par jour maximum (journal `newsletter_logs`, une ligne/jour) ;
 *   - chaque vague envoie jusqu'à {@see self::DAILY_CAP} emails aux abonnés
 *     confirmés qui n'ont PAS encore reçu l'édition de la semaine (`last_sent_week`).
 *   → 250 abonnés partent le vendredi ; s'il y en a plus, la suite part samedi,
 *     puis dimanche. Personne ne reçoit deux fois la même édition.
 *
 * Options :
 *   --to=adresse : envoi de test du template (aucun effet de bord)
 *   --dry        : n'envoie rien, affiche l'état (restants, vague du jour…)
 *   --force      : déclenche une vague maintenant (ignore la fenêtre et le 1×/jour)
 *   --limit=N    : plafonne cette vague à N emails (défaut : DAILY_CAP)
 */
class SendWeeklyNewsletter extends Command
{
    protected $signature = 'newsletter:send-weekly
                            {--to= : Envoyer uniquement à cette adresse (test du template)}
                            {--dry : N\'envoie rien, affiche l\'état}
                            {--force : Envoie une vague maintenant (ignore la fenêtre vendredi/8h et le 1×/jour)}
                            {--limit= : Plafond d\'emails pour cette vague (défaut 250)}';

    protected $description = 'Envoie la newsletter hebdomadaire par vagues (à la une de la semaine) aux abonnés confirmés.';

    /** Plafond d'emails par vague/jour — marge sous les 300/jour de Brevo (OTP inclus). */
    public const DAILY_CAP = 250;

    public function handle(): int
    {
        $now   = Carbon::now('Europe/Paris');
        $week  = $now->format('o-\WW'); // ex. "2026-W30" (identique vendredi→dimanche : même semaine ISO)
        $today = $now->toDateString();
        $cap   = (int) ($this->option('limit') ?: self::DAILY_CAP);

        $isPreview = $this->option('to') || $this->option('dry');
        $auto      = ! $isPreview && ! $this->option('force');

        // --- Test du template : une seule adresse, aucun effet de bord ---
        if ($to = $this->option('to')) {
            $emissions = Emision::getWeeklyHighlights(6);
            if ($emissions->isEmpty()) {
                $this->warn('Aucune émission à mettre en avant.');
                return self::SUCCESS;
            }
            Mail::to($to)->send(new WeeklyNewsletterMail(
                $emissions,
                URL::signedRoute('newsletter.unsubscribe', ['subscriber' => 0]),
                $this->weekLabel($now),
            ));
            $this->info("Newsletter de test envoyée à {$to}.");
            return self::SUCCESS;
        }

        // --- Fenêtre + une vague/jour (mode automatique uniquement) ---
        if ($auto) {
            $inWindow = ($now->isFriday() || $now->isSaturday() || $now->isSunday()) && $now->hour >= 8;
            if (! $inWindow) {
                $this->line('Hors fenêtre d\'envoi (vendredi→dimanche à partir de 8h) — ignoré.');
                return self::SUCCESS;
            }
            if (DB::table('newsletter_logs')->where('date', $today)->exists()) {
                $this->line("Une vague a déjà été envoyée aujourd'hui ({$today}) — ignoré.");
                return self::SUCCESS;
            }
        }

        // Abonnés confirmés, non désinscrits, PAS encore servis cette semaine.
        $pending = NewsletterSubscriber::query()
            ->whereNotNull('verified_at')
            ->whereNull('unsubscribed_at')
            ->where(fn ($q) => $q->whereNull('last_sent_week')->orWhere('last_sent_week', '!=', $week));

        $remaining = $pending->count();

        if ($this->option('dry')) {
            $this->info("[dry] Semaine {$week} : {$remaining} abonné(s) restant(s) à servir, vague plafonnée à {$cap}.");
            return self::SUCCESS;
        }

        if ($remaining === 0) {
            $this->line("Semaine {$week} : tous les abonnés confirmés ont déjà reçu l'édition — rien à envoyer.");
            return self::SUCCESS;
        }

        $emissions = Emision::getWeeklyHighlights(6);
        if ($emissions->isEmpty()) {
            $this->warn('Aucune émission à mettre en avant — newsletter non envoyée.');
            return self::SUCCESS;
        }

        $label = $this->weekLabel($now);
        $batch = $pending->orderBy('id')->limit($cap)->get();
        $sent  = 0;

        foreach ($batch as $sub) {
            $url = URL::signedRoute('newsletter.unsubscribe', ['subscriber' => $sub->id]);
            Mail::to($sub->email)->send(new WeeklyNewsletterMail($emissions, $url, $label));
            $sub->update(['last_sent_week' => $week]);
            $sent++;
        }

        // Journalise la vague du jour (empêche une 2e vague le même jour en auto).
        DB::table('newsletter_logs')->updateOrInsert(
            ['date' => $today],
            ['week' => $week, 'recipients' => $sent, 'sent_at' => now(), 'updated_at' => now(), 'created_at' => now()],
        );

        $leftover = max(0, $remaining - $sent);
        $this->info("Vague envoyée : {$sent} email(s) — semaine {$week}."
            . ($leftover > 0 ? " Reste {$leftover} abonné(s) pour la prochaine vague (demain)." : ' Tous servis.'));

        return self::SUCCESS;
    }

    private function weekLabel(Carbon $now): string
    {
        return 'du ' . $now->copy()->subDays(6)->format('d/m') . ' au ' . $now->format('d/m');
    }
}
