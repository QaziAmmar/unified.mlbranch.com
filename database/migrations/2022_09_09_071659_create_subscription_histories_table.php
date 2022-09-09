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
        Schema::create('subscription_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('change_gender_filter', 255)->default('');
            $table->string('remove_ads', 255)->default('');
            $table->string('create_business', 255)->default('');
            $table->string('unlimited_matches', 255)->default('');
            $table->string('unlimited_swipes', 255)->default('');
            $table->string('spotlight', 255)->default('');
            $table->string('get_featured', 255)->default('');
            $table->string('message', 255)->default('');
            $table->boolean('status')->default(false);
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
        Schema::dropIfExists('subscription_histories');
    }
};
