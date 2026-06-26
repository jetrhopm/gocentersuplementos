<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->id();
            $table->string('postal_code', 5)->index();
            $table->string('settlement', 160);
            $table->string('settlement_type', 80)->nullable();
            $table->string('municipality', 120);
            $table->string('state', 120);
            $table->string('city', 120)->nullable();
            $table->string('zone', 40)->nullable();
            $table->string('state_code', 8)->nullable();
            $table->string('municipality_code', 8)->nullable();
            $table->timestamps();

            $table->unique(['postal_code', 'settlement', 'municipality', 'state'], 'postal_codes_unique_settlement');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postal_codes');
    }
};
