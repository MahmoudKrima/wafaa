<?php

namespace App\Enum;

enum NotificationTypeEnum: string
{
    case TRANSACTION_CREATED = 'transaction_created';
    case TRANSACTION_REJECTED = 'transaction_rejected';
    case TRANSACTION_ACCEPTED = 'transaction_accepted';
    case NEWSHIPMENT = 'newshipment';
    case BALANCEDEPOSITED = 'balance_deposited';
    case BALANCEDEDUCTION = 'balance_deduction';

    public function lang(): string
    {
        return match ($this) {
            self::TRANSACTION_CREATED => __("admin.transaction_created"),
            self::TRANSACTION_REJECTED => __("admin.transaction_rejected"),
            self::TRANSACTION_ACCEPTED => __("admin.transaction_accepted"),
            self::NEWSHIPMENT => __("admin.newshipment"),
            self::BALANCEDEPOSITED => __("admin.balance_deposited"),
            self::BALANCEDEDUCTION => __("admin.balance_deduction"),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::TRANSACTION_CREATED => 'badge bg-success btn-sm text-center',
            self::TRANSACTION_REJECTED => 'badge bg-danger btn-sm text-center',
            self::TRANSACTION_ACCEPTED => 'badge bg-success btn-sm text-center',
            self::NEWSHIPMENT => 'badge bg-success btn-sm text-center',
            self::BALANCEDEPOSITED => 'badge bg-success btn-sm text-center',
            self::BALANCEDEDUCTION => 'badge bg-danger btn-sm text-center',
        };
    }

    public static function vals(): array
    {
        return [
            self::TRANSACTION_CREATED->value,
            self::TRANSACTION_REJECTED->value,
            self::TRANSACTION_ACCEPTED->value,
            self::NEWSHIPMENT->value,
            self::BALANCEDEPOSITED->value,
            self::BALANCEDEDUCTION->value,
        ];
    }
}
