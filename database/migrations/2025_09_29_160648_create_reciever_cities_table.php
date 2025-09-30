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
        Schema::create('reciever_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reciever_id')
                ->constrained('recievers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('city_id');
            $table->string('shipping_company_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reciever_cities');
    }
};
