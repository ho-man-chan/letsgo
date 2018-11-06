<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserProfile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('users', function (Blueprint $table) {
            $table->date('birthday');
            $table->string('civic');
            $table->string('street_name');
            $table->string('city');
            $table->string('province');
            $table->string('postal_address');
            $table->string('country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('birthday');
            $table->dropColumn('street_number');
            $table->dropColumn('street_name');
            $table->dropColumn('city');
            $table->dropColumn('province');
            $table->dropColumn('postal_address');
            $table->dropColumn('country');
        });
    }
}
