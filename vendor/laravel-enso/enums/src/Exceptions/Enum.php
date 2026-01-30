<?php

namespace LaravelEnso\Enums\Exceptions;

use InvalidArgumentException;

class Enum extends InvalidArgumentException
{
    public static function keyNotFound()
    {
        return new static(__('Key not found'));
    }
}
