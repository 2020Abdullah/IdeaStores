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
        Schema::create('exponse_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('affect_debt')->comment('هل يؤثر علي المديونة ؟');
            $table->boolean('affect_wallet')->comment('هل يؤثر علي الخزنة');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exponse_items');
    }
};
