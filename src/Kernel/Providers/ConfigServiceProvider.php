<?php

/*
 * This file is part of the Yhcztk/zhetaoke.
 *
 * (c) Yhcztk <Yhcztk@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yhcztk\Zhetaoke\Kernel\Providers;

use Yhcztk\Zhetaoke\Kernel\Config;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['config'] = function ($app) {
            return new Config($app->getConfig());
        };
    }
}
