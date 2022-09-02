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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('name', 255)->default('');
            $table->string('location_name', 255)->default('');
            $table->string('lat', 255)->default('');
            $table->string('long', 255)->default('');
            $table->string('description', 255)->default('');
            $table->string('bannar_img', 255)->default('');
            $table->string('business_img', 255)->default('');
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
        Schema::dropIfExists('businesses');
    }
};
