<?php

namespace App\Enum;

enum TransactionStatusEnum: string
{
    case ACCEPTED = 'accepted';
    case PENDING = 'pending';
    case REJECTED = 'rejected';

    public function lang(): string
    {
        return match ($this) {
            self::ACCEPTED => __("admin.accepted"),
            self::PENDING => __("admin.pending"),
            self::REJECTED => __("admin.rejected"),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::ACCEPTED => 'btn btn-success btn-sm text-center',
            self::PENDING => 'btn btn-warning btn-sm text-center',
            self::REJECTED => 'btn btn-danger btn-sm text-center',
        };
    }

    public static function vals(): array
    {
        return [
            self::ACCEPTED->value,
            self::PENDING->value,
            self::REJECTED->value,
        ];
    }
}
