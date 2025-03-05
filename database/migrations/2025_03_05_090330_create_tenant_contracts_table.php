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
        Schema::create('tenant_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->integer('pay_period')->comment('1: 1 tháng, 3: 3 tháng, 6: 6 tháng, 12: 1 năm');
            $table->bigInteger('price');
            $table->integer('electricity_pay_type')->comment('1: Per person, 2: Fixed per room, 3: By usage');
            $table->bigInteger('electricity_price');
            $table->integer('electricity_number_start');
            $table->integer('water_pay_type')->comment('1: Per person, 2: Fixed per room, 3: By usage');
            $table->bigInteger('water_price');
            $table->integer('water_number_start');
            $table->integer('number_of_tenant_current');
            $table->text('note')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_contracts');
    }
};
