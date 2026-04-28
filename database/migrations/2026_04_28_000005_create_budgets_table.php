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
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Budget details
            $table->string('category', 100);
            $table->decimal('limit_amount', 10, 2); // Budget limit
            $table->string('month_year', 7); // Format: 'YYYY-MM'
            $table->decimal('spent_amount', 10, 2)->default(0); // Running total
            
            // Alert settings
            $table->unsignedTinyInteger('alert_threshold')->default(80); // Alert at 80%
            $table->boolean('is_active')->default(true);
            
            // Timestamps
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('month_year');
            $table->index(['user_id', 'month_year']);
            
            // One budget per category per month per user
            $table->unique(['user_id', 'category', 'month_year']);
            
            // Constraints
            $table->check('limit_amount > 0');
            $table->check('spent_amount >= 0');
            $table->check('alert_threshold BETWEEN 0 AND 100');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
