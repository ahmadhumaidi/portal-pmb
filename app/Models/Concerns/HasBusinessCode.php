<?php

namespace App\Models\Concerns;

trait HasBusinessCode
{
    protected static function nextBusinessCode(
        string $prefix,
        string $column,
        int $padding = 4,
        bool $withYear = false
    ): string {
        $nextId = ((int) static::max('id')) + 1;
        $number = str_pad((string) $nextId, $padding, '0', STR_PAD_LEFT);

        if ($withYear) {
            return $prefix . '-' . now()->year . '-' . $number;
        }

        return $prefix . '-' . $number;
    }
}
