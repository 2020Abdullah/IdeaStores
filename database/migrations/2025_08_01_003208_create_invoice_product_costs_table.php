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
            $table->unsignedBigInteger('supplier_invoice_id');
            $table->unsignedBigInteger('stock_id');
            $table->decimal('base_cost', 10, 2)->comment('السعر الأساسي من المورد');
            $table->decimal('cost_share', 10, 2)->comment('سعر التكلفة للصنف');
            $table->decimal('suggested_price', 10, 2)->nullable()->comment('سعر البيع المقترح');
            $table->integer('rate')->default(0)->comment('النسبة لعمل البيع المقترح');
            $table->string('source_code')->nullable();
            $table->date('date')->nullable();
            $table->timestamps();
        
            // المفتاح الفريد المركب مباشرة
            $table->unique(['supplier_invoice_id', 'stock_id']);
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
