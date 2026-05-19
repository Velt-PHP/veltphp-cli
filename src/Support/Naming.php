<?php

declare(strict_types=1);

namespace Velt\Cli\Support;

final class Naming
{
    public static function kebab(string $value): string
    {
        $value = preg_replace('/([a-z])([A-Z])/', '$1-$2', trim($value)) ?? $value;
        $value = preg_replace('/[^a-zA-Z0-9]+/', '-', $value) ?? $value;

        return strtolower(trim($value, '-'));
    }

    public static function pascal(string $value): string
    {
        $kebab = self::kebab($value);
        $words = array_filter(explode('-', $kebab));

        return implode('', array_map(static fn (string $word): string => ucfirst($word), $words));
    }

    public static function suffix(string $value, string $suffix): string
    {
        return str_ends_with($value, $suffix) ? $value : $value . $suffix;
    }
}
