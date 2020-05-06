<?php

/*
 * This file is part of the Yhcztk/zhetaoke.
 *
 * (c) Yhcztk <Yhcztk@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Yhcztk\Zhetaoke;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['tool'] = function ($app) {
            return new ToolClient($app);
        };

        $app['good'] = function ($app) {
            return new GoodClient($app);
        };
    }
}
