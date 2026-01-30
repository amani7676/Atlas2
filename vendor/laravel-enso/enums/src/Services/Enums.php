<?php

namespace LaravelEnso\Enums\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use LaravelEnso\Enums\Contracts\Mappable;
use ReflectionEnum;

class Enums
{
    public function handle(): array
    {
        return $this->enums()
            ->mapWithKeys(fn ($enum) => [
                $enum::registerBy() => $this->map($enum),
            ])->toArray();
    }

    private function enums(): Collection
    {
        return Collection::wrap(Config::get('enso.enums.vendors'))
            ->map(fn ($vendor) => base_path("vendor/{$vendor}"))
            ->map(fn ($vendor) => File::directories($vendor))
            ->flatten()
            ->push(base_path())
            ->mapInto(Source::class)
            ->map->get()
            ->filter->isNotEmpty()
            ->collapse();
    }

    private function map(string $enum): Collection
    {
        return Collection::wrap($enum::cases())
            ->when($this->mappable($enum), fn ($cases) => $cases
                ->map(fn ($case) => [
                    'name' => $case->map(),
                    'value' => $case->value,
                ]))
            ->pluck('name', 'value');
    }

    private function mappable(string $enum): bool
    {
        $reflection = new ReflectionEnum($enum);

        return $reflection->implementsInterface(Mappable::class);
    }
}
