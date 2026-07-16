<?php

namespace App\Livewire\V2;

use App\Models\Emision;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Fiche émission (feuille). Adaptative selon media_type :
 *  - audio → alimente le lecteur PERSISTANT (rb:play / rb:queue), pas de lib.
 *  - vidéo → lecteur <video> natif sur la page (isolé wire:ignore).
 *  - texte → article.
 * Résolue par l'id du slug (self-healing 301). Commentaires délégués à un
 * composant Livewire enfant (le commentable est fixé côté serveur, jamais le POST).
 */
class EmissionPage extends TallPage
{
    // Id plutôt que modèle : une propriété publique nommée comme le param de route
    // ({emission}) déclencherait un route-model-binding Livewire → 404 avant mount.
    public int $emissionId;

    public function mount(string $categorie, string $programme, string $emission): void
    {
        $e = Emision::fromSlugId($emission);
        abort_if(! $e, 404);

        $e->load(['programme.group_programme', 'attachment', 'tags']);

        $published = $e->active_at && ! Carbon::parse($e->active_at)->isFuture();
        abort_if(! $e->is_active || ! $e->programme || ! $e->programme->is_active || ! $published, 404);

        $this->emissionId = $e->id;

        // Chemin périmé → 301 vers la canonique.
        $this->enforceCanonical($e->canonicalUrl());
    }

    public function render()
    {
        $e    = Emision::with(['programme.group_programme', 'attachment', 'tags'])->findOrFail($this->emissionId);
        $prog = $e->programme;
        $cat  = $prog?->group_programme;

        // Voisins (même programme, par date de publication) — bornés à now() des deux
        // côtés : jamais de voisin non publié / programmé.
        $before = $prog->emisions()->where('is_active', true)
            ->where('active_at', '<', $e->active_at)->where('active_at', '<', now())
            ->orderByDesc('active_at')->first();
        $next = $prog->emisions()->where('is_active', true)
            ->where('active_at', '>', $e->active_at)->where('active_at', '<', now())
            ->orderBy('active_at')->first();
        // Le programme (avec sa catégorie) est déjà chargé : on le réinjecte pour que
        // $before/$next->canonicalUrl() ne relance pas de requêtes (évite le N+1).
        $before?->setRelation('programme', $prog);
        $next?->setRelation('programme', $prog);

        // Suggestions (même programme, 6 autres, hors courante).
        $suggestions = $prog->emisions()
            ->where('is_active', true)->where('active_at', '<', now())
            ->where('emisions.id', '!=', $e->id)
            ->orderByDesc('active_at')
            ->with(['attachment', 'programme.group_programme'])
            ->limit(6)->get();

        // Média selon le type.
        $isAudio = $e->media_type === Emision::TYPE_AUDIO;
        $isVideo = $e->media_type === Emision::TYPE_VIDEO;
        $img     = $e->image ?: 'https://picsum.photos/seed/rb-' . $e->id . '/1200/700';

        $track = $isAudio ? [
            'title'    => $e->name,
            'prog'     => $prog?->name,
            'art'      => $img,
            'src'      => $e->audioUrl(),
            'duration' => $e->duration ? (int) round($e->duration * 60) : null,
        ] : null;

        $videoUrl = $isVideo ? $e->videoUrl() : null;

        $desc = trim(strip_tags((string) $e->description));
        $metaDescription = Str::limit(($prog?->name ? $prog->name . ' — ' : '') . ($desc !== '' ? $desc : $e->name), 160);

        $crumbs = array_values(array_filter([
            ['name' => 'Accueil', 'url' => route('v2.home')],
            ['name' => 'Catégories', 'url' => route('v2.categories')],
            $cat ? ['name' => $cat->name, 'url' => $cat->canonicalUrl()] : null,
            $prog ? ['name' => $prog->name, 'url' => $prog->canonicalUrl()] : null,
            ['name' => $e->name],
        ]));

        // Données structurées du média (en plus du fil d'Ariane) : AudioObject /
        // VideoObject / Article selon le type — rich results Google.
        $publishedIso = $e->active_at ? Carbon::parse($e->active_at)->toIso8601String() : null;
        $mediaJsonLd = array_filter([
            '@context'      => 'https://schema.org',
            '@type'         => $isAudio ? 'AudioObject' : ($isVideo ? 'VideoObject' : 'Article'),
            $isAudio || $isVideo ? 'name' : 'headline' => $e->name,
            'description'   => $desc !== '' ? Str::limit($desc, 300) : null,
            'thumbnailUrl'  => $e->image ?: null,
            'image'         => $e->image ?: null,
            'url'           => $e->canonicalUrl(),
            'uploadDate'    => ($isAudio || $isVideo) ? $publishedIso : null,
            'datePublished' => $publishedIso,
            'duration'      => $e->duration ? 'PT' . (int) round($e->duration) . 'M' : null,
            'contentUrl'    => $isAudio ? $e->audioUrl() : ($isVideo ? $videoUrl : null),
            'author'        => ['@type' => 'Organization', 'name' => 'Radio Bastides'],
            'publisher'     => ['@type' => 'Organization', 'name' => 'Radio Bastides'],
            'partOfSeries'  => $prog ? ['@type' => 'CreativeWorkSeries', 'name' => $prog->name, 'url' => $prog->canonicalUrl()] : null,
        ], fn ($v) => $v !== null);

        return view('livewire.v2.emission-page', [
            'e'           => $e,
            'prog'        => $prog,
            'category'    => $cat,
            'img'         => $img,
            'isAudio'     => $isAudio,
            'isVideo'     => $isVideo,
            'track'       => $track,
            'videoUrl'    => $videoUrl,
            'before'      => $before,
            'next'        => $next,
            'suggestions' => $suggestions,
            'crumbs'      => $crumbs,
            'publishedAt' => $e->active_at ? Carbon::parse($e->active_at)->locale('fr')->isoFormat('LL') : null,
        ])->layout('layouts.tall', [
            'title'           => $e->name . ' — Radio Bastides',
            'metaDescription' => $metaDescription,
            'canonical'       => $e->canonicalUrl(),
            'ogType'          => $isAudio ? 'music.song' : ($isVideo ? 'video.other' : 'article'),
            'ogImage'         => $e->image ?: null,
            // Racine tableau = plusieurs entités JSON-LD dans un seul <script> (valide).
            'jsonLd'          => [$this->breadcrumbJsonLd($crumbs), $mediaJsonLd],
        ]);
    }
}
