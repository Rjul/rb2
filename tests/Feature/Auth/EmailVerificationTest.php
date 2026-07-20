<?php

namespace Tests\Feature\Auth;

use App\Models\EmailOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Vérification d'email par CODE OTP (Breeze lien remplacé par un OTP).
 * Le détail (envoi, honeypot, gate commentaires) est couvert par
 * {@see \Tests\Feature\EmailVerificationOtpTest}.
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered()
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)->get('/verify-email')->assertStatus(200);
    }

    public function test_email_can_be_verified_with_otp()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $code = EmailOtp::issue($user->email, 'register');

        $this->actingAs($user)->post(route('verification.verify'), ['code' => $code]);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_is_not_verified_with_wrong_otp()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        EmailOtp::issue($user->email, 'register');

        $this->actingAs($user)->post(route('verification.verify'), ['code' => '111111']);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
