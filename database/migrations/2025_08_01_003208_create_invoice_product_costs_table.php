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
        Schema::create('invoice_product_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->nullable()->constrained('stocks')->onDelete('SET NULL');
            $table->decimal('base_cost', 10, 2)->comment('السعر الأساسي من المورد');         // السعر الأساسي من المورد
            $table->decimal('cost_share', 10, 2)->comment('سعر التكلفة للصنف');        // نصيب المنتج من التكاليف
            $table->decimal('suggested_price', 10, 2)->nullable()->comment('سعر البيع المقترح'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_product_costs');
    }
};
