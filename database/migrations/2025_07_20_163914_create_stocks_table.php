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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_house_id')->constrained()->onDelete('cascade');
            $table->integer('size')->nullable()->default(0)->comment('المقاس');
            $table->foreignId('unit_id')->nullable()->constrained('units')->nullOnDelete();
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
        Schema::dropIfExists('stocks');
    }
};
