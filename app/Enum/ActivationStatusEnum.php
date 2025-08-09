<?php

namespace App\Enum;

enum ActivationStatusEnum: string
{
    case ACTIVE  = 'active';
    case DEACTIVE  = 'deactive';

    public function lang(): string
    {
        return match ($this) {
            self::ACTIVE => __("admin.active"),
            self::DEACTIVE => __("admin.deactive"),
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::ACTIVE => 'btn btn-success text-center',
            self::DEACTIVE => 'btn btn-danger text-center',
        };
    }

    public static function vals(): array
    {
        return [
            self::ACTIVE->value,
            self::DEACTIVE->value,
        ];
    }
}
