<?php

namespace Tests\Feature;

use App\Mail\OtpCodeMail;
use App\Models\EmailOtp;
use App\Models\User;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Vérification d'email par OTP à l'inscription + anti-bot honeypot.
 */
class EmailVerificationOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_inscription_cree_un_compte_non_verifie_et_envoie_un_otp(): void
    {
        Mail::fake();

        $this->post('/register', [
            'name'                  => 'Jean Test',
            'email'                 => 'JEAN@Example.com',
            'password'              => 'password-123',
            'password_confirmation' => 'password-123',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'jean@example.com')->first(); // normalisé en minuscules
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at, 'Le compte doit être NON vérifié à la création');
        $this->assertDatabaseHas('email_otps', ['email' => 'jean@example.com', 'purpose' => 'register']);
        Mail::assertSent(OtpCodeMail::class);
    }

    public function test_le_honeypot_bloque_les_bots(): void
    {
        Mail::fake();

        $this->post('/register', [
            'name'                  => 'Bot',
            'email'                 => 'bot@spam.com',
            'password'              => 'password-123',
            'password_confirmation' => 'password-123',
            'website'               => 'http://spam.example',   // champ piège rempli
        ]);

        $this->assertDatabaseMissing('users', ['email' => 'bot@spam.com']);
        Mail::assertNothingSent();
    }

    public function test_un_bon_code_verifie_l_email(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $code = EmailOtp::issue($user->email, 'register');

        $this->actingAs($user)
            ->post(route('verification.verify'), ['code' => $code])
            ->assertRedirect();

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertDatabaseMissing('email_otps', ['email' => $user->email]); // code consommé
    }

    public function test_un_mauvais_code_est_refuse(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        EmailOtp::issue($user->email, 'register');

        $this->actingAs($user)
            ->post(route('verification.verify'), ['code' => '000000'])
            ->assertSessionHasErrors('code');

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_un_utilisateur_non_verifie_ne_peut_pas_commenter(): void
    {
        $unverified = User::factory()->create(['email_verified_at' => null]);
        $verified   = User::factory()->create(['email_verified_at' => now()]);

        $this->assertFalse(Gate::forUser($unverified)->allows('create-comment'));
        $this->assertTrue(Gate::forUser($verified)->allows('create-comment'));
    }

    public function test_un_compte_cree_par_un_admin_est_verifie_directement(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name'     => 'Staff RB',
                'email'    => 'staff@radiobastides.fr',
                'password' => 'motdepasse-123',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $user = User::where('email', 'staff@radiobastides.fr')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasVerifiedEmail(), 'Un compte créé par un admin doit être vérifié directement');
    }
}
