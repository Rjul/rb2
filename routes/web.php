<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*
|--------------------------------------------------------------------------
| Redirections des anciennes URL v1 → pages v2 canoniques (préservation SEO)
|--------------------------------------------------------------------------
| Les chemins qui COÏNCIDENT avec v2 (`/`, `/thematique-{tag}`, `/recherche`)
| ne sont plus déclarés ici : ils sont servis directement par les routes v2
| (mêmes URL). Les chemins qui DIFFÈRENT redirigent en 301 vers la canonique.
| On garde les noms `list-programme` / `view-emision` (utilisés par l'admin,
| les flux RSS et d'anciens liens) — ils pointent désormais vers une redirection.
*/

// /programme-{slug} → fiche programme v2 (résolution résiliente : slug exact ou id en fin de slug).
Route::get('/programme-{programme}', function (string $programme) {
    $p = \App\Models\Programme::fromSlugId($programme);
    abort_if(! $p, 404);

    return redirect($p->canonicalUrl(), 301);
})->name('list-programme');

// /programme-{slug}/emission-{slug} → fiche émission v2 (segment programme cosmétique).
Route::get('/programme-{programme}/emission-{emision}', function (string $programme, string $emision) {
    $e = \App\Models\Emision::fromSlugId($emision);
    abort_if(! $e, 404);

    return redirect($e->canonicalUrl(), 301);
})->name('view-emision');

// /emisiones/{id} → fiche émission v2.
Route::get('/emisiones/{emision:id}',
    fn (\App\Models\Emision $emision) => redirect($emision->canonicalUrl(), 301)
)->name('redirect-emision');

// /programas?id=… → programme v2 (ou recherche si introuvable).
Route::get('/programas', function () {
    $programme = \App\Models\Programme::find(request('id'));

    return $programme
        ? redirect($programme->canonicalUrl(), 301)
        : redirect()->route('v2.search', [], 301);
})->name('redirect-programas');

// L'ancien tableau de bord Breeze (vue stub cassée) est remplacé par l'espace compte.
Route::get('/dashboard', fn () => redirect()->route('profile.edit'))
    ->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Commentaires (public, réservé aux utilisateurs connectés)
Route::middleware('auth')->group(function () {
    Route::post('comments', [\App\Http\Controllers\WebCommentController::class, 'store'])->name('comments.store');
    Route::delete('comments/{comment}', [\App\Http\Controllers\WebCommentController::class, 'destroy'])->name('comments.destroy');
    Route::put('comments/{comment}', [\App\Http\Controllers\WebCommentController::class, 'update'])->name('comments.update');
    Route::post('comments/{comment}', [\App\Http\Controllers\WebCommentController::class, 'reply'])->name('comments.reply');
});

Route::get('/informations-generales', function() {
   return view('pages.information');
});

// Newsletter (double opt-in) : liens signés reçus par email.
Route::get('newsletter/confirm/{subscriber}', [\App\Http\Controllers\NewsletterController::class, 'confirm'])
    ->middleware('signed')->name('newsletter.confirm');
Route::get('newsletter/unsubscribe/{subscriber}', [\App\Http\Controllers\NewsletterController::class, 'unsubscribe'])
    ->middleware('signed')->name('newsletter.unsubscribe');

// Prévisualisation du template de newsletter dans le navigateur (dev uniquement).
if (app()->environment('local')) {
    Route::get('newsletter/preview', function () {
        $emissions = \App\Models\Emision::getWeeklyHighlights(6);
        abort_if($emissions->isEmpty(), 404, 'Aucune émission à afficher.');
        $url = \Illuminate\Support\Facades\URL::signedRoute('newsletter.unsubscribe', ['subscriber' => 0]);

        return new \App\Mail\WeeklyNewsletterMail($emissions, $url, 'du 14/07 au 20/07');
    });
}

/*
|--------------------------------------------------------------------------
| Front v2 (TALL) — servi à la RACINE du site (bascule production)
|--------------------------------------------------------------------------
| Toutes les pages du front modernisé. Feuilles (programme, émission) résolues
| par l'id du slug (self-healing → 301 vers la canonique) ; les segments parents
| sont cosmétiques. Noms « v2.* » conservés. Placé AVANT le catch-all
| /{pageAdmin:path}. Les 3 chemins repris du v1 (/, /thematique-{tag}, /recherche)
| sont servis ici directement.
*/
Route::name('v2.')->group(function () {
    Route::get('/', \App\Livewire\HomePage::class)->name('home');

    Route::get('/categories', \App\Livewire\V2\CategoriesIndex::class)->name('categories');
    Route::get('/categories/{categorie}', \App\Livewire\V2\CategoryPage::class)->name('category');
    Route::get('/categories/{categorie}/{programme}', \App\Livewire\V2\ProgrammePage::class)->name('programme');
    Route::get('/categories/{categorie}/{programme}/{emission}', \App\Livewire\V2\EmissionPage::class)->name('emission');

    Route::get('/programmes', \App\Livewire\V2\ProgrammesIndex::class)->name('programmes');
    Route::get('/emissions', \App\Livewire\V2\EmissionsIndex::class)->name('emissions');
    Route::get('/emissions/{type}', \App\Livewire\V2\EmissionsIndex::class)
        ->whereIn('type', ['audio', 'video', 'articles'])->name('emissions.type');

    Route::get('/thematiques', \App\Livewire\V2\ThemesIndex::class)->name('themes');
    Route::get('/thematique-{tag}', \App\Livewire\V2\ThemePage::class)->name('theme');

    Route::get('/recherche', \App\Livewire\V2\SearchPage::class)->name('search');
});

// Impersonation : revenir à son compte d'origine après « se connecter en tant que ».
// Passe par AuthenticateSession (comme le panel) pour que le hash de mot de passe
// suivi en session soit réaligné sur l'utilisateur restauré (sinon déconnexion).
Route::middleware(['auth', \Filament\Http\Middleware\AuthenticateSession::class])
    ->get('impersonation/leave', function () {
        if (\Orchid\Access\Impersonation::isSwitch()) {
            \Orchid\Access\Impersonation::logout();
        }

        return redirect('/gestion');
    })->name('impersonation.leave');

require __DIR__.'/auth.php';

Route::get('/{pageAdmin:path}', [\App\Http\Controllers\PageAdminController::class, 'index'])->name('page-admin');
