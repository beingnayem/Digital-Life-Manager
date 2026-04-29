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
        Schema::create('moods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Primary mood metric
            $table->unsignedTinyInteger('mood_level'); // 1-10 scale
            $table->string('mood_label', 50)->nullable(); // 'happy', 'sad', 'anxious', etc.
            
            // Related metrics
            $table->unsignedTinyInteger('energy_level')->nullable(); // 1-10
            $table->unsignedTinyInteger('stress_level')->nullable(); // 1-10
            $table->unsignedTinyInteger('focus_level')->nullable(); // 1-10
            
            // Context & details
            $table->json('emotion_tags')->nullable(); // Array of emotions
            $table->text('notes')->nullable(); // User notes about their mood
            $table->json('activities')->nullable(); // Array of activities (exercise, work, etc.)
            
            // Health metrics
            $table->decimal('sleep_hours', 3, 1)->nullable(); // 0-24 hours
            $table->string('weather', 50)->nullable(); // 'sunny', 'rainy', etc.
            $table->string('location', 100)->nullable(); // Where mood was recorded
            
            // Date tracking
            $table->date('recorded_date'); // Date of mood entry
            $table->timestamp('recorded_at'); // Exact time
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('recorded_date');
            $table->index('mood_level');
            $table->index('created_at');
            $table->index(['user_id', 'recorded_date']); // Composite index
            $table->unique(['user_id', 'recorded_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moods');
    }
};
