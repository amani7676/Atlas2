<?php

namespace LaravelEnso\Enums\Facades;

use Illuminate\Support\Facades\Facade;

class Enums extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'legacyEnums';
    }
}
