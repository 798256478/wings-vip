<?php

return [

    'wings-vip' => [
        'wechat' => [
            'debug'  => true,
            'app_id' => 'wxcdacd82115bfe66e',
            'secret' => '873b1f9bf3321d02d65437c878b5d3d3',
            'token'  => 'moodynight',
        ],
        'sms' => [
            'appkey'=>"23445943",
            'secretKey'=>"679997e522a7815f16b50d92938f5138",
            'signName'=>"金翼信息",
        ],
        'enable_modules' => [
            'shopping',
            'health'
        ],
        'themes' => [
            'default',
            'cafe'
        ]
    ],

    'jintai' => [
        'wechat' => [
            'debug'  => true,
            'app_id' => 'wx5fe8c2c897802221',
            'secret' => '56ea916c3707a707b23a4b589cd7a113',
            'token'  => 'wechat-vip',
            'payment' => [
                'merchant_id'        => '1277456201',
                'key'                => 'GAUuvVXoPS3ORBNr20e9i1miqVRc9cyl',
                'cert_path'          => 'C:\wamp\bin\php\php5.5.12\cacert.pem', // XXX: 绝对路径！！！！
                'key_path'           => 'C:\wamp\bin\php\php5.5.12\cacert.pem',      // XXX: 绝对路径！！！！
                'notify_url'         => 'http://jintai.goldwings.cn/WXOrderNoticeListen',
                // 'device_info'     => '013467007045764',
                // 'sub_app_id'      => '',
                // 'sub_merchant_id' => '',
                // ...
            ],
        ],
        'sms' => [
            'appkey'=>"23274700",
            'secretKey'=>"c4b29172e80a958f9917f5170f940bb8",
            'signName'=>"店铺名",
        ],
        'enable_modules' => [
            'shopping',
            'health'
        ],
        'themes' => [
            'default',
        ]
    ],
];
