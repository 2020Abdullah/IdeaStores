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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('account_id')->constrained()->onDelete('cascade'); // الحساب المالي المرتبط (خزنة)
            
            $table->string('name'); 

            $table->enum('method', ['cash', 'bank', 'vodafone_cash', 'instapay']); 
        
            $table->string('details')->nullable();
            
            $table->decimal('current_balance', 8, 2)->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
