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
        Schema::create('programmes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_programme_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('group_programme_id')
                ->references('id')
                ->on('group_programmes');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->string('name');
            $table->text('description');
            $table->string('image');
            $table->boolean('active');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programas');
    }
};
