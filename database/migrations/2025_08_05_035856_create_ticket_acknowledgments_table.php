<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_acknowledgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('anonymous_identifier')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            
            $table->unique(['ticket_id', 'user_id'], 'unique_user_acknowledgment');
            $table->unique(['ticket_id', 'anonymous_identifier'], 'unique_anonymous_acknowledgment');
            $table->index(['ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_acknowledgments');
    }
};
