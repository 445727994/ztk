<?php

/*
 * This file is part of the levelooy/zhetaoke.
 *
 * (c) levelooy <levelooy@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Levelooy\Zhetaoke;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class Facade extends LaravelFacade
{
    public static function getFacadeAccessor()
    {
        return 'zhetaoke';
    }

    public static function tool($name = '')
    {
        return $name ? app('zhetaoke.'.$name)->tool : app('zhetaoke')->tool;
    }

    public static function good($name = '')
    {
        return $name ? app('zhetaoke.'.$name)->good : app('zhetaoke')->good;
    }
}
