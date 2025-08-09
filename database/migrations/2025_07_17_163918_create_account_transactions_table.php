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
        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            // نوع الحركة: دخلت فلوس ولا خرجت؟
            $table->enum('direction', ['in', 'out']); // in = credit, out = debit
            
            // وسيلة الدفع
            $table->enum('method', ['cash', 'bank', 'vodafone_cash', 'instapay'])->nullable();

            // المبلغ
            $table->decimal('amount', 15, 2);

            // نوع المعاملة (تفصيل نوعها لتقارير الربحية وغيرها)
            $table->enum('transaction_type', [
                'payment',      // دفع
                'expense',      // مصروف
                'purchase',     // مشتريات (فاتورة مورد)
                'sale',     // مبيعات (فاتورة بيع)
                'added',   // تسوية يدوية
                'transfer',     // تحويل بين حسابين
                'open_balance',     // رصيد افتتاحي
            ]);

            $table->nullableMorphs('related'); 

            // وصف أو ملاحظة اختيارية
            $table->string('description')->nullable();
            $table->string('source_code')->nullable();
            $table->date('date')->nullable()->comment('تاريخ الإضافة بتاريخ الفاتورة');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_transactions');
    }
};
