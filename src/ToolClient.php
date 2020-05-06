<?php

/*
 * This file is part of the levelooy/zhetaoke.
 *
 * (c) levelooy <levelooy@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Levelooy\Zhetaoke;

use Illuminate\Support\Carbon;
use Levelooy\Zhetaoke\Kernel\BaseClient;
use Levelooy\Zhetaoke\Kernel\Exceptions\AlimamaErrorResponseException;
use Levelooy\Zhetaoke\Kernel\Exceptions\InvalidArgumentException;

class ToolClient extends BaseClient
{
    public function smartConvert($content, $pid)
    {
        if (is_numeric($content)) {
            return $this->convertGoodId($content, $pid, true);
        }

        $coupon = $this->parseGoodId($content);
        $goodsId = $coupon['item_id'];

        return $this->convertGoodId($goodsId, $pid, true, $coupon);
    }

    public function convertTpwd($tpwd, $pid, $aggregated = false)
    {
        if (empty($tpwd) || empty($pid)) {
            throw new InvalidArgumentException('参数不能为空（淘口令文案/pid）');
        }
        $baseUrl = 'http://api.zhetaoke.com:10000/api/open_gaoyongzhuanlian_tkl.ashx?'
            .'appkey=%s&sid=%s&pid=%s&tkl=%s&signurl=1';
        $url = sprintf($baseUrl, $this->appKey, $this->sid, $pid, $tpwd);

        $coupon = $this->parseGoodId($tpwd);

        $response = $this->convert($url, $coupon);

        return $aggregated ? $this->addExtraTo($response) : $response;
    }

    public function convertGoodId($goodId, $pid, $aggregated = false, $coupon = null)
    {
        if (empty($goodId) || empty($pid)) {
            throw new InvalidArgumentException('参数不能为空（商品ID/pid）');
        }

        $baseUrl = 'http://api.zhetaoke.com:10000/api/open_gaoyongzhuanlian.ashx?'
            .'appkey=%s&sid=%s&pid=%s&num_iid=%s&signurl=1';
        $url = sprintf($baseUrl, $this->appKey, $this->sid, $pid, $goodId);

        $response = $this->convert($url, $coupon);

        // 折淘客的其他优惠券接口，好像只能查出已经附加了这个优惠券的淘口令或者链接的 activity_id。所以，下面的代码无用

        return $aggregated ? $this->addExtraTo($response) : $response;
    }

    public function parseGoodId($content)
    {
        if (empty($content)) {
            throw new InvalidArgumentException('参数不能为空: 支持淘口令文案、长链接、二合一链接、短链接、喵口令、新浪短链，可直接返回特殊优惠券');
        }

        $baseUrl = 'http://api.zhetaoke.com:10000/api/open_shangpin_id.ashx?'
            .'appkey=%s&sid=%s&content=%s&type=1';
        $url = sprintf($baseUrl, $this->appKey, $this->sid, \urlencode($content));

        $formatResponse = $this->requestZhetaoke($url);

        return $formatResponse;
    }

    public function parseActivityId($content)
    {
        exit('此接口，官方已放弃维护！');

        if (empty($content)) {
            throw new InvalidArgumentException('参数不能为空: 支持淘口令文案或者二合一链接或者长链接或者短链接');
        }

        $baseUrl = 'http://api.zhetaoke.com:10000/api/open_activity_id.ashx?'
            .'appkey=%s&sid=%s&content=%s';
        $url = sprintf($baseUrl, $this->appKey, $this->sid, \urlencode($content));

        $formatResponse = $this->requestZhetaoke($url);

        return $formatResponse['activity_id'];
    }

    public function detail($goodId)
    {
        if (empty($goodId)) {
            throw new InvalidArgumentException('参数不能为空: 支持淘口令文案或者二合一链接或者长链接或者短链接');
        }

        $baseUrl = 'http://api.zhetaoke.com:10000/api/open_item_info.ashx?'
            .'appkey=%s&sid=%s&num_iids=%s';
        $url = sprintf($baseUrl, $this->appKey, $this->sid, $goodId);

        // 有点特别，虽然链接是折淘客的，但返回的格式是按官方返回
        $response = $this->requestOfficialUrl($url);

        return $response['tbk_item_info_get_response']['results']['n_tbk_item'][0];
    }

    public function createTpwd($title, $url, $logo = '')
    {
        if (empty($title) || empty($url)) {
            throw new InvalidArgumentException('参数不能为空(title/url)');
        }

        $tpwdUrl = $url;
        $baseUrl = 'http://api.zhetaoke.com:10000/api/open_tkl_create.ashx?'
            .'appkey=%s&sid=%s&text=%s&url=%s&logo=%s&signurl=1';
        $url = sprintf($baseUrl, $this->appKey, $this->sid, \urlencode($title), \urlencode($url), \urlencode($logo));

        $response = $this->requestZhetaoke($url);

        $response = $this->requestOfficialUrl($response['url']);

        $data = $response['tbk_tpwd_create_response']['data'];

        if (empty($data['model'])) {
            throw new AlimamaErrorResponseException('生成淘口令为空，请检查你的参数，url 必须以 https 开头，而且是淘宝的链接，当前 url 为 '.$tpwdUrl);
        }

        return $data['model'];
    }

    public function shortUrl($url, $target = 'sina')
    {
        $target = \strtolower($target);

        if (!\in_array($target, ['sina', 'baidu'])) {
            throw new InvalidArgumentException('暂时只支持新浪/百度短链接');
        }

        if (empty($url)) {
            throw new InvalidArgumentException('参数不能为空(url)');
        }

        $baseUrl = 'http://api.zhetaoke.com:10000/api/open_shorturl_'.$target.'_get.ashx?'
            .'appkey=%s&sid=%s&content=%s';
        $url = sprintf($baseUrl, $this->appKey, $this->sid, \urlencode($url));

        $response = $this->requestZhetaoke($url);

        return $response['shorturl'];
    }

    protected function orders($startAt, $queryType = 'create_time', $span = 1200)
    {
        if ($span > 1200 || $span < 60) {
            throw new InvalidArgumentException('时间间隔(span)超出范围，60-1200');
        }

        if (date('Y-m-d H:i:s', strtotime($startAt)) !== $startAt) {
            throw new InvalidArgumentException('时间格式不正确（Y-m-d H:i:s）');
        }

        $baseUrl = 'http://api.zhetaoke.com:10000/api/open_dingdanchaxun.ashx?'
            .'appkey=%s&sid=%s&order_query_type=%s&start_time=%s&span=%s'
            .'&signurl=1&page_no=1&page_size=100&fields='
            .'trade_parent_id,trade_id,tk_status,num_iid,item_title,item_num,price,pay_price,alipay_total_price,'
            .'income_rate,commission_rate,pub_share_pre_fee,commission,total_commission_rate,total_commission_fee,'
            .'subsidy_rate,subsidy_fee,subsidy_type,terminal_type,order_type,auction_category,click_time,'
            .'create_time,earning_time,seller_nick,seller_shop_title,site_id,site_name,adzone_id,adzone_name,'
            .'unid,tk3rd_type,tk3rd_pub_id,relation_id,special_id';
        $url = sprintf($baseUrl, $this->appKey, $this->sid, $queryType, $startAt, $span);

        $response = $this->requestZhetaoke($url);
        $response = $this->requestOfficialUrl($response['url']);

        return $response['tbk_sc_order_get_response']['results']['n_tbk_order'] ?? [];
    }

    public function ordersByCreateAt($startAt, $span = 1200)
    {
        return $this->orders($startAt, 'create_time', $span);
    }

    public function ordersByCompleteAt($startAt, $span = 1200)
    {
        return $this->orders($startAt, 'settle_time', $span);
    }

    protected function convert($url, $coupon = null)
    {
        $response = $this->requestZhetaoke($url);

        $response = $this->requestOfficialUrl($response['url']);

        $data = $response['tbk_privilege_get_response']['result']['data'];

        $coupon_amount = 0;

        if (isset($data['coupon_info'])) {
            preg_match_all('/(\d+)/', $data['coupon_info'], $matches);
            $coupon_amount = $matches[0][1];
        }

        if (isset($coupon['effectiveEndTime']) && Carbon::parse($coupon['effectiveEndTime'])->gt(Carbon::now()) && $coupon['amount'] > $coupon_amount) {
            $data['activity_id'] = $coupon['activity_id'];
            $data['coupon_click_url'] .= '&activityId='.$coupon['activity_id'];
        }

        return $data;
    }

    protected function addExtraTo($response)
    {
        $detail = $this->detail($response['item_id']);
        $response['detail'] = $detail;

        $response['tpwd_url'] = $tpwdUrl = (isset($response['activity_id']) || isset($response['coupon_info']))
            ? $response['coupon_click_url']
            : $response['item_url'];

        $title = (isset($response['activity_id']) ? '私-' : '').$detail['title'];
        $response['tpwd'] = $this->createTpwd($title, $tpwdUrl, $detail['pict_url']);

        $response['short_url_sina'] = $this->shortUrl($response['tpwd_url'], 'sina');
        $response['short_url_baidu'] = $this->shortUrl($response['tpwd_url'], 'baidu');

        return $response;
    }
}
