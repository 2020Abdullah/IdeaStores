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
        Schema::create('external_debts', function (Blueprint $table) {
            $table->id();
            $table->morphs('debtable');
            $table->string('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('paid', 15, 2);
            $table->decimal('remaining', 15, 2);
            $table->boolean('is_paid')->default(0);
            $table->date('date')->nullable()->comment('تاريخ الإضافة بتاريخ الفاتورة');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_debts');
    }
};
