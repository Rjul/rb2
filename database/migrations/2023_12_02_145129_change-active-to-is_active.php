<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emisions', function (Blueprint $table) {
            // change default value of approved column
            $table->renameColumn('active', 'is_active');
        });
        Schema::table('programmes', function (Blueprint $table) {
            // change default value of approved column
            $table->renameColumn('active', 'is_active');
        });
        Schema::table('group_programmes', function (Blueprint $table) {
            // change default value of approved column
            $table->renameColumn('active', 'is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
