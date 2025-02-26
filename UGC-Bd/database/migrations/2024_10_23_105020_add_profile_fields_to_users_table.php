<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'first_name')) {
                    $table->string('first_name')->nullable();
                }
                if (!Schema::hasColumn('users', 'last_name')) {
                    $table->string('last_name')->nullable();
                }
                if (!Schema::hasColumn('users', 'phone')) {
                    $table->string('phone')->nullable();
                }
                if (!Schema::hasColumn('users', 'profile_image')) {
                    $table->string('profile_image')->nullable();
                }
                if (!Schema::hasColumn('users', 'social_link')) {
                    $table->string('social_link')->nullable();
                }
                if (!Schema::hasColumn('users', 'service_description')) {
                    $table->text('service_description')->nullable();
                }
                if (!Schema::hasColumn('users', 'hashtags')) {
                    $table->string('hashtags')->nullable();
                }
                if (!Schema::hasColumn('users', 'identity_pdf')) {
                    $table->string('identity_pdf')->nullable();
                }
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {});
    }
};
