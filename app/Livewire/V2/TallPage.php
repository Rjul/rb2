<?php

namespace App\Livewire\V2;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;

/**
 * Base des pages full-page du front v2 (layout `layouts.tall`).
 * Fournit :
 *  - enforceCanonical() : self-healing SEO — si le chemin demandé diffère de
 *    l'URL canonique (segment parent périmé, ancien slug), on renvoie un 301.
 *  - breadcrumbJsonLd() : données structurées BreadcrumbList prêtes pour le <head>.
 */
abstract class TallPage extends Component
{
    /**
     * Redirige en 301 vers l'URL canonique si le chemin courant en diffère.
     * Comparaison sur le PATH uniquement (jamais l'hôte) pour éviter les boucles.
     */
    protected function enforceCanonical(string $canonical): void
    {
        $current = '/' . trim(request()->path(), '/');
        $target  = parse_url($canonical, PHP_URL_PATH) ?: '/';
        $target  = '/' . trim($target, '/');

        if (rawurldecode($current) !== rawurldecode($target)) {
            // Conserve la query string (?page=…, filtres) sur la cible du 301.
            $qs = request()->getQueryString();
            $to = $qs ? $canonical . '?' . $qs : $canonical;

            // RedirectResponse direct : dans un composant Livewire, le helper redirect()
            // renvoie un Redirector Livewire (pas une Response Symfony) → rejeté par HttpResponseException.
            throw new HttpResponseException(new RedirectResponse($to, 301));
        }
    }

    /**
     * L'utilisateur courant peut-il prévisualiser du contenu NON publié ?
     * Même porte que le back-office (permission Orchid réutilisée par Filament) :
     * quiconque accède à /gestion ou /admin peut prévisualiser depuis les liens
     * « Voir sur le site ». Les visiteurs restent sur un 404 strict.
     */
    protected function canPreviewUnpublished(): bool
    {
        return (bool) auth()->user()?->hasAccess('platform.index');
    }

    /**
     * Construit un BreadcrumbList schema.org.
     * @param array<int,array{name:string,url?:string}> $crumbs
     */
    protected function breadcrumbJsonLd(array $crumbs): array
    {
        $items = [];
        foreach (array_values($crumbs) as $i => $c) {
            $items[] = array_filter([
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $c['name'],
                'item'     => $c['url'] ?? null,
            ], fn ($v) => $v !== null);
        }

        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }
}
