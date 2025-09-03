<?php

namespace App\Enum;

enum WalletLogTypeEnum: string
{
    case SHIPPMENT = 'shippment';
    case TRANSACTION = 'transaction';
    case EDITBALANCE = 'edit_balance';
    case OTHER = 'other';

    public function lang(): string
    {
        return match ($this) {
            self::SHIPPMENT => __("admin.issue_shippment"),
            self::TRANSACTION => __("admin.balace_transfer"),
            self::EDITBALANCE => __("admin.edit_balance"),
            self::OTHER => __("admin.other"),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::SHIPPMENT => 'badge bg-success text-center',
            self::TRANSACTION => 'badge bg-warning text-center',
            self::EDITBALANCE => 'badge bg-info text-center',
            self::OTHER => 'badge bg-secondary text-center',
        };
    }

    public static function vals(): array
    {
        return [
            self::SHIPPMENT->value,
            self::TRANSACTION->value,
            self::EDITBALANCE->value,
            self::OTHER->value,
        ];
    }
}
