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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('SET Null');
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('cascade');
            $table->enum('type', ['in','out'])->comment('in => إضافة / out => خصم'); 
            $table->decimal('quantity',15,2)->comment('الكمية المحركة');   
            $table->text('note')->nullable()->comment('شراء / بيع'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
