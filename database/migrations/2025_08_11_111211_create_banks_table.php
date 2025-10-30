<?php

use App\Enum\ActivationStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->morphs('bankable');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete();
            $table->string('name');
            $table->string('image');
            $table->string('account_owner');
            $table->string('account_number');
            $table->string('iban_number');
            $table->enum('status', ActivationStatusEnum::vals());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
