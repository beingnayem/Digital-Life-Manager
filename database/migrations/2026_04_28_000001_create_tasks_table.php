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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Task content
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('category', 100)->nullable();
            
            // Task properties
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'archived', 'cancelled'])
                ->default('not_started');
            
            // Dates
            $table->dateTime('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Time tracking
            $table->unsignedInteger('estimated_hours')->nullable();
            $table->unsignedInteger('actual_hours')->nullable();
            
            // UI & organization
            $table->string('color_tag', 7)->default('#3b82f6');
            
            // Recurrence
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern', 50)->nullable(); // 'daily', 'weekly', 'monthly'
            
            // Flexible data
            $table->json('tags')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['user_id', 'status']); // Composite index
            $table->index('status');
            $table->index('due_date');
            $table->index('priority');
            $table->index('created_at');
            $table->fullText(['title', 'description']); // Full-text search
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
