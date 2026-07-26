<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'verified_at',
        'unsubscribed_at',
        'last_sent_week',
    ];

    protected $casts = [
        'verified_at'     => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];
}
