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
        Schema::create('supplier_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_invoice_id')->constrained('supplier_invoices')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('size_id')->nullable()->constrained('sizes')->nullOnDelete();
            $table->integer('quantity')->default(1)->comment('الكمية');
            $table->decimal('pricePerMeter', 15, 2)->default(0)->comment('سعر المتر');
            $table->decimal('length', 15, 2)->default(0)->comment('الطول');
            $table->decimal('purchase_price', 15, 2)->default(0)->comment('سعر الشراء');
            $table->decimal('total_price', 15, 2)->default(0)->comment('سعر الإجمالي');
            $table->decimal('final_cost_price', 15, 2)->default(0)->comment('السعر النهائي لكل صنف');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invoice_items');
    }
};
