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
        $table->string('role')->nullable(); // يمكن أن يكون NULL إذا كان الحقل اختياريًا
    });
}

public function down()
{
    Schema::table('user_profiles', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}

};
