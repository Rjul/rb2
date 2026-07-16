<?php

namespace App\Livewire\V2;

use App\Livewire\V2\Concerns\WithEmissionFilters;
use App\Models\Emision;
use App\Models\Programme;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

/**
 * Page programme : ses émissions filtrables (recherche + type + tri) et paginées,
 * + contexte (catégorie, RSS, voisins).
 *
 * On stocke l'id (pas le modèle) : une propriété publique typée-modèle nommée
 * comme un paramètre de route ({programme}) déclencherait un route-model-binding
 * Livewire (résolution par id) qui échouerait sur un slug → 404 avant mount.
 */
class ProgrammePage extends TallPage
{
    use WithPagination;
    use WithEmissionFilters;

    public int $programmeId;

    /** Filtre type scopé au programme (media_type : audio|video|text). */
    #[Url(history: true)]
    public ?string $ptype = null;

    public function mount(string $categorie, string $programme): void
    {
        $p = Programme::fromSlugId($programme);
        abort_if(! $p || ! $p->is_active, 404);

        $this->programmeId = $p->id;
        $this->enforceCanonical($p->load('group_programme')->canonicalUrl());
    }

    public function updatedPtype(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('q', 'sort', 'ptype');
        $this->resetPage();
    }

    public function render()
    {
        $programme = Programme::with('group_programme')->findOrFail($this->programmeId);
        $category  = $programme->group_programme;

        $query = $programme->emisions()
            ->where('emisions.is_active', true)
            ->where('emisions.active_at', '<', now())
            ->with(['attachment', 'programme.group_programme']);

        $this->applyTextAndSort($query);

        if ($this->ptype) {
            $query->where('emisions.media_type', $this->ptype);
        }

        $emissions = $query->paginate(12)->withQueryString();

        $siblings = $category
            ? $category->programmesOrderByHeightAndActive()
                ->with('group_programme')
                ->where('programmes.id', '!=', $programme->id)
                ->limit(6)->get()
            : collect();

        $crumbs = array_values(array_filter([
            ['name' => 'Accueil', 'url' => route('v2.home')],
            ['name' => 'Catégories', 'url' => route('v2.categories')],
            $category ? ['name' => $category->name, 'url' => $category->canonicalUrl()] : null,
            ['name' => $programme->name],
        ]));

        $desc = trim(strip_tags((string) $programme->description));

        return view('livewire.v2.programme-page', [
            'programme'  => $programme,
            'emissions'  => $emissions,
            'siblings'   => $siblings,
            'category'   => $category,
            'crumbs'     => $crumbs,
            'typeTabs'   => [
                ['key' => null, 'label' => 'Tout'],
                ['key' => Emision::TYPE_AUDIO, 'label' => 'Audio'],
                ['key' => Emision::TYPE_VIDEO, 'label' => 'Vidéo'],
                ['key' => Emision::TYPE_TEXT, 'label' => 'Articles'],
            ],
            'hasFilters' => trim($this->q) !== '' || $this->ptype || $this->sort !== 'recent',
        ])->layout('layouts.tall', [
            'title'           => $programme->name . ' — Radio Bastides',
            'metaDescription' => \Illuminate\Support\Str::limit($desc !== '' ? $desc : 'Toutes les émissions du programme ' . $programme->name . ' sur Radio Bastides.', 160),
            'canonical'       => $programme->canonicalUrl(),
            'ogImage'         => $programme->image ?: null,
            'jsonLd'          => $this->breadcrumbJsonLd($crumbs),
        ]);
    }
}
