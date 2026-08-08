<?php

namespace App\Utilities;

use App\Models\Emision;
use App\Models\GroupProgramme;
use App\Models\PageAdmin;
use App\Models\Programme;
use App\Models\Tag;
use Illuminate\Support\Facades\Log;

/**
 * Génère public/sitemap.xml en STREAMING, à empreinte mémoire bornée.
 *
 * Calqué sur {@see RssService} (rendu → fichier dans public/). Contrainte clé :
 * hébergement mutualisé → on ne charge JAMAIS toute la table en RAM. On itère
 * par lots via chunkById() (curseur sur la PK, coût constant, exploite l'index)
 * et on écrit chaque <url> au fil de l'eau dans un descripteur de fichier.
 *
 * Servi en statique par le serveur web (comme public/rss/*.xml) : aucune route,
 * aucun PHP par requête, aucun conflit avec le catch-all /{pageAdmin:path}.
 */
class SitemapService
{
    /** Seuil d'alerte : au-delà, prévoir un sitemap-index (limite protocole = 50 000). */
    private const MAX_URLS = 45000;

    public function generate(?string $path = null): bool
    {
        $path = $path ?? public_path('sitemap.xml');
        $tmp  = $path . '.tmp';

        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $h = fopen($tmp, 'w');
        if ($h === false) {
            return false;
        }

        try {
            fwrite($h, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
            fwrite($h, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");

            // Hubs statiques (indexables). /recherche est exclu (noindex).
            foreach ($this->staticUrls() as $loc) {
                fwrite($h, $this->urlTag($loc));
            }

            $count = count($this->staticUrls());

            // Catégories actives.
            GroupProgramme::query()
                ->where('is_active', 1)
                ->chunkById(200, function ($categories) use ($h, &$count) {
                    foreach ($categories as $category) {
                        fwrite($h, $this->urlTag($category->canonicalUrl(), $category->updated_at));
                        $count++;
                    }
                });

            // Programmes actifs (rattachés à une catégorie → canonicalUrl valide).
            Programme::query()
                ->where('is_active', 1)
                ->whereNotNull('group_programme_id')
                ->with('group_programme')
                ->chunkById(500, function ($programmes) use ($h, &$count) {
                    foreach ($programmes as $programme) {
                        fwrite($h, $this->urlTag($programme->canonicalUrl(), $programme->updated_at));
                        $count++;
                    }
                });

            // Émissions publiées. JOIN (pas whereHas) pour exploiter les index et éviter
            // une sous-requête corrélée par lot ; eager-load pour canonicalUrl() sans N+1.
            Emision::query()
                ->join('programmes', 'emisions.programme_id', '=', 'programmes.id')
                ->where('programmes.is_active', 1)
                ->whereNotNull('programmes.group_programme_id')
                ->where('emisions.is_active', 1)
                ->where('emisions.active_at', '<', now())
                ->select('emisions.*')
                ->with('programme.group_programme')
                ->chunkById(1000, function ($emisions) use ($h, &$count) {
                    foreach ($emisions as $emision) {
                        fwrite($h, $this->urlTag($emision->canonicalUrl(), $emision->updated_at));
                        $count++;
                    }
                }, 'emisions.id', 'id');

            // Pages éditoriales administrables (BO), servies par le catch-all.
            PageAdmin::query()
                ->chunkById(200, function ($pages) use ($h, &$count) {
                    foreach ($pages as $page) {
                        fwrite($h, $this->urlTag(url('/' . $page->path), $page->updated_at));
                        $count++;
                    }
                });

            // Thèmes ayant au moins une émission publiée.
            Tag::query()
                ->whereHas('emisions', fn ($q) => $q
                    ->where('emisions.is_active', 1)
                    ->where('emisions.active_at', '<', now()))
                ->chunkById(500, function ($tags) use ($h, &$count) {
                    foreach ($tags as $tag) {
                        fwrite($h, $this->urlTag($this->themeUrl($tag), $tag->updated_at));
                        $count++;
                    }
                });

            fwrite($h, '</urlset>' . "\n");
        } finally {
            fclose($h);
        }

        if ($count > self::MAX_URLS) {
            Log::warning("Sitemap: {$count} URLs (> " . self::MAX_URLS . ") — prévoir un sitemap-index.");
        }

        // Écriture atomique : le serveur ne sert jamais un XML tronqué pendant la génération.
        rename($tmp, $path);

        return true;
    }

    /** URLs des hubs indexables (sans /recherche). */
    private function staticUrls(): array
    {
        return [
            route('v2.home'),
            route('v2.categories'),
            route('v2.programmes'),
            route('v2.emissions'),
            route('v2.emissions.type', ['type' => 'audio']),
            route('v2.emissions.type', ['type' => 'video']),
            route('v2.emissions.type', ['type' => 'articles']),
            route('v2.themes'),
            route('informations'),
        ];
    }

    /** URL canonique d'un thème (slug traduit fr, déterministe en CLI). */
    private function themeUrl(Tag $tag): string
    {
        return route('v2.theme', ['tag' => $tag->getTranslation('slug', 'fr')]);
    }

    private function urlTag(string $loc, $lastmod = null): string
    {
        $xml = '  <url><loc>' . htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>';
        if ($lastmod !== null) {
            $xml .= '<lastmod>' . $lastmod->toAtomString() . '</lastmod>';
        }

        return $xml . '</url>' . "\n";
    }
}
