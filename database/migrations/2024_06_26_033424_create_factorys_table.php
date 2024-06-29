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
        Schema::create('factories', function (Blueprint $table) {
            $table->foreignId('id')->constrained('users');
            $table->integer('level')->default(1);
            $table->timestamps();
            $table->primary('id');
        });

        Schema::create('factory_mst', function (Blueprint $table) {
            $table->increments('level')->primary()->index();
            $table->integer('product_count');
            $table->integer('need_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factories');
        Schema::dropIfExists('factory_mst');
    }
};
