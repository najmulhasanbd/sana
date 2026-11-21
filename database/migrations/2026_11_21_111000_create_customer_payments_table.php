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
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_no', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->decimal('previous_due', 12, 2)->default(0);
            $table->decimal('remaining_due', 12, 2)->default(0);
            $table->string('payment_method', 50)->default('Cash');
            $table->string('transaction_id', 100)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('sale_payments', function (Blueprint $table) {
            $table->foreignId('customer_payment_id')
                ->nullable()
                ->after('sales_id')
                ->constrained('customer_payments')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_payments', function (Blueprint $table) {
            $table->dropForeign(['customer_payment_id']);
            $table->dropColumn('customer_payment_id');
        });

        Schema::dropIfExists('customer_payments');
    }
};
