<?php

namespace App\Livewire;

use App\Models\NewsletterSubscriber;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Inscription à la newsletter : enregistre l'email (table newsletter_subscribers).
 * La logique d'envoi hebdomadaire / double opt-in sera ajoutée plus tard.
 */
class Newsletter extends Component
{
    #[Validate('required|email|max:255')]
    public string $email = '';

    public bool $done = false;

    public function subscribe(): void
    {
        $this->validate();

        NewsletterSubscriber::firstOrCreate([
            'email' => mb_strtolower(trim($this->email)),
        ]);

        $this->done = true;
        $this->reset('email');
    }

    public function render()
    {
        return view('livewire.newsletter');
    }
}
