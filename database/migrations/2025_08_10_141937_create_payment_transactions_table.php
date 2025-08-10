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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            // الجهة التي تم دفع المبلغ لها: عميل أو مورد
            $table->nullableMorphs('related'); // related_type: Customer / Supplier

            // مصدر الدفع: فاتورة بيع أو شراء
            $table->nullableMorphs('source'); // source_type: SaleInvoice / SupplierInvoice

            $table->enum('direction', ['in', 'out']); // in = دخل (من عميل) / out = خرج (إلى مورد)
            $table->decimal('amount', 15, 2);
            $table->date('payment_date');
            $table->enum('method', ['cash', 'bank', 'vodafone_cash', 'instapay'])->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
