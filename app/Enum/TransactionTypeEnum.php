<?php

namespace App\Enum;

enum TransactionTypeEnum: string
{
    case DEPOSIT = 'deposit';
    case DEDUCT = 'deduct';

    public function lang(): string
    {
        return match ($this) {
            self::DEPOSIT => __("admin.deposit"),
            self::DEDUCT => __("admin.deduct"),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::DEPOSIT => 'badge bg-success text-center',
            self::DEDUCT => 'badge bg-danger text-center',
        };
    }

    public static function vals(): array
    {
        return [
            self::DEPOSIT->value,
            self::DEDUCT->value,
        ];
    }
}
