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
        Schema::create('customer_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('code')->uniqid();
            $table->date('date');
            $table->enum('type', ['cash', 'credit', 'opening_balance']);
            $table->tinyInteger('staute')->default(0)->comment('حالة الفاتورة');
            $table->decimal('paid_amount', 15, 2)->default(0)->comment('إجمالي المدفوع');
            $table->decimal('cost_price', 15,2)->default(0)->comment('التكاليف');
            $table->decimal('total_amount_without_discount', 15,2)->default(0)->comment('إجمالي الفاتورة بدون خصم');
            $table->decimal('total_amount', 15,2)->default(0)->comment('إجمالي الفاتورة');
            $table->decimal('total_profit', 15, 2)->default(0)->comment('سعر الإجمالي');
            $table->text('notes')->nullable();
            $table->enum('discount_type', ['value', 'percent'])->default('value'); 
            $table->integer('discount_value')->default(0); 
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->onDelete('SET NULL')->cascadeOnUpdate();
            $table->foreignId('wallet_id')->nullable()->constrained('wallets')->onDelete('SET NULL')->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_invoices');
    }
};
