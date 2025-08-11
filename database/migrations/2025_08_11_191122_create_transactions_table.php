<?php

use App\Enum\TransactionStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('amount', 8, 2);
            $table->foreignId('banks_id')
                ->nullable()
                ->constrained('banks')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('attachment')
                ->nullable();
            $table->enum('status', TransactionStatusEnum::vals())
                ->default('pending');
            $table->foreignId('accepted_by')
                ->nullable()
                ->constrained('admins')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
