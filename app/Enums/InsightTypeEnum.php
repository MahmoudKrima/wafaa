<?php

namespace App\Enums;

enum InsightTypeEnum: string
{
    case PERCENT = 'percent';
    case FIXED = 'fixed';

    public function lang(): string
    {
        return match ($this) {
            self::PERCENT => __("admin.percent"),
            self::FIXED => __("admin.fixed"),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::PERCENT => 'btn btn-success text-center btn-sm',
            self::FIXED => 'btn btn-danger text-center btn-sm',
        };
    }

    public static function vals(): array
    {
        return [
            self::PERCENT->value,
            self::FIXED->value,
        ];
    }
}
