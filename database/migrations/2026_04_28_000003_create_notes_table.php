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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Note content
            $table->string('title');
            $table->longText('content');
            $table->string('category', 100)->nullable();
            
            // UI & organization
            $table->string('color_tag', 7)->default('#fbbf24'); // Yellow default
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_archived')->default(false);
            
            // Organization
            $table->json('tags')->nullable();
            $table->json('attachments')->nullable(); // Array of attachment metadata
            
            // Collaboration
            $table->json('collaborator_ids')->nullable(); // Array of user IDs
            $table->enum('permission_level', ['private', 'shared', 'public'])
                ->default('private');
            
            // Analytics
            $table->unsignedInteger('word_count')->default(0);
            $table->unsignedInteger('reading_time')->default(0); // In minutes
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('user_id');
            $table->index('is_pinned'); // Quick access to pinned notes
            $table->index('is_archived');
            $table->index('category');
            $table->index('created_at');
            $table->index('updated_at');
            $table->fullText(['title', 'content']); // Full-text search
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
