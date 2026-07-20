<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

/**
 * Confirmation (double opt-in) et désinscription de la newsletter.
 * Les deux routes passent par le middleware `signed` : le lien reçu par email
 * est une URL signée à durée limitée → impossible à forger ou à deviner.
 */
class NewsletterController extends Controller
{
    public function confirm(Request $request, NewsletterSubscriber $subscriber)
    {
        if (! $subscriber->verified_at) {
            $subscriber->update([
                'verified_at'     => now(),
                'unsubscribed_at' => null,
            ]);
        }

        return view('newsletter.confirmed', ['email' => $subscriber->email]);
    }

    public function unsubscribe(Request $request, NewsletterSubscriber $subscriber)
    {
        if (! $subscriber->unsubscribed_at) {
            $subscriber->update(['unsubscribed_at' => now()]);
        }

        return view('newsletter.unsubscribed', ['email' => $subscriber->email]);
    }
}
