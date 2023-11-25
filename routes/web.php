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
)->name('redirect-emision');

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

Route::get('/informations-generales', function() {
   return view('pages.information');
});

require __DIR__.'/auth.php';

Route::get('/{pageAdmin:path}', [\App\Http\Controllers\PageAdminController::class, 'index'])->name('page-admin');
