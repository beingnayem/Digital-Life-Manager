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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            // Expense details
            $table->decimal('amount', 10, 2); // Up to 99,999,999.99
            $table->string('category', 100);
            $table->string('description', 500)->nullable();
            
            // Payment info
            $table->enum('payment_method', [
                'cash',
                'card',
                'check',
                'bank_transfer',
                'mobile_payment',
                'other'
            ])->default('card');
            
            // Timestamps
            $table->date('date'); // Date of expense
            
            // Receipt & documents
            $table->string('receipt_url', 500)->nullable();
            
            // Status tracking
            $table->enum('status', ['pending', 'confirmed', 'disputed', 'refunded'])
                ->default('confirmed');
            
            // Tags & notes
            $table->json('tags')->nullable();
            
            // Budget tracking
            $table->boolean('budget_alert_sent')->default(false);
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('category');
            $table->index('date');
            $table->index('amount');
            $table->index('created_at');
            $table->index(['user_id', 'date']); // Composite: user expenses by date
            
            // Constraints
            $table->check('amount > 0'); // Prevent negative amounts
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
