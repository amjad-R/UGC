<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::table('chats')->whereNull('receiver_id')->update(['receiver_id' => 1]);
        DB::table('chats')->where('receiver_id', '<', 1)->update(['receiver_id' => 0]);

        Schema::table('chats', function (Blueprint $table) {
            $table->unsignedBigInteger('receiver_id')->nullable()->change();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->unsignedBigInteger('receiver_id')->nullable(false)->change();
        });
    }
};
