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
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('system_qty')->comment('الكمية الدفترية'); // الكمية الدفترية (من النظام)
            $table->integer('actual_qty')->comment('القيمة الفعلية'); // الكمية الفعلية (من الجرد اليدوي)
            $table->integer('difference')->comment('قيمة الجرد'); // actual - system
            $table->date('date')->nullable()->comment('تاريخ الإضافة');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
    }
};
