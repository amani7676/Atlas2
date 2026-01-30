<?php

namespace LaravelEnso\Enums\Traits;

trait Random
{
    public static function random(): self
    {
        return self::cases()[array_rand(self::cases())];
    }

    public static function select(): array
    {
        return self::cases()
            ->map(fn ($value, $key) => (object) ['id' => $key, 'name' => $value])
            ->values();
    }
}
