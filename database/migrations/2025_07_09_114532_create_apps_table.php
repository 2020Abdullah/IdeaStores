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
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable();
            $table->string('company_name')->nullable();
            $table->text('company_info')->nullable();
            $table->string('Tax_number')->nullable()->comment('رقم التعريف الضريبي');
            $table->tinyInteger('is_active')->default(1);
            $table->string('secret_key');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
