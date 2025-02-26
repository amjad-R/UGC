<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->integer('global_followers')->default(0); // الحقل الجديد
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('global_followers');
        });
    }

};
