<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained();  // رابط الفيديو
            $table->foreignId('user_id')->constrained();  // رابط المستخدم
            $table->timestamps();
            $table->unique(['service_id', 'user_id']);  // منعه من إعطاء لايكات متعددة لنفس الفيديو من نفس المستخدم
        });
    }

    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
