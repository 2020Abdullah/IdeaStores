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
            $table->foreignId('stock_id')->nullable()->constrained('stocks')->nullOnDelete()->cascadeOnUpdate();
            $table->integer('quantity')->default(1)->comment('الكمية');
            $table->decimal('price', 15, 2)->default(0)->comment('سعر البيع');
            $table->decimal('total_price', 15, 2)->default(0)->comment('سعر الإجمالي');
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
