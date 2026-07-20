<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Code OTP de vérification d'email.
 *
 * Sécurité :
 *  - code stocké HACHÉ (bcrypt) ;
 *  - expiration courte ({@see self::TTL_MINUTES}) ;
 *  - nombre de tentatives plafonné ({@see self::MAX_ATTEMPTS}) ;
 *  - un seul code actif par (email, purpose) — l'émission efface les précédents.
 */
class EmailOtp extends Model
{
    protected $fillable = ['email', 'purpose', 'code', 'attempts', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];

    public const TTL_MINUTES = 10;
    public const MAX_ATTEMPTS = 5;
    public const CODE_LENGTH = 6;

    /**
     * Émet un nouveau code pour (email, purpose) et renvoie le code EN CLAIR
     * (à envoyer par email ; il n'est jamais stocké en clair).
     */
    public static function issue(string $email, string $purpose = 'register'): string
    {
        $email = mb_strtolower(trim($email));
        $code  = str_pad((string) random_int(0, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);

        static::where('email', $email)->where('purpose', $purpose)->delete();

        static::create([
            'email'      => $email,
            'purpose'    => $purpose,
            'code'       => Hash::make($code),
            'attempts'   => 0,
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
        ]);

        return $code;
    }

    /**
     * Vérifie un code. Consomme (supprime) le code en cas de succès.
     * Incrémente le compteur de tentatives sinon ; au-delà du plafond, le code
     * est invalidé.
     */
    public static function attempt(string $email, string $purpose, string $code): bool
    {
        $email = mb_strtolower(trim($email));

        $otp = static::where('email', $email)
            ->where('purpose', $purpose)
            ->latest('id')
            ->first();

        if (! $otp || $otp->expires_at->isPast() || $otp->attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        $otp->increment('attempts');

        if (! Hash::check($code, $otp->code)) {
            return false;
        }

        $otp->delete();

        return true;
    }
}
