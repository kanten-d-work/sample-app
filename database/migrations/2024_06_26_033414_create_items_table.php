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
        Schema::create('items', function (Blueprint $table) {
            $table->foreignId('id')->constrained('users');
            $table->integer('item_id');
            $table->integer('count')->default(0);
            $table->timestamps();
            $table->primary(['id', 'item_id']);
        });

        Schema::create('item_mst', function (Blueprint $table) {
            $table->integer('item_id')->primary()->index();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
        Schema::dropIfExists('item_mst');
    }
};
