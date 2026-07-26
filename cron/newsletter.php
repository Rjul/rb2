<?php

/**
 * Point d'entrée CRON de la newsletter hebdomadaire.
 *
 * Hébergement OVH mutualisé : le cron ne peut appeler qu'UN fichier PHP (pas de
 * « php artisan … » avec espaces, ni de schedule:run à la minute). Ce fichier
 * lance la commande newsletter:send-weekly.
 *
 * La commande s'auto-protège : elle n'envoie QUE le vendredi à partir de 8h et
 * UNE seule fois par semaine. Tu peux donc planifier ce fichier « toutes les
 * heures » dans le manager OVH sans aucun risque de double envoi.
 *
 * Manager OVH → Hébergement → Tâches planifiées :
 *   • Langage      : PHP 8.3
 *   • Fichier      : cron/newsletter.php   (chemin relatif à la racine du site)
 *   • Fréquence    : toutes les heures (ou quotidienne un vendredi si possible)
 */

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->call('newsletter:send-weekly');

echo $kernel->output();

exit($status);
