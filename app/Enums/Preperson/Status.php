<?php

declare(strict_types=1);

namespace App\Enums\Preperson;

use App\Traits\EnumUtils;

enum Status: string
{
    use EnumUtils;

    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('forms.status.active'),
            self::INACTIVE => __('forms.status.non_active')
        };
    }
}
