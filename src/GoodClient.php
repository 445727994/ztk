<?php

/*
 * This file is part of the levelooy/zhetaoke.
 *
 * (c) levelooy <levelooy@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Levelooy\Zhetaoke;

use Levelooy\Zhetaoke\Kernel\BaseClient;
use Levelooy\Zhetaoke\Kernel\Exceptions\GoodRequestErrorException;
use Levelooy\Zhetaoke\Kernel\Exceptions\InvalidArgumentException;

class GoodClient extends BaseClient
{
    protected $params = ['page_size' => 20];
    protected $apiMethod = 'api_all';

    public function list($page = 1)
    {
        if ($page < 1) {
            throw new InvalidArgumentException('页数超出范围，>0');
        }

        $baseUrl = 'http://api.zhetaoke.com:10000/api/'.$this->apiMethod.'.ashx?'
            .'appkey=%s&page=%s&';
        $url = sprintf($baseUrl, $this->appKey, $page).$this->params();

        $response = $this->requestZhetaoke($url);

        if (200 !== $response['status']) {
            throw new GoodRequestErrorException('查询商品库出错('.$response['status'].')');
        }

        return $response['content'];
    }

    public function top($name)
    {
        switch ($name) {
            case '2hours':
                $this->apiMethod = 'api_xiaoshi';
                break;
            case '1day':
                $this->apiMethod = 'api_quantian';
                break;
            case 'now':
                $this->apiMethod = 'api_shishi';
                break;
            case 'ddq':
                $this->apiMethod = 'api_dongdong';
                break;
            default:
                throw new InvalidArgumentException('无效参数: 支持[\'2hours\', \'1day\', \'now\', \'ddq\']');
                break;
        }

        return $this;
    }

    public function item($goodId)
    {
        if (!$goodId) {
            throw new InvalidArgumentException('商品 ID 必须');
        }

        $baseUrl = 'http://api.zhetaoke.com:10000/api/api_detail.ashx?'
            .'appkey=%s&tao_id=%s';
        $url = sprintf($baseUrl, $this->appKey, $goodId);

        $response = $this->requestZhetaoke($url);

        if (200 !== $response['status']) {
            throw new GoodRequestErrorException('查询商品库出错('.$response['status'].')');
        }

        return $response['content'];
    }

    protected function params()
    {
        $params = array_filter($this->params);

        $params = array_map(function ($key, $value) {
            return $key.'='.$value;
        }, array_keys($params), array_values($params));

        $this->refreshParams();

        return implode($params, '&');
    }

    protected function refreshParams()
    {
        $this->params = [
            'page_size' => 20,
        ];
        $this->apiMethod = 'api_all';

        return $this;
    }

    public function category($cid = null)
    {
        if (!\in_array($cid, range(0, 14)) && !empty($cid)) {
            throw new InvalidArgumentException('分类参数：1-14');
        }

        if ($cid > 0) {
            $this->params['cid'] = $cid;
        } else {
            $this->params['cid'] = '';
        }

        return $this;
    }

    public function sort($sort = null)
    {
        if (!\in_array($sort, [
            'new', 'sale_num', 'commission_rate_asc', 'commission_rate_desc', 'price_asc', 'price_desc',
        ]) && !empty($cid)) {
            throw new InvalidArgumentException('排序参数无效');
        }

        if ($sort) {
            $this->params['sort'] = $sort;
        } else {
            $this->params['sort'] = '';
        }

        return $this;
    }

    public function pageSize($pageSize = 20)
    {
        if ($pageSize > 50 || $pageSize < 1) {
            throw new InvalidArgumentException('每页条数超出范围，1-50');
        }

        if ($pageSize) {
            $this->params['page_size'] = $pageSize;
        } else {
            $this->params['page_size'] = 20;
        }

        return $this;
    }

    public function keyword($keyword = null)
    {
        if ($keyword) {
            $this->params['q'] = urlencode($keyword);
        } else {
            $this->params['q'] = '';
        }

        return $this;
    }

    public function tmall($boll = true)
    {
        if ((bool) $boll) {
            $this->params['tj'] = 'tmall';
        } else {
            $this->params['tj'] = '';
        }

        return $this;
    }

    public function goldSeller($boll = true)
    {
        if ((bool) $boll) {
            $this->params['tj'] = 'gold_seller';
        } else {
            $this->params['tj'] = '';
        }

        return $this;
    }

    public function taoQiangGou($boll = true)
    {
        if ((bool) $boll) {
            $this->params['jt'] = 'taoqianggou';
        } else {
            $this->params['jt'] = '';
        }

        return $this;
    }

    public function juHuaSuan($boll = true)
    {
        if ((bool) $boll) {
            $this->params['jt'] = 'juhuasuan';
        } else {
            $this->params['jt'] = '';
        }

        return $this;
    }

    public function haiTao($boll = true)
    {
        if ((bool) $boll) {
            $this->params['jh'] = 'haitao';
        } else {
            $this->params['jh'] = '';
        }

        return $this;
    }

    public function jiYouJia($boll = true)
    {
        if ((bool) $boll) {
            $this->params['jh'] = 'jiyoujia';
        } else {
            $this->params['jh'] = '';
        }

        return $this;
    }

    public function today($boll = true)
    {
        if ((bool) $boll) {
            $this->params['today'] = '1';
        } else {
            $this->params['today'] = '';
        }

        return $this;
    }

    public function brand($boll = true)
    {
        if ((bool) $boll) {
            $this->params['pinpai'] = '1';
        } else {
            $this->params['pinpai'] = '';
        }

        return $this;
    }

    public function price($min = 0, $max = 0)
    {
        if ($min > $max) {
            list($min, $max) = [$max, $min];
        }

        if (0 == $min && 0 == $max) {
            $this->params['price'] = '';
        } else {
            $this->params['price'] = $min.'-'.$max;
        }

        return $this;
    }

    public function commission($commission = 0)
    {
        if (0 == $commission) {
            $this->params['commission_rate_start'] = '';
        } else {
            $this->params['commission_rate_start'] = $commission;
        }

        return $this;
    }

    public function volume($volume = 0)
    {
        if (0 == $volume) {
            $this->params['sale_num_start'] = '';
        } else {
            $this->params['sale_num_start'] = $volume;
        }

        return $this;
    }

    public function score($score = 0)
    {
        if (0 == $score) {
            $this->params['dsr_start'] = '';
        } else {
            $this->params['dsr_start'] = $score;
        }

        return $this;
    }

    public function couponAmount($amount = 0)
    {
        if (0 == $amount) {
            $this->params['coupon_amount_start'] = '';
        } else {
            $this->params['coupon_amount_start'] = $amount;
        }

        return $this;
    }
}
