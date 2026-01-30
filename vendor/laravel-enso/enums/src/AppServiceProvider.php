<?php

namespace LaravelEnso\Enums;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Enums\Services\LegacyEnums;

class AppServiceProvider extends ServiceProvider
{
    public $singletons = [
        'legacyEnums' => LegacyEnums::class,
    ];

    public function boot()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/enums.php', 'enso.enums');

        $this->publishes([
            __DIR__.'/../config' => config_path('enso'),
        ], ['enums-config', 'enso-config']);

        $this->publishes([
            __DIR__.'/../stubs/EnumServiceProvider.stub' => app_path(
                'Providers/EnumServiceProvider.php'
            ),
        ], 'enum-provider');
    }
}
