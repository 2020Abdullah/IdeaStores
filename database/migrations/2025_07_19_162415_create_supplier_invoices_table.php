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
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('suppliers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('invoice_code')->uniqid();
            $table->date('invoice_date');
            $table->enum('invoice_type', ['cash', 'credit', 'opening_balance']);
            $table->tinyInteger('invoice_staute')->default(0)->comment('حالة الفاتورة');
            $table->decimal('paid_amount', 15, 2)->default(0)->comment('إجمالي المدفوع');
            $table->decimal('cost_price', 15,2)->default(0)->comment('التكاليف');
            $table->decimal('total_amount', 15,2)->default(0)->comment('إجمالي الفاتورة');
            $table->decimal('total_amount_invoice', 15,2)->default(0)->comment('إجمالي الفاتورة بدون تكاليف');
            $table->text('notes')->nullable();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('SET NULL')->cascadeOnUpdate();
            $table->foreignId('wallet_id')->nullable()->constrained('wallets')->onDelete('SET NULL')->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_invoices');
    }
};
