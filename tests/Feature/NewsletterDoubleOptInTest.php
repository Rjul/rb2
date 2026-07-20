<?php

namespace Tests\Feature;

use App\Livewire\Newsletter;
use App\Mail\NewsletterConfirmMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Newsletter en double opt-in : inscription non confirmée + lien signé,
 * anti-bot honeypot, confirmation par lien signé.
 */
class NewsletterDoubleOptInTest extends TestCase
{
    use RefreshDatabase;

    public function test_inscription_cree_un_abonne_non_confirme_et_envoie_le_lien(): void
    {
        Mail::fake();

        Livewire::test(Newsletter::class)
            ->set('email', 'Lecteur@Example.com')
            ->call('subscribe')
            ->assertSet('done', true);

        $sub = NewsletterSubscriber::where('email', 'lecteur@example.com')->first();
        $this->assertNotNull($sub);
        $this->assertNull($sub->verified_at, 'L\'abonné doit être NON confirmé tant que le lien n\'est pas cliqué');
        Mail::assertSent(NewsletterConfirmMail::class);
    }

    public function test_le_honeypot_bloque_les_bots(): void
    {
        Mail::fake();

        Livewire::test(Newsletter::class)
            ->set('email', 'bot@spam.com')
            ->set('website', 'rempli-par-un-bot')
            ->call('subscribe')
            ->assertSet('done', true);

        $this->assertDatabaseMissing('newsletter_subscribers', ['email' => 'bot@spam.com']);
        Mail::assertNothingSent();
    }

    public function test_le_lien_signe_confirme_l_abonnement(): void
    {
        $sub = NewsletterSubscriber::create(['email' => 'ok@example.com']);

        $url = URL::temporarySignedRoute('newsletter.confirm', now()->addHours(48), ['subscriber' => $sub->id]);

        $this->get($url)->assertOk();

        $this->assertNotNull($sub->fresh()->verified_at);
    }

    public function test_un_lien_non_signe_est_refuse(): void
    {
        $sub = NewsletterSubscriber::create(['email' => 'ok2@example.com']);

        $this->get(route('newsletter.confirm', $sub))->assertForbidden();

        $this->assertNull($sub->fresh()->verified_at);
    }
}
