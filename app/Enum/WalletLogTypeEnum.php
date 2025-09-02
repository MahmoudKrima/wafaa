<?php

namespace App\Enum;

enum WalletLogTypeEnum: string
{
    case SHIPPMENT = 'shippment';
    case TRANSACTION = 'transaction';
    case OTHER = 'other';

    public function lang(): string
    {
        return match ($this) {
            self::SHIPPMENT => __("admin.shippment"),
            self::TRANSACTION => __("admin.transaction"),
            self::OTHER => __("admin.other"),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::SHIPPMENT => 'btn btn-success btn-sm text-center',
            self::TRANSACTION => 'btn btn-warning btn-sm text-center',
            self::OTHER => 'btn btn-secondary btn-sm text-center',
        };
    }

    public static function vals(): array
    {
        return [
            self::SHIPPMENT->value,
            self::TRANSACTION->value,
            self::OTHER->value,
        ];
    }
}
