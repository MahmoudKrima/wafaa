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
            self::PENDING => 'badge bg-warning text-center',
            self::REPLIED => 'badge bg-success text-center',
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
