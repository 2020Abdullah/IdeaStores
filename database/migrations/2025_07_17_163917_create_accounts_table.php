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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->nullableMorphs('accountable');
            $table->enum('type', ['warehouse', 'supplier', 'customer']);
            $table->decimal('total_profit_balance', 15, 2)->default(0);
            $table->tinyInteger('is_main')->default(0);
            $table->decimal('current_balance', 15,2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
