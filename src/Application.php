<?php

/*
 * This file is part of the levelooy/zhetaoke.
 *
 * (c) levelooy <levelooy@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Levelooy\Zhetaoke;

use Levelooy\Zhetaoke\Kernel\ServiceContainer;

class Application extends ServiceContainer
{
    protected $providers = [
        ServiceProvider::class,
    ];
}
