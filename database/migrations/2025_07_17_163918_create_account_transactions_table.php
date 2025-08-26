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
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('wallet_id')->nullable()->constrained('wallets')->onDelete('SET NULL')->cascadeOnUpdate();
    
            // حركة مالية دخل أو خرج
            $table->enum('direction', ['in', 'out']); 
            
            // المبلغ
            $table->decimal('amount', 15, 2);
        
            // الربحية من الحركة (لو مبيعات)
            $table->decimal('profit_amount', 15, 2)->default(0)->comment('الربح من العملية إن وجد');
        
            // نوع المعاملة (تفصيل نوعها لتقارير الربحية وغيرها)
            $table->enum('transaction_type', [
                'payment',        // دفع
                'expense',        // مصروف
                'purchase',       // مشتريات (فاتورة مورد)
                'sale',           // مبيعات (فاتورة بيع)
                'profit_adjust',  // إضافة ربحية بدون حركة رأس مال
                'added',          // تسوية يدوية
                'transfer',       // تحويل بين حسابين
                'open_balance',   // رصيد افتتاحي
            ]);
        
            // كيان مرتبط بالحركة (فاتورة، عميل، مورد، الخ)
            $table->nullableMorphs('related'); 
        
            // وصف أو ملاحظة
            $table->string('description')->nullable();
            
            // كود مرجعي (مثلاً رقم الفاتورة)
            $table->string('source_code')->nullable();
            
            // تاريخ الحركة (ممكن يختلف عن تاريخ الإدخال)
            $table->date('date')->nullable()->comment('تاريخ العملية الحقيقي'); 
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
