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
            self::DEPOSIT => 'btn btn-success btn-sm text-center',
            self::DEDUCT => 'btn btn-danger btn-sm text-center',
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
