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
        Schema::create('customer_invoices_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_invoice_id')->constrained('customer_invoices')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('SET NULL')->cascadeOnUpdate();
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('SET NULL')->cascadeOnUpdate();          
            $table->string('unit_name')->nullable();
            $table->integer('size')->default(0)->comment('المقاس');
            $table->integer('quantity')->comment('الكمية');
            $table->integer('length')->default(0)->comment('الطول / القطر');
            $table->decimal('sale_price', 15, 2)->default(0)->comment('سعر بيع الوحدة');
            $table->decimal('total_price', 15, 2)->default(0)->comment('سعر الإجمالي');
            $table->decimal('profit', 15, 2)->default(0)->comment('هامش الربح للصنف');
            $table->decimal('total_profit', 15, 2)->default(0)->comment('إجمالي هامش الربح');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_invoices_items');
    }
};
