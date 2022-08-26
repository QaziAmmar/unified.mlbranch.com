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
        //
        Schema::table('users', function (Blueprint $table) {
            $table->string('age');
            $table->string('gender');
            $table->string('role');
            $table->string('looking_for');
            $table->string('firebase_id');

            $table->string('status', 255)->default(1);
            $table->string('g_token', 255)->default('');
            $table->string('fb_token', 255)->default('');
            $table->string('apl_token', 255)->default('');
            $table->string('lat', 255)->default('');
            $table->string('long', 255)->default('');
            $table->string('email_code', 255)->default('');
            $table->string('device_id', 255)->default('');

            $table->string('bio', 255)->default('');
            $table->json('pictures')->default("[]");
            $table->json('skills')->default("[]");
            $table->json('interstes')->default("[]");
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

        Schema::table('users', function(Blueprint $table){
            $table->dropColumn('age');
            $table->dropColumn('gender');
            $table->dropColumn('role');
            $table->dropColumn('looking_for');
            $table->dropColumn('firebase_id');
            
            $table->dropColumn('status');
            $table->dropColumn('g_token');
            $table->dropColumn('fb_token');
            $table->dropColumn('apl_token');
            $table->dropColumn('lat');
            $table->dropColumn('long');
            $table->dropColumn('email_code');
            $table->dropColumn('device_id');

            $table->dropColumn('bio');
            $table->dropColumn('pictures');
            $table->dropColumn('skills');
            $table->dropColumn('interstes');
        });

    }
};
