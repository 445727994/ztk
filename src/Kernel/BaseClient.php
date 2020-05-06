<?php

/*
 * This file is part of the levelooy/zhetaoke.
 *
 * (c) levelooy <levelooy@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Levelooy\Zhetaoke\Kernel;

use GuzzleHttp\Client;
use Levelooy\Zhetaoke\Kernel\Exceptions\AlimamaErrorResponseException;
use Levelooy\Zhetaoke\Kernel\Exceptions\Exception;
use Levelooy\Zhetaoke\Kernel\Exceptions\HttpException;
use Levelooy\Zhetaoke\Kernel\Exceptions\ZhetaokeErrorResponseException;

class BaseClient
{
    protected $app;
    protected $appKey;
    protected $sid;
    protected $guzzleOptions = [];

    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
        $this->appKey = $app['config']->get('app_key');
        $this->sid = $app['config']->get('sid');
    }

    protected function requestOfficialUrl($url)
    {
        $response = $this->request($url);
        $formatResponse = \json_decode($response, true);

        if (isset($formatResponse['error_response'])) {
            $subMsg = $formatResponse['error_response']['sub_msg'] ?? '';
            throw new AlimamaErrorResponseException($formatResponse['error_response']['msg']
                .($subMsg ? ': '.$subMsg : ''));
        }

        return $formatResponse;
    }

    protected function requestZhetaoke($url)
    {
        $response = $this->request($url);
        $formatResponse = \json_decode($response, true);

        if (empty($formatResponse)) {
            throw new ZhetaokeErrorResponseException($response);
        }

        return $formatResponse;
    }

    protected function request($url)
    {
        try {
            return $this->getHttpClient()->get($url)->getBody()->getContents();
        } catch (Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }
}
