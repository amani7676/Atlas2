<?php

namespace LaravelEnso\Enums\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class LegacyEnums
{
    private $enums;

    public function __construct()
    {
        $this->enums = new Collection();
    }

    public function register($enums)
    {
        Collection::wrap($enums)
            ->each(fn ($enum, $key) => $this->enums->put($key, $enum));
    }

    public function remove($aliases)
    {
        Collection::wrap($aliases)
            ->each(fn ($alias) => $this->enums->forget($alias));
    }

    public function all(): array
    {
        return $this->enums
            ->map(fn ($enum) => is_array($enum) ? $enum : $this->map($enum))
            ->toArray();
    }

    private function map(string $enum): array
    {
        $enum::localisation(false);
        $all = App::make($enum)::all();
        $enum::localisation(true);

        return $all;
    }

    private function label(string $value): string
    {
        $string = Str::of($value);

        return $string->exactly($string->upper())
            ? $string->title()
            : $string->snake(' ')->title();
    }
}
