<?php

use App\Enum\ActivationStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')
                ->nullable();
            $table->string('password');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('admins')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('image');
            $table->enum('status', ActivationStatusEnum::vals());
            $table->string('token')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
