<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * تشغيل الهجرة.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('start_date')->nullable();  // التاريخ البداية
            $table->date('end_date')->nullable();    // التاريخ النهاية
            $table->string('email');
            $table->string('website')->nullable();
            $table->text('message');
            $table->integer('facebook')->default(0);   // القيمة الافتراضية هي 0
            $table->integer('instagram')->default(0);  // القيمة الافتراضية هي 0
            $table->integer('tiktok')->default(0);     // القيمة الافتراضية هي 0
            $table->integer('youtube')->default(0);    // القيمة الافتراضية هي 0
            $table->json('platform_data')->nullable(); // لتخزين تفاصيل المنصات
            $table->decimal('price', 10, 2)->default(0.00); // لتخزين السعر
            $table->timestamps(); // سيتم إضافة created_at و updated_at
        });
    }

    /**
     * التراجع عن الهجرة.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
