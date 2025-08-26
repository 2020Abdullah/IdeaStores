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
        Schema::create('customer_dues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('customer_invoice_id')->nullable()->constrained('customer_invoices')->onDelete('SET NULL');
            $table->string('description')->nullable(); // رصيد افتتاحي / فاتورة آجل / تسوية
            $table->decimal('amount', 15, 2); // إجمالي الدين
            $table->decimal('paid_amount', 15, 2)->default(0); // ما تم دفعه
            $table->date('due_date')->nullable(); // تاريخ الاستحقاق
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_dues');
    }
};
