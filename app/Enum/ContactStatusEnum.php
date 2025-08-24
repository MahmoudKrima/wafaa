<?php

namespace App\Enum;

enum ContactStatusEnum: string
{
    case PENDING = 'pending';
    case REPLIED = 'replied';

    public function lang(): string
    {
        return match ($this) {
            self::PENDING => __("admin.pending"),
            self::REPLIED => __("admin.replied"),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::PENDING => 'btn btn-warning btn-sm text-center',
            self::REPLIED => 'btn btn-success btn-sm text-center',
        };
    }

    public static function vals(): array
    {
        return [
            self::PENDING->value,
            self::REPLIED->value,
        ];
    }
}
