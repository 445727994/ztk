<?php

/*
 * This file is part of the levelooy/zhetaoke.
 *
 * (c) levelooy <levelooy@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Levelooy\Zhetaoke;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class ServiceProviderForLaravel extends LaravelServiceProvider
{
    public function boot()
    {
    }

    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/config.php');
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('zhetaoke.php')], 'zhetaoke');
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('zhetaoke');
        }
        $this->mergeConfigFrom($source, 'zhetaoke');
    }

    public function register()
    {
        $this->setupConfig();

        if (!empty(config('zhetaoke.accounts.sid'))) {
            $accounts = [
                'default' => config('zhetaoke.accounts'),
            ];
            config(['zhetaoke.accounts.default' => $accounts['default']]);
        } else {
            $accounts = config('zhetaoke.accounts');
        }

        foreach ($accounts as $account => $config) {
            if (!isset($config['app_id'])) {
                $config['app_id'] = config('zhetaoke.defaults.app_id');
            }
            $this->app->singleton("zhetaoke.{$account}", function ($laravelApp) use ($config) {
                $app = new Application(array_merge(config('zhetaoke.defaults', []), $config));

                $app['request'] = $laravelApp['request'];

                return $app;
            });
        }

        $this->app->alias('zhetaoke.default', 'zhetaoke');
        $this->app->alias('zhetaoke', Application::class);
    }
}
