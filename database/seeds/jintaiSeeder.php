<?php

use Illuminate\Database\Seeder;
use App\Models\OperatingRecord;
use App\Models\EventRule;
use App\Models\Setting;
use App\Services\SettingService;


class jintaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $service = new SettingService;

        /*基础卡券配置************************************************************************************/

        //卡券基础
        $service->set('TICKET', [
            'logo_url' => 'https://mp.weixin.qq.com/misc/getheadimg?token=1634606114&fakeid=3090887998&r=449871',
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
            'logo' => '/logo/jintai.png',
            'title' => '金 泰 生 物',
            'sub_title' => '探究生命奥秘                造福人类健康',
            'color' => 'Color040',
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
                    'template_id' => 'Ff7Bz4uF8P11QiVwRcBh-tVOW6yCklceFwckgE_guwY',
                    'data_format' => [
                        'first' => '欢迎加入会员中心！',
                        'keyword1' => '[card.card_code]',
                        'keyword2' => '{date("Y-m-d H:i")}',
                        'remark' => '感谢您的光临。',
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
                    'template_id' => 'kMLqpQsyXv6lzuJfJd3vTdvyu-D9Uo-u9PJeE6pKApU',
                    'data_format' => [
                        'first' => '尊敬的会员，您刚进行了一笔消费！赠送您[order.bonus_present]积分',
                        'keyword1' =>'{date("Y-m-d H:i")}',
                        'keyword2' => '[order.money_pay_amount]元',
                        'keyword3' => '金泰生物',
                        'remark' => '感谢您的光临',
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
                    'template_id' => '_Vi2I7KdlMZ4IeO5fxOrZQQDVaj__3euv_ifVj4ju0Y',
                    'data_format' => [
                        'first' => '尊敬的会员,恭喜您充值成功！',
                        'keyword1' => '[order.balance_fee]元',
                        'keyword2' => '[order.balance_present]元，[order.bonus_present]积分',
                        'keyword3' => '金泰生物',
                        'keyword4' => '[card.balance]元',
                        'remark' => '如有疑问，敬请咨询',
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
                    'template_id' => 'CbXupOXMeiCM8jUdda5Lil96FCryDm1krLyowp9OefU',
                     'data_format' => [
                        'first' => '尊敬的会员,您刚完成一笔订单！',
                        'keyword1' => '[title]',
                        'keyword2' => '[order.money_pay_amount]元。',
                        'keyword3' => '[card.nickname]',
                        'keyword4' => '',
                        'keyword5' =>'',
                        'remark' => '支付方式：[paymentStr],感谢您的光临',
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
                    'template_id' => 'WNfNYaFZtQj_7N3Oq1nzuIoxCWKPXt2SR0XpKVJdlIA',
                    'data_format' => [
                        'first' => '尊敬的会员,您刚核销了优惠券！',
                        'keyword1' => '[ticket.ticket_code]',
                        'keyword2' => '[ticket.tickettemplate.title]',
                        'keyword3' =>'',
                        'remark' => '如有疑问，请咨询商家客服',
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
                    'template_id' => 'fluWd9dEQSbpuus0wTHdTqqit42ByB-6Vb5ZspNIdQQ',
                    'data_format' => [
                        'first' => '尊敬的会员,您刚体验了一项服务',
                        'keyword1' => '金泰生物',
                        'keyword2' => '[property.property_template.title]',
                        'keyword3' => '{date("Y-m-d H:i")}',
                        'remark' => '感谢您的惠顾',
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
                    'template_id' => 'bg9iMWmh9dbS1ToCa_TZeIaaf-JPhzDco81mihPGwoM',
                    'data_format' => [
                        'first' => '尊敬的会员，您使用[code]兑换码！',
                        'keyword1' =>'[message]',
                        'keyword2' =>'{date("Y-m-d H:i")}',
                        'remark' => '感谢您的光临',
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
                    'template_id' => '-yPw5qTAfgEae50udizbCVYYOVGzgO3Osn2Fz7IYUdk',
                    'data_format' => [
                        'first' => '尊敬的会员，您的资料变更成功',
                        'keyword1' => '{date("Y-m-d H:i")}',
                        'keyword2' => '金泰生物',
                        'keyword3' => '[content]',
                        'remark' => '如非本人操作，请联系商家客服,感谢您的光临',
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
                    'template_id' => 'h8KeFqj6Sq9EBx9SVytFwKQPnT2z3DuLus50v-P_E40',
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
                    'template_id' => 'cJq3nEfqsR-cF74de6lMogJLFj0I_RgMRBXIPpPCfb4',
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
                    'template_id' => '9yGZ6d-z0lzD35BQrPEExAPLuvtqJciybISb167cA08',
                    'data_format' => [
                        'first' => '尊敬的会员，您的账户因为[reason]发生了余额变化',
                        'keyword1' =>'{date("Y-m-d H:i")}',
                        'keyword2' => '[args]元',
                        'keyword3' => '',
                        'keyword4' => '[card.balance]',
                        'remark' => '感谢您的光临',
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
                    'template_id' => '_LfsmtinCu1oJ9i6UmdhCx4KvywHayIFYqPhAB3PSJI',
                    'data_format' => [
                        'first' => '尊敬的会员，赠送您[args]积分。',
                        'keyword1' => '[card.nickname]',
                        'keyword2' =>'{date("Y-m-d H:i")}',
                        'keyword3' =>'[args]',
                        'keyword4' =>'[card.bonus]',
                        'keyword5' =>'[reason]',
                        'remark' => '感谢您的光临',
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
                    'template_id' => 'bg9iMWmh9dbS1ToCa_TZeIaaf-JPhzDco81mihPGwoM',
                    'data_format' => [
                        'first' => '尊敬的会员，因为您达到[reason]条件,特赠送您大礼包一个。',
                        'keyword1' =>'[message]',
                        'keyword2' =>'{date("Y-m-d H:i")}',
                        'remark' => '感谢您的光临',
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
                    'template_id' => '16Z82dpEZH6-HMFXZX4PWxt-tWIamZ3PP46fW9zO0Jg',
                    'data_format' => [
                        'first' => '尊敬的会员，您已成功升级为[card.level]级会员。',
                        'keyword1' => '[detail]',
                        'keyword2' => '{date("Y-m-d H:i")}',
                        'remark' => '感谢您的光临',
                    ]
                ],
            ],



        ]);

      
	}
}
