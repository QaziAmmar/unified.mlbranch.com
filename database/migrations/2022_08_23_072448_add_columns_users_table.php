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
            $table->string('bio')->nullable();
            $table->string('pictures')->nullable();
            $table->string('skills')->nullable();
            $table->string('interstes')->nullable();
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
            $table->dropColumn('bio')->nullable()->default(null);
            $table->dropColumn('pictures')->nullable()->change();
            $table->dropColumn('skills')->nullable()->default(null);
            $table->dropColumn('interstes')->nullable()->default(null);
        });

    }
};
