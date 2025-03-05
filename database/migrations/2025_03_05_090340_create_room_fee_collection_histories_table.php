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
        Schema::create('room_fee_collection_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_fee_collection_id')->constrained()->cascadeOnDelete();
            $table->dateTime('paid_date');
            $table->bigInteger('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_fee_collection_histories');
    }
};
