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
        Schema::create('exponses', function (Blueprint $table) {
            $table->id();
            $table->morphs('expenseable');
            $table->foreignId('expense_item_id')->constrained('exponse_items')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->onDelete('SET NULL')->cascadeOnUpdate();
            $table->enum('type', ['value', 'percent'])->default('value'); 
            $table->decimal('amount', 15, 2);
            $table->text('note')->nullable();
            $table->date('date')->nullable()->comment('تاريخ الإضافة بتاريخ الفاتورة');
            $table->string('source_code')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exponses');
    }
};
