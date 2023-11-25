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
        Schema::create('emisions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('programme_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('programme_id')
                ->references('id')
                ->on('programmes');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->string('name');
            $table->string('duration')->nullable();
            $table->text('description');
            $table->string('media_type');
            $table->boolean('is_put_forward');
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
        Schema::dropIfExists('emisions');
    }
};
