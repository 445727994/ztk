<?php

/*
 * This file is part of the Yhcztk/zhetaoke.
 *
 * (c) Yhcztk <Yhcztk@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yhcztk\Zhetaoke;

use Yhcztk\Zhetaoke\Kernel\ServiceContainer;

class Application extends ServiceContainer
{
    protected $providers = [
        ServiceProvider::class,
    ];
}
