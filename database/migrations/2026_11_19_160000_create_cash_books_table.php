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
        Schema::create('cash_books', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['deposit', 'expense'])->default('expense');
            $table->string('category', 100);
            $table->date('date');
            $table->decimal('amount', 14, 2);
            $table->string('payment_method', 50)->default('নগদ');
            $table->string('reference_no', 100)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['date', 'type']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_books');
    }
};
