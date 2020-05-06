<?php

/*
 * This file is part of the levelooy/zhetaoke.
 *
 * (c) levelooy <levelooy@666.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

return [
    'defaults' => [
        'app_key' => env('ZHETAOKE_APPKEY', 'your-app-key'),
    ],
    /*
     * 多账户配置
     *
     * 可以是同一个 app_key 下的多个 sid，也可以是不同 app_key 下的多个 sid
     * app_key: 当 defaults 定义了 app_key 时，为可选配置
     * 如果只用到一个账户，app_key 和 sid 可以直接定义在 accounts 下。
     */
    'accounts' => [
        'sid' => env('ZHETAOKE_SID', 'your-sid'),
        // 'default' => [
        //     'sid' => env('ZHETAOKE_SID', 'your-sid'),
        // ],
        // 'account1' => [
        //     'sid' => env('ZHETAOKE_SID_1', 'your-sid'),
        // ],
        // 'account2' => [
        //     'app_key' => env('ZHETAOKE_APPID_2', 'your-app-id'),
        //     'sid' => env('ZHETAOKE_SID_2', 'your-sid'),
        // ],
    ],
];
