<?php

/**
 * Point d'entrée CRON de la génération du sitemap.
 *
 * Hébergement OVH mutualisé : le cron ne peut appeler qu'UN fichier PHP (pas de
 * « php artisan … » avec espaces, ni de schedule:run à la minute). Ce fichier
 * lance la commande app:sitemap:generate, qui (ré)écrit public/sitemap.xml.
 *
 * La génération est IDEMPOTENTE et à mémoire bornée (streaming + chunkById) :
 * tu peux la planifier à n'importe quelle fréquence sans risque. Une fois par
 * jour suffit largement pour un site de contenu.
 *
 * Manager OVH → Hébergement → Tâches planifiées :
 *   • Langage   : PHP 8.3
 *   • Fichier   : cron/sitemap.php   (chemin relatif à la racine du site)
 *   • Fréquence : quotidienne
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->call('app:sitemap:generate');

echo $kernel->output();

exit($status);
