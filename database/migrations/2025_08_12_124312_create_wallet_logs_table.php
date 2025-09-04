<?php

use App\Enum\WalletLogTypeEnum;
use App\Enum\TransactionTypeEnum;
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
        Schema::create('wallet_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('trans_type', WalletLogTypeEnum::vals());
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('admin_id')
                ->nullable()
                ->constrained('admins')
                ->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('type', TransactionTypeEnum::vals());
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_logs');
    }
};
