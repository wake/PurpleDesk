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
            // full_name 設為可 null (選填)
            $table->string('full_name')->nullable()->change();
            // display_name 設為不可 null (必填)  
            $table->string('display_name')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 還原變更
            $table->string('full_name')->nullable(false)->change();
            $table->string('display_name')->nullable()->change();
        });
    }
};
