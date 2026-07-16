<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * Cache des données du front public (v2).
 *
 * Le driver `file` ne supporte pas les tags : on versionne les clés à la place.
 * Toute écriture back-office sur un modèle du front (émission, programme,
 * catégorie…) appelle bump() → la version change → toutes les entrées
 * précédentes deviennent orphelines (ignorées, purgées par cache:clear ou TTL).
 *
 * Le TTL reste court en filet de sécurité (modifications hors modèle : pivots
 * SQL bruts, imports…).
 */
class FrontCache
{
    private const VERSION_KEY = 'front:v';

    public static function remember(string $key, Closure $callback, ?int $ttl = null)
    {
        return Cache::remember(static::key($key), $ttl ?? static::defaultTtl(), $callback);
    }

    /**
     * TTL par défaut : expiration au PROCHAIN top d'heure (14:00, 15:00…).
     * Les publications étant programmées à l'heure pile, le cache se régénère
     * exactement quand une nouvelle émission doit apparaître. Les écritures BO
     * invalident en plus immédiatement (bump()), et le bouton du dashboard force
     * un rafraîchissement à la demande.
     */
    public static function defaultTtl(): int
    {
        $nextHour = now()->copy()->startOfHour()->addHour();

        return max(1, $nextHour->getTimestamp() - now()->getTimestamp());
    }

    /** Invalide tout le cache front (appelé par les événements de modèle). */
    public static function bump(): void
    {
        // add() initialise la clé si absente ; increment() la fait tourner.
        Cache::add(self::VERSION_KEY, 1, null);
        Cache::increment(self::VERSION_KEY);
    }

    public static function key(string $key): string
    {
        $version = Cache::get(self::VERSION_KEY, 1);

        return "front:{$version}:{$key}";
    }
}
