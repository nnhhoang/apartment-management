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
        Schema::create('prefectures', function (Blueprint $table) {
            $table->id();
            $table->string('ward_id', 256);
            $table->string('ward_name', 256);
            $table->string('ward_name_en', 256)->nullable();
            $table->string('ward_level', 256)->nullable();
            $table->string('district_id', 256);
            $table->string('district_name', 256);
            $table->string('province_id', 256);
            $table->string('province_name', 256);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prefectures');
    }
};
