<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First change the enum definition to include 'open'
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('status', ['submitted', 'open', 'in_progress', 'completed', 'cancel'])->default('submitted')->change();
        });
        
        // Then update existing records from 'submitted' to 'open'
        DB::table('tickets')->where('status', 'submitted')->update(['status' => 'open']);
        
        // Finally remove 'submitted' from the enum and set 'open' as default
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('status', ['open', 'in_progress', 'completed', 'cancel'])->default('open')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First add 'submitted' back to the enum
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('status', ['submitted', 'open', 'in_progress', 'completed', 'cancel'])->default('open')->change();
        });
        
        // Then update existing records from 'open' to 'submitted'
        DB::table('tickets')->where('status', 'open')->update(['status' => 'submitted']);
        
        // Finally remove 'open' from the enum and set 'submitted' as default
        Schema::table('tickets', function (Blueprint $table) {
            $table->enum('status', ['submitted', 'in_progress', 'completed', 'cancel'])->default('submitted')->change();
        });
    }
};
