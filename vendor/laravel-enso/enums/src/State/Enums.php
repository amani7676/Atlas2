<?php

namespace LaravelEnso\Enums\State;

use LaravelEnso\Core\Contracts\ProvidesState;
use LaravelEnso\Enums\Facades\Enums as LegacyFacade;
use LaravelEnso\Enums\Services\Enums as Service;

class Enums implements ProvidesState
{
    public function mutation(): string
    {
        return 'setEnums';
    }

    public function state(): mixed
    {
        return array_merge((new Service())->handle(), LegacyFacade::all());
    }
}
