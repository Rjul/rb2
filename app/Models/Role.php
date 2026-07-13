<?php

namespace App\Models;

use Orchid\Platform\Models\Role as OrchidRole;

/**
 * Sous-classe applicative du rôle Orchid (même table `roles`),
 * pour exposer la gestion des rôles côté Filament tout en réutilisant
 * le système de permissions d'Orchid.
 */
class Role extends OrchidRole
{
}
