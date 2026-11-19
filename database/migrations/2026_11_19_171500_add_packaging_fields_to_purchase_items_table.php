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
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->string('box', 50)->nullable()->after('purchase_product_id');
            $table->string('poly', 50)->nullable()->after('box');
            $table->string('meer', 50)->nullable()->after('poly');
            $table->string('unit', 50)->nullable()->default('পোনা')->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['box', 'poly', 'meer', 'unit']);
        });
    }
};
