<?php

namespace Tests\Feature\Front;

use App\Livewire\Newsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('emission_image');
        Storage::fake('emission_audio');
    }

    public function test_accueil_tall_repond_200(): void
    {
        $this->get(route('v2.home'))
            ->assertOk()
            ->assertSee('Radio Bastides');
    }

    public function test_inscription_newsletter_enregistre_l_email(): void
    {
        Livewire::test(Newsletter::class)
            ->set('email', 'Test@Exemple.FR')
            ->call('subscribe')
            ->assertHasNoErrors()
            ->assertSet('done', true);

        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'test@exemple.fr']);
    }

    public function test_newsletter_refuse_un_email_invalide(): void
    {
        Livewire::test(Newsletter::class)
            ->set('email', 'pas-un-email')
            ->call('subscribe')
            ->assertHasErrors(['email']);

        $this->assertDatabaseCount('newsletter_subscribers', 0);
    }

    public function test_newsletter_pas_de_doublon(): void
    {
        NewsletterSubscriber::create(['email' => 'x@y.fr']);

        Livewire::test(Newsletter::class)
            ->set('email', 'x@y.fr')
            ->call('subscribe')
            ->assertHasNoErrors();

        $this->assertDatabaseCount('newsletter_subscribers', 1);
    }
}
