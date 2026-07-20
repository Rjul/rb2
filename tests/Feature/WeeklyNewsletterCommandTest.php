<?php

namespace Tests\Feature;

use App\Mail\WeeklyNewsletterMail;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\NewsletterSubscriber;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Commande d'envoi de la newsletter hebdomadaire.
 */
class WeeklyNewsletterCommandTest extends TestCase
{
    use RefreshDatabase;

    private function seedHighlight(): void
    {
        $user  = User::factory()->create();
        $group = GroupProgramme::factory()->create(['is_active' => true]);
        $prog  = Programme::factory()->create([
            'user_id' => $user->id, 'group_programme_id' => $group->id, 'is_active' => true,
        ]);
        Emision::factory()->create([
            'programme_id'   => $prog->id,
            'user_id'        => $user->id,
            'is_active'      => true,
            'is_put_forward' => true,
            'active_at'      => now()->subDay(),
            'media_type'     => 'audio',
        ]);
    }

    public function test_envoi_uniquement_aux_abonnes_confirmes_non_desinscrits(): void
    {
        Mail::fake();
        $this->seedHighlight();

        NewsletterSubscriber::create(['email' => 'confirme@x.fr', 'verified_at' => now()]);
        NewsletterSubscriber::create(['email' => 'non-confirme@x.fr']);
        NewsletterSubscriber::create(['email' => 'desinscrit@x.fr', 'verified_at' => now(), 'unsubscribed_at' => now()]);

        $this->artisan('newsletter:send-weekly')->assertSuccessful();

        Mail::assertSent(WeeklyNewsletterMail::class, 1);
        Mail::assertSent(WeeklyNewsletterMail::class, fn ($m) => $m->hasTo('confirme@x.fr'));
        Mail::assertNotSent(WeeklyNewsletterMail::class, fn ($m) => $m->hasTo('non-confirme@x.fr'));
        Mail::assertNotSent(WeeklyNewsletterMail::class, fn ($m) => $m->hasTo('desinscrit@x.fr'));
    }

    public function test_rien_envoye_sans_emission_a_la_une(): void
    {
        Mail::fake();
        NewsletterSubscriber::create(['email' => 'confirme@x.fr', 'verified_at' => now()]);

        $this->artisan('newsletter:send-weekly')->assertSuccessful();

        Mail::assertNothingSent();
    }

    public function test_option_to_envoie_un_seul_email_de_test(): void
    {
        Mail::fake();
        $this->seedHighlight();

        $this->artisan('newsletter:send-weekly', ['--to' => 'moi@x.fr'])->assertSuccessful();

        Mail::assertSent(WeeklyNewsletterMail::class, 1);
        Mail::assertSent(WeeklyNewsletterMail::class, fn ($m) => $m->hasTo('moi@x.fr'));
    }
}
