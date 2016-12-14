<?php

use Illuminate\Database\Seeder;
use App\Models\OperatingRecord;
use App\Models\EventRule;
use App\Models\Setting;
use App\Services\SettingService;
use App\Models\User;


class defaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        登录
        // User::create(['login_name' => '000000', 'display_name' => 'admin', 'password' => '111111', 'roles' => 'admin' ]);
        // User::create(['login_name' => '130822', 'display_name' => 'cashier', 'password' => '111111', 'roles' => 'cashier' ]);


        $service = new SettingService;

        /*基础卡券配置************************************************************************************/

        //卡券基础
        $service->set('TICKET', [
            'logo_url' => 'http://mmbiz.qpic.cn/mmbiz/nMB4wnaA8aba6Y2jMm0l0XhpLVL20dxmHZA1Hm5pxGTnYnWus0lTxpRT4eWuvoZiaDYRhxcNibkQrkUmbBibTehOA/0',
            'brand_name' => 'Cafe Alayna',
            'service_phone' => '4007170808',
            'custom_url_name' => '会员中心',
            'custom_url_sub_title' => '精彩不断',
            'sub_title' => "无限制",
            'notice' => "消费时请出示该优惠券",
            'description' => "请与会员卡配合使用"
        ]);

        //会员卡
        $service->set('CARD', [
            //微信标准
            'card_id' => 'pHVpWuD0HlSwrn8yry_4AcRVO4Fs',
            'logo' => '/logo/jinyi.png',
            'title' => 'CAFE ALAYNA',
            'sub_title' => 'Change the world, one cup at a time',
            'color' => 'Color102',
            'notice' => '请向店员出示会员卡',
            'description' => '详见会员中心相关说明',
            'bonus_cleared' => '积分不清零',
            'bonus_rules' => '每消费1元获取1点积分',
            'prerogative' => '持会员卡消费，可获1：1积分，会员储值更有满额赠送。',
            'levels' => [
                ['id' => 1,'order' => 1,'name' => "银牌",'description' => '....'],
                ['id' => 2,'order' => 2,'name' => "金牌",'description' => '....'],
                ['id' => 3,'order' => 4,'name' => "白金",'description' => '....'],
                ['id' => 4,'order' => 3,'name' => "年费",'description' => '....'],
            ],

        ]);

      



        /*模版消息配置*************************************************************************************/

        $service->set('TEMPLATE_MESSAGES', [
            'CARD_CREATE' => [
                'label' => '新卡创建',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => 'kuoKt-79eSlAlj56ML7idN-kqryvMr7QHlYG5CVSMIM',
                    'data_format' => [
                        'first' => '欢迎加入阿琳娜会员！',
                        'keyword1' => '[card.card_code]',
                        'keyword2' => '[card.nickname]',
                        'keyword3' => '',
                        'keyword4' => '{date("Y-m-d H:i")}',
                        'remark' => '咖啡啊琳娜,感谢您的光临。',
                    ]
                ],
            ],

            'CONSUME' => [
                'label' => '消费提醒',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => 'NKOVpQr5x1teIUNl1CrnPVGzg9ntB7VdaJnMLDXPmUc',
                    'data_format' => [
                        'first' => '尊敬的会员，您刚进行了一笔消费！赠送您[order.bonus_present]积分',
                        'keyword1' => '[order.money_pay_amount]元',
                        'keyword2' => 0,
                        'keyword3' =>'{date("Y-m-d H:i")}',
                        'keyword5' => '',
                        'remark' => '咖啡啊琳娜,感谢您的光临',
                    ]
                ],
            ],
            'BALANCE' => [
                'label' => '储值提醒',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => 'S5ShtXOFA8qltK1uGg1pcYVi9ddIGhkqGaPi4KU5rj8',
                    'data_format' => [
                        'first' => '尊敬的会员,您刚进行了一笔储值！赠送您[order.bonus_present]积分',
                        'keyword1' => '[card.card_code]',
                        'keyword2' => '[order.balance_fee]元',
                        'keyword3' => '[order.balance_present]元',
                        'keyword4' => '[card.balance]元',
                        'remark' => '咖啡啊琳娜,感谢您的光临',
                    ]
                ],
            ],

            'GOODS' => [
                'label' => '新订单提醒',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => 'wxv5v9mddRg7joz4Gxi3QaYRHw8a6FRqxEl8josChBU',
                     'data_format' => [
                        'first' => '尊敬的会员,您刚完成一笔订单！订单号[order.number]',
                        'keyword1' => '[title]',
                        'keyword2' => '[order.money_pay_amount]元。',
                        'keyword3' => '[card.nickname]',
                        'keyword4' => '[paymentStr]',
                        'keyword5' =>'',
                        'remark' => '咖啡啊琳娜,感谢您的光临',
                    ]
                ],
            ],

            'TICKET_VERIFIED' => [
                'label' => '核销通知',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => 'YUVJWIgabvKJ-fF1OIaoqNV4n1R7KmHQk0t0RmmXrPo',
                    'data_format' => [
                        'first' => '尊敬的会员,您刚使用一张[ticket.tickettemplate.title]优惠券！',
                        'keyword1' => '[ticket.ticket_code]',
                        'keyword2' => '0',
                        'keyword3' =>'{date("Y-m-d H:i")}',
                        'remark' => '咖啡啊琳娜,感谢您的光临',
                    ]
                ],
            ],

            'USE_PROPERTY' => [
                'label' => '服务体验',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => 'H83maq9zrU6zq0aYSAlvWqY8zID-fuC_fSpH3KZixzY',
                    'data_format' => [
                        'first' => '尊敬的会员,您刚体验了一项服务',
                        'keyword1' => '[property.property_template.title]',
                        'keyword2' => '已完成',
                        'remark' => '咖啡啊琳娜,感谢您的光临',
                    ]
                ],
            ],

            'CODE_REDEEMED' => [
                'label' => '兑换码',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => 'oB_X8GcyXplD_rgDmXVJAb7h2QTazOOq_6YrxbhutCw',
                    'data_format' => [
                        'first' => '尊敬的会员，您刚使用[code]兑换码兑换了[message]！',
                        'keyword1' =>'{date("Y-m-d H:i")}',
                        'keyword2' =>'',
                        'remark' => '咖啡啊琳娜,感谢您的光临',
                    ]
                ],
            ],
            'PROFILE_FILLED'=>[
                'label' => '会员卡资料变更通知',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => '0aPS3znYdbjJ396p3ubFSV4N1HLaGg7dXl8SRmlDUfs',
                    'data_format' => [
                        'first' => '尊敬的会员，您的资料变更成功',
                        'keyword1' =>'[card.card_code]',
                        'keyword2' => '[card.nickname]',
                        'keyword3' => '[content]',
                        'keyword4' => '{date("Y-m-d H:i")}',
                        'remark' => '咖啡啊琳娜,感谢您的光临',
                    ]
                ],
            ],
            'REFERRER_BOUND'=>[//待完成
                'label' => '推荐人绑定',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[experiment.name]',
                        'balance' => '[experiment.progress]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => '',
                    'data_format' => [
                        'first' => '您好，【[card.nickname]】已被您推荐为会员',
                        'keyword1' =>'[card.nickname]',
                        'keyword2' => '{date("Y-m-d H:i")}',
                        'remark' => '推荐关注奖励已打入您的会员账户，感谢您的推荐',
                    ]
                ],
            ],
            
            'PROGRESS_CHANGED'=>[//待完成
                'label' => '项目进度提醒',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                       'shop_name' => '[name]',
                       'balance' => '[progress]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => '',
                    'data_format' => [
                        'first' => '尊敬的会员，您的检测状态发生了变化',
                        'keyword1' =>'[name]',
                        'keyword2' => '[progress]',
                        'keyword3' => '【[progress]】完成',
                        'keyword4' =>'{date("Y-m-d H:i")}',
                        'remark' => '请注意跟进，谢谢',
                    ]
                ],
            ],
            
            
            'GiveBalance'=>[
                'label' => '账户余额变更通知',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => '7_lFWfdzY6_4nUBV7wPu31Vpn5ryV9xbApW2EAa9v1s',
                    'data_format' => [
                        'first' => '尊敬的会员，您的账户发生了余额变化',
                        'keyword1' =>'{date("Y-m-d H:i")}',
                        'keyword2' => '[reason]',
                        'keyword3' => '[args]元',
                        'remark' => '咖啡啊琳娜,感谢您的光临',
                    ]
                ],
            ],
            'GIVE_BONUS'=>[
                'label' => '积分变更通知',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => 'jsXxi4y_AKvS0gyw90jtBCrtt06OmkXnO1tKPIcYgnw',
                    'data_format' => [
                        'first' => '尊敬的会员，赠送您[args]积分。',
                        'keyword1' => '[card.nickname]',
                        'keyword2' => '[card.card_code]',
                        'keyword3' =>'[reason]',
                        'keyword4' => '[card.bonus]',
                        'remark' => '咖啡啊琳娜,感谢您的光临',
                    ]
                ],
            ],
            'GIVE_GIFT'=>[
                'label' => '礼品赠送成功提醒',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => 'V5tOa5yXBC3lkhO0BvtsMIf_CcbQlCvR95F0U-uI0J4',
                    'data_format' => [
                        'first' => '尊敬的会员，因为您达到[reason]条件,特赠送您大礼包一个。',
                        'keyword1' => '[card.nickname]',
                        'keyword2' => '[message]',
                        'keyword3' =>'',
                        'keyword4' => '',
                        'remark' => '咖啡啊琳娜,感谢您的光临',
                    ]
                ],
            ],
            'LEVEL_UP'=>[
                'label' => '等级提升通知',
                'sms' => [
                    'active' => true,
                    'sms_template_code' => 'SMS_2940044',
                    'sms_param' => [
                        'shop_name' => '[sms.shopname]',
                        'balance' => '[balance]',
                    ]
                ],
                'wechat' => [
                    'active' => true,
                    'template_id' => 'Q4Wn1Fb6_LjAXVwt397_nE1QwOAZw8UkBSqx3MnhmpQ',
                    'data_format' => [
                        'first' => '尊敬的会员，您已成功升级为[card.level]级会员。',
                        'keyword1' => '{date("Y-m-d H:i")}',
                        'keyword2' => '[detail]',
                        'remark' => '咖啡啊琳娜,感谢您的光临',
                    ]
                ],
            ],



        ]);
	}
}
