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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Action details
            $table->string('action', 255); // 'created', 'updated', 'deleted', etc.
            $table->string('entity_type', 100); // 'Task', 'Expense', 'Note', etc.
            $table->unsignedBigInteger('entity_id')->nullable(); // ID of affected record
            
            // Data changes
            $table->json('old_values')->nullable(); // Previous values
            $table->json('new_values')->nullable(); // New values
            
            // Request information
            $table->ipAddress()->nullable(); // IPv4 or IPv6
            $table->text('user_agent')->nullable(); // Browser/client info
            
            // Timestamp
            $table->timestamp('created_at');
            
            // Indexes for querying
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
            $table->index(['entity_type', 'entity_id']);
            $table->index(['user_id', 'created_at']); // Composite
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
