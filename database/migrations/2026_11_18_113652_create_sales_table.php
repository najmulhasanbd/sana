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
        if (! Schema::hasTable('customers')) {
            Schema::create('customers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete();
                $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
                $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
                $table->string('name', 150);
                $table->string('mobile', 30)->unique();
                $table->string('email', 100)->nullable();
                $table->text('address')->nullable();
                $table->text('note')->nullable();
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
            });
        }

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 50)->unique();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->date('sale_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('due_amount', 12, 2)->default(0);
            $table->string('payment_status', 30)->default('due')->index(); // 'paid', 'partial', 'due'
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('sale_product_id')->constrained('sale_products')->cascadeOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->string('unit', 50)->default('কেজি'); // কেজি, পিস, মণ, গ্রাম, ড্রাম, ইত্যাদি
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });

        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->constrained('sales')->cascadeOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 50)->default('Cash'); // 'Cash', 'bKash', 'Nagad', 'Rocket', 'Bank', etc.
            $table->string('transaction_id', 100)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('customers');
    }
};
