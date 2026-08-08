<?php

namespace App\Livewire\V2;

/**
 * Page « Informations générales » (contenu statique historique : association,
 * responsabilité légale, RGPD), migrée du layout Bootstrap vers le layout v2.
 * Le détail par sujet vit dans les pages administrables (BO) servies par
 * {@see AdminPage} ; cette page agrégée conserve son URL indexée.
 */
class InformationPage extends TallPage
{
    public function render()
    {
        $crumbs = [
            ['name' => 'Accueil', 'url' => route('v2.home')],
            ['name' => 'Informations générales'],
        ];

        return view('livewire.v2.information-page', ['crumbs' => $crumbs])
            ->layout('layouts.tall', [
                'title'           => 'Informations générales — Radio Bastides',
                'metaDescription' => 'Médias Citoyens en Villeneuvois (MCV) : l’association qui porte Radio Bastides, sa responsabilité légale et sa politique de protection des données.',
                'canonical'       => url('/informations-generales'),
                'jsonLd'          => $this->breadcrumbJsonLd($crumbs),
            ]);
    }
}
