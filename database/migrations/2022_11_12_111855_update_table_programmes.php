<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->integer('height');
        });

    }

    public function down()
    {
        Schema::table('programmes', function (Blueprint $table) {
            $table->dropColumn('height');

        });
    }
};
