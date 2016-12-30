<?php

return [

    'wings-vip' => [
        'wechat' => [
            'debug'  => true,
            'app_id' => 'wxcdacd82115bfe66e',
            'secret' => '873b1f9bf3321d02d65437c878b5d3d3',
            'token'  => 'moodynight',
            'aes_key' => 'MxuH9IBcaV6xfMVHNltelsaWKUqYavCUzMK5jFkxsDP',
            'log' => [
                'level' => 'debug',
                'file'  => 'D:\Sites\wings-vip\storage\logs\wechat.log', // XXX: 绝对路径！！！！
            ],
        ],
        'sms' => [
            'appkey'=>"23445943",
            'secretKey'=>"679997e522a7815f16b50d92938f5138",
            'signName'=>"金翼信息",
        ],
//        TODO 短信调试配置临时修改,与smsservice同时调整
//        'sms' => [
//            'appkey'=>"23274700",
//            'secretKey'=>"c4b29172e80a958f9917f5170f940bb8",
//            'signName'=>"店铺名",
//        ],
        'enable_modules' => [
            'shopping',
            'health',
            'yuda'
        ],
        'themes' => [
            'default',
            'cafe',
            'yuda'
        ],
        'sync' => [
            'key'=>'B78jVbW5AK4n0fMTWDAh0QpUFec5xPOi',//签名key
            'name'=>'yuda',//yuda的域名
            'adminTel'=>'18703862309',
            'newMember'=>'http://vip.h3pos.com/api/MemberEvent/NewMember',//yuda的会员信息接口
            'consume'=>'http://vip.h3pos.com/api/MemberEvent/MemberConsume',//yuda的消费接口
            'receiveTest'=>'http://vip.goldwings.cn/api/systemsync/memberevent',//自己内部测试的接口
            'mcrypt'=>[//密码加密
                'key'=>'B78jVbW5AK4n0fMTWDAh0QpUFec5xPOi',
                'iv'=>'00000000000000000000000000000000'
            ]
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
    'yuda' => [
        'wechat' => [
            'debug'  => true,
            'app_id' => 'wxcdacd82115bfe66e',
            'secret' => '873b1f9bf3321d02d65437c878b5d3d3',
            'token'  => 'moodynight',
            'aes_key' => 'MxuH9IBcaV6xfMVHNltelsaWKUqYavCUzMK5jFkxsDP',
            'log' => [
                'level' => 'debug',
                'file'  => 'D:\Sites\wings-vip\storage\logs\wechat.log', // XXX: 绝对路径！！！！
            ],
        ],
        'sms' => [
            'appkey'=>"23445943",
            'secretKey'=>"679997e522a7815f16b50d92938f5138",
            'signName'=>"金翼信息",
        ],
//        TODO 短信调试配置临时修改,与smsservice同时调整
//        'sms' => [
//            'appkey'=>"23274700",
//            'secretKey'=>"c4b29172e80a958f9917f5170f940bb8",
//            'signName'=>"店铺名",
//        ],
        'enable_modules' => [
            'shopping',
            'yuda'
        ],
        'themes' => [
            'yuda'
        ],
        'sync' => [
            'key'=>'B78jVbW5AK4n0fMTWDAh0QpUFec5xPOi',//签名key
            'name'=>'yuda',//yuda的域名
            'adminTel'=>'18703862309',
            'newMember'=>'http://vip.h3pos.com/api/MemberEvent/NewMember',//yuda的会员信息接口
            'consume'=>'http://vip.h3pos.com/api/MemberEvent/MemberConsume',//yuda的消费接口
            'receiveTest'=>'http://vip.goldwings.cn/api/systemsync/memberevent',//自己内部测试的接口
            'mcrypt'=>[//密码加密
                'key'=>'B78jVbW5AK4n0fMTWDAh0QpUFec5xPOi',
                'iv'=>'00000000000000000000000000000000'
            ]
        ]
    ],
];
