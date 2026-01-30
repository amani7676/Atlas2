<?php

namespace LaravelEnso\Enums\Traits;

use Illuminate\Support\Collection;
use LaravelEnso\Enums\Contracts\Mappable;
use ReflectionClass;

trait Select
{
    public static function random(): self
    {
        return self::cases()[array_rand(self::cases())];
    }

    public static function select(): array
    {
        $mappable = (new ReflectionClass(self::class))
            ->implementsInterface(Mappable::class);

        return Collection::wrap(self::cases())
            ->when(
                $mappable,
                fn ($cases) => $cases->map(fn ($case) => [
                    'id' => $case->value,
                    'name' => $case->map(),
                ]),
                fn ($cases) => $cases->map(fn ($case) => [
                    'id' => $case->value,
                    'name' => $case->name,
                ])
            )->values()->toArray();
    }
}
