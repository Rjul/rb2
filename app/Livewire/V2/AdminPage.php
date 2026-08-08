<?php

namespace App\Livewire\V2;

use App\Models\PageAdmin;
use Illuminate\Support\Str;

/**
 * Page éditoriale administrable (contenu HTML géré dans le BO), servie par le
 * catch-all /{pageAdmin:path}. Rendue dans le layout v2 : header/méga-menu,
 * footer, lecteur persistant, canonical — le HTML riche passe par
 * <x-tall.rich-text> (assainissement) + .prose-rb (charte).
 */
class AdminPage extends TallPage
{
    public PageAdmin $pageAdmin;

    public function mount(PageAdmin $pageAdmin): void
    {
        $this->pageAdmin = $pageAdmin;
    }

    public function render()
    {
        $crumbs = [
            ['name' => 'Accueil', 'url' => route('v2.home')],
            ['name' => $this->pageAdmin->name],
        ];

        return view('livewire.v2.admin-page', [
            'page'   => $this->pageAdmin,
            'crumbs' => $crumbs,
        ])->layout('layouts.tall', [
            'title'           => $this->pageAdmin->name . ' — Radio Bastides',
            'metaDescription' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) $this->pageAdmin->content))), 160),
            'canonical'       => url('/' . $this->pageAdmin->path),
            'jsonLd'          => $this->breadcrumbJsonLd($crumbs),
        ]);
    }
}
