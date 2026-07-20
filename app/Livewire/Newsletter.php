<?php

namespace App\Livewire;

use App\Mail\NewsletterConfirmMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Inscription à la newsletter avec DOUBLE OPT-IN : on enregistre l'email
 * (non confirmé) puis on envoie un lien de confirmation signé. L'abonnement
 * n'est actif qu'après clic. Anti-bot : honeypot + limitation de débit
 * (protège le quota d'emails et empêche le flood d'abonnés fantômes).
 */
class Newsletter extends Component
{
    #[Validate('required|email|max:255')]
    public string $email = '';

    public string $website = ''; // honeypot (doit rester vide)

    public bool $done = false;

    public function subscribe(): void
    {
        // Honeypot : rempli → bot. On mime un succès sans rien envoyer.
        if (filled($this->website)) {
            $this->done = true;
            return;
        }

        $this->validate();

        // Limite : 5 tentatives / heure / IP.
        $key = 'newsletter:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->done = true;
            return;
        }
        RateLimiter::hit($key, 3600);

        $email = mb_strtolower(trim($this->email));
        $subscriber = NewsletterSubscriber::firstOrCreate(['email' => $email]);

        // Déjà confirmé → on ne renvoie pas de mail (évite le spam / le quota).
        if (! $subscriber->verified_at) {
            $url = URL::temporarySignedRoute(
                'newsletter.confirm',
                now()->addHours(48),
                ['subscriber' => $subscriber->id],
            );
            Mail::to($email)->send(new NewsletterConfirmMail($url, 48));
        }

        $this->done = true;
        $this->reset('email');
    }

    public function render()
    {
        return view('livewire.newsletter');
    }
}
