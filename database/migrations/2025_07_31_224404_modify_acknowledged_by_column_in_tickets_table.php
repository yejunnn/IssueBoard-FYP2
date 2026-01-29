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
        Schema::table('tickets', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['acknowledged_by']);
            
            // Modify the column to allow negative values
            $table->integer('acknowledged_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Re-add the foreign key constraint
            $table->foreign('acknowledged_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
