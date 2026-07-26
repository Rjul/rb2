<?php

namespace Tests\Feature;

use App\Mail\WeeklyNewsletterMail;
use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\NewsletterSubscriber;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Newsletter hebdomadaire : ciblage, fenêtre d'envoi, et envoi par VAGUES
 * (plafond quotidien réparti sur plusieurs jours).
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

    public function test_force_envoie_aux_confirmes_seulement(): void
    {
        Mail::fake();
        $this->seedHighlight();

        NewsletterSubscriber::create(['email' => 'confirme@x.fr', 'verified_at' => now()]);
        NewsletterSubscriber::create(['email' => 'non-confirme@x.fr']);
        NewsletterSubscriber::create(['email' => 'desinscrit@x.fr', 'verified_at' => now(), 'unsubscribed_at' => now()]);

        $this->artisan('newsletter:send-weekly', ['--force' => true])->assertSuccessful();

        Mail::assertSent(WeeklyNewsletterMail::class, 1);
        Mail::assertSent(WeeklyNewsletterMail::class, fn ($m) => $m->hasTo('confirme@x.fr'));
    }

    public function test_rien_sans_emission(): void
    {
        Mail::fake();
        NewsletterSubscriber::create(['email' => 'confirme@x.fr', 'verified_at' => now()]);

        $this->artisan('newsletter:send-weekly', ['--force' => true])->assertSuccessful();

        Mail::assertNothingSent();
    }

    public function test_option_to(): void
    {
        Mail::fake();
        $this->seedHighlight();

        $this->artisan('newsletter:send-weekly', ['--to' => 'moi@x.fr'])->assertSuccessful();

        Mail::assertSent(WeeklyNewsletterMail::class, 1);
        Mail::assertSent(WeeklyNewsletterMail::class, fn ($m) => $m->hasTo('moi@x.fr'));
    }

    public function test_hors_fenetre_aucun_envoi(): void
    {
        $this->travelTo(Carbon::now('Europe/Paris')->next(Carbon::MONDAY)->setTime(9, 0));

        Mail::fake();
        $this->seedHighlight();
        NewsletterSubscriber::create(['email' => 'confirme@x.fr', 'verified_at' => now()]);

        $this->artisan('newsletter:send-weekly')->assertSuccessful(); // auto

        Mail::assertNothingSent();
        $this->assertDatabaseCount('newsletter_logs', 0);
    }

    public function test_une_seule_vague_par_jour(): void
    {
        $this->travelTo(Carbon::now('Europe/Paris')->next(Carbon::FRIDAY)->setTime(9, 0));

        Mail::fake();
        $this->seedHighlight();
        NewsletterSubscriber::create(['email' => 'a@x.fr', 'verified_at' => now()]);

        $this->artisan('newsletter:send-weekly')->assertSuccessful();
        $this->artisan('newsletter:send-weekly')->assertSuccessful(); // 2e passage le même jour

        Mail::assertSent(WeeklyNewsletterMail::class, 1);       // une seule fois
        $this->assertDatabaseCount('newsletter_logs', 1);
    }

    public function test_envoi_par_vagues_sur_plusieurs_jours(): void
    {
        $friday = Carbon::now('Europe/Paris')->next(Carbon::FRIDAY)->setTime(9, 0);
        $this->travelTo($friday);

        Mail::fake();
        $this->seedHighlight();
        // 3 abonnés confirmés, plafond de vague = 2 → 2 vendredi, 1 samedi.
        foreach (['a@x.fr', 'b@x.fr', 'c@x.fr'] as $email) {
            NewsletterSubscriber::create(['email' => $email, 'verified_at' => now()]);
        }

        // Vendredi : 2 envoyés (plafond).
        $this->artisan('newsletter:send-weekly', ['--limit' => 2])->assertSuccessful();
        Mail::assertSent(WeeklyNewsletterMail::class, 2);
        $this->assertSame(1, NewsletterSubscriber::whereNull('last_sent_week')->count());

        // Samedi : la 3e part.
        $this->travelTo($friday->copy()->addDay()->setTime(9, 0));
        $this->artisan('newsletter:send-weekly', ['--limit' => 2])->assertSuccessful();

        Mail::assertSent(WeeklyNewsletterMail::class, 3);       // total cumulé
        $this->assertSame(0, NewsletterSubscriber::whereNull('last_sent_week')->count());
        $this->assertDatabaseCount('newsletter_logs', 2);        // une vague vendredi, une samedi
    }
}
