<?php

/*
 * This file is part of the levelooy/zhetaoke.
 *
 * (c) levelooy <levelooy@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Levelooy\Zhetaoke\Kernel;

use Levelooy\Zhetaoke\Kernel\Providers\ConfigServiceProvider;
use Pimple\Container;

class ServiceContainer extends Container
{
    protected $id;
    protected $defaultConfig = [];
    protected $userConfig = [];
    protected $providers = [];

    public function __construct(array $config = [], array $prepends = [], string $id = null)
    {
        $this->registerProviders($this->getProviders());

        parent::__construct($prepends);

        $this->id = $id;

        $this->userConfig = $config;
    }

    public function getId()
    {
        return $this->id ?? $this->id = md5(json_encode($this->userConfig));
    }

    public function getConfig()
    {
        $base = [
            'http' => [
                'timeout' => 30.0,
                'base_uri' => 'http://api.zhetaoke.com:10000/api/',
            ],
        ];

        return array_replace_recursive($base, $this->defaultConfig, $this->userConfig);
    }

    public function getProviders()
    {
        return array_merge([
            ConfigServiceProvider::class,
        ], $this->providers);
    }

    public function rebind($id, $value)
    {
        $this->offsetUnset($id);
        $this->offsetSet($id, $value);
    }

    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    public function __set($id, $value)
    {
        return $this->offsetSet($id, $value);
    }

    public function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            parent::register(new $provider());
        }
    }
}
