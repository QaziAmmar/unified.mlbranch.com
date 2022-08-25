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

            $table->string('status', 32)->default(1);
            $table->string('g_token', 32)->default('');
            $table->string('fb_token', 32)->default('');
            $table->string('apl_token', 32)->default('');
            $table->string('lat', 32)->default('');
            $table->string('long', 32)->default('');
            $table->string('email_code', 32)->default('');
            $table->string('device_id', 32)->default('');

            $table->string('bio', 32)->default('');
            $table->string('pictures', 32)->default('');
            $table->string('skills', 32)->default('');
            $table->string('interstes', 32)->default('');
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
            
            $table->dropColumn('status', 32);
            $table->dropColumn('g_token', 32);
            $table->dropColumn('fb_token', 32);
            $table->dropColumn('apl_token', 32);
            $table->dropColumn('lat', 32);
            $table->dropColumn('long', 32);
            $table->dropColumn('email_code', 32);
            $table->dropColumn('device_id', 32);

            $table->dropColumn('bio');
            $table->dropColumn('pictures');
            $table->dropColumn('skills');
            $table->dropColumn('interstes');
        });

    }
};
