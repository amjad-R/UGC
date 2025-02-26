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
    Schema::create('monthly_stats', function (Blueprint $table) {
        $table->id();
        $table->date('month'); // الشهر والسنة
        $table->integer('business_users')->default(0);
        $table->integer('creator_users')->default(0);
        $table->integer('services_count')->default(0);
        $table->decimal('revenue', 15, 2)->default(0.00);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_stats');
    }
};
