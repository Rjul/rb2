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
Route::get('/',
    [\App\Http\Controllers\HomepageController::class, 'index' ]
)->name('homepage');

Route::get('/programme-{programme:slug}',
    [\App\Http\Controllers\ListController::class, 'index' ]
)->name('list-programme');

Route::get('/emisiones/{emision:id}',
    function (\App\Models\Emision $emision) {
        return redirect()->route('view-emision', ['programme' => $emision->programme , 'emision' => $emision ], 301);
    }
)->name('redirect-emision');

Route::get('/programas',
    function (\Illuminate\Support\Facades\Request $request) {
        $programme = \App\Models\Programme::find(request("id"));
        if ($programme == null) {
            return redirect()->route('list-search', [], 301);
        }
        return redirect()->route('list-programme', ['programme' => $programme ], 301);
    }
)->name('redirect-programas');

Route::get('/programme-{programme:slug}/emission-{emision:slug}',
    [\App\Http\Controllers\DetannController::class, 'index' ])
->where('emision:id', '[0-9]*')
->name('view-emision');

Route::get('/thematique-{tag}',
    [\App\Http\Controllers\ListController::class, 'index' ]
)->name('list-tag');

Route::get('/recherche',
    [\App\Http\Controllers\SearchController::class, 'index' ]
)->name('list-search');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

/*
|--------------------------------------------------------------------------
| Front v2 (TALL) — préfixe temporaire, à retirer pour la bascule
|--------------------------------------------------------------------------
| Toutes les pages du front modernisé. Le legacy reste à la racine pendant
| la cohabitation ; le layout met /v2 en noindex automatiquement.
| Feuilles (programme, émission) résolues par l'id du slug (self-healing),
| les segments parents sont cosmétiques. Noms « v2.* » conservés à la bascule.
| Placé AVANT le catch-all /{pageAdmin:path}.
*/
Route::prefix('v2')->name('v2.')->group(function () {
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
