<?php

namespace Tests\Feature\Mailler;

use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\TestCase;

class MaillerTestSend extends TestCase
{

    public function up_database_testing()
    {
        Mail::fake();

        $mailler = new Mail();

        $mailler->send('vendor.notifications.email', ['name' => 'John'], function ($m) {
            $m->from('contact@example.com', 'Your Application');
            $m->to('hummel.julien.pro@gmail.com', 'John Smith')->subject('Welcome!');
            $m->content('vendor.notifications.email');
        });


    }
}
