<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\OtpVerificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

    // throttle : limite les inscriptions par IP → protège le quota d'emails des bots.
    Route::post('register', [RegisteredUserController::class, 'store'])
                ->middleware('throttle:5,1');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
                ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
                ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
                ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
                ->name('password.store');

    /**
     * Google's connection and callback
     */
    Route::get('auth/google', [\App\Http\Controllers\Auth\Socialite\GoogleConnectionController::class, 'redirectToGoogle'])
        ->name('login.google');
    Route::get('auth/google/callback', [\App\Http\Controllers\Auth\Socialite\GoogleConnectionController::class, 'handleGoogleCallback']);
    /**
     * Facebook's connection and callback
     */
    Route::get('auth/facebook', [\App\Http\Controllers\Auth\Socialite\FacebookConnectionController::class, 'redirectToFacebook'])
        ->name('login.facebook');
    Route::get('auth/facebook/callback', [\App\Http\Controllers\Auth\Socialite\FacebookConnectionController::class, 'handleFacebookCallback']);



});

Route::middleware('auth')->group(function () {
    // Vérification d'email par code OTP (remplace le lien Breeze).
    Route::get('verify-email', [OtpVerificationController::class, 'notice'])
                ->name('verification.notice');

    Route::post('verify-email', [OtpVerificationController::class, 'verify'])
                ->middleware('throttle:10,1')
                ->name('verification.verify');

    Route::post('verify-email/resend', [OtpVerificationController::class, 'resend'])
                ->middleware('throttle:3,10')
                ->name('verification.resend');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
                ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->name('logout');
});
