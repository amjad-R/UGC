<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChangesToUserStatisticsTable extends Migration
{
    public function up()
    {
        Schema::table('user_statistics', function (Blueprint $table) {
            $table->decimal('transactions_change', 5, 2)->nullable();
            $table->decimal('payments_change', 5, 2)->nullable();
            $table->decimal('profit_change', 5, 2)->nullable();
            $table->decimal('sales_change', 5, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('user_statistics', function (Blueprint $table) {
            $table->dropColumn(['transactions_change', 'payments_change', 'profit_change', 'sales_change']);
        });
    }
}
