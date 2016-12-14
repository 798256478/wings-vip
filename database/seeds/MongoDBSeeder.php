<?php

use Illuminate\Database\Seeder;
use App\Models\OperatingRecord;
use App\Models\EventRule;
use App\Models\Setting;
use App\Services\SettingService;


class MongoDBSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $service = new SettingService;

        /*商城************************************************************************************************/

        //订单
        $service->set('ORDER', [
            'express_company' => ['圆通快递','申通快递'],
            'refund_reason' => ['商品运送途中已损毁','商品不符','订单有误','故障','其它'],
        ]);

        /*收银************************************************************************************************/

        //储值，赠送余额
        $service->set('BALANCE', [
            'buy'=>[
                500 => 0.1,
                1000 => 0.15,
                5000 => 0.2
             ],
             'exchange'=>[
                500 => 2,
                1000 => 5,
                5000 => 30
             ],

        ]);

        //支付方式&积分
        $service->set('PAYMENT', [
            'methods' => [
                ['name' => '微信线上', 'type' => 'WECHAT', 'disabled' => false],
                ['name' => '余额', 'type' => 'BALANCE', 'disabled' => false],
                ['name' => '现金', 'type' => 'CASHIER', 'disabled' => false],
                ['name' => '银行卡', 'type' => 'CASHIER', 'disabled' => false],
                ['name' => '支付宝', 'type' => 'CASHIER', 'disabled' => false],
                ['name' => '微信', 'type' => 'CASHIER', 'disabled' => false],
                ['name' => '积分抵扣', 'type' => 'BONUS', 'disabled' => false],
            ],
            'balance' => [
                'bonus' => 0,
                'methods' => [
                    '微信线上' => 1,
                    '现金' => 1,
                    '银行卡' => 1,
                    '支付宝' => 1,
                ]
            ],
            'consume' => [
                'bonus' => 1,
                'methods' => [
                    '现金' => 2,
                    '余额' => 1,
                    '银行卡' => 1,
                    '支付宝' => 0.5,
                    '积分抵扣'=>0,
                ]
            ],
            'commodities' => [
                'bonus' => 0,
                'methods' => [
                    '现金'=>1.2,
                    '余额'=>1.3,
                    '银行卡'=>1,
                    '支付宝'=>1,
                    '积分抵扣'=>0,
                ]
            ],
            'bonus_rule' => [
                'disabled'=>false,//是否禁用
                'exchange'=>1,//积分兑换现金比例
                'use'=>1,//使用比率
                'limit'=>500//现金使用上限
            ],
        ]);

        /*系统************************************************************************************************/
        $service->set('CASHIER_CLIENT', [
            'title' => '收银端',
            'auto_lock' => 300, //收银界面自动锁定时间（秒）
            'menus' => [
                ['page' => 'CONSUME', 'tag' => '消费', 'disable' => false],
                ['page' => 'WRITE_OFF', 'tag' => '核销', 'disable' => false],
                ['page' => 'BALANCE', 'tag' => '储值', 'disable' => false],
                ['page' => 'MALL', 'tag' => '商城', 'disable' => false],
                ['page' => 'INITIALIZE', 'tag' => '初始化', 'disable' => false],
            ],
        ]);

        /*主题************************************************************************************************/
        $service->set('THEME', [
            'key' => 'default',
            'colors' => [
                'THEME' => '#0092DB', //主题色，用于装饰性色块
                'VALUE' => '#0092DB', //数值颜色，
                'PRICE' => '',//价格，用于商品价格显示
                'BONUS' => '',//积分，用于商品积分显示
                'BUTTON1' => '',//按钮1，用于标准按钮
                'BUTTON2' => '',//按钮2，用于小按钮
            ],
            'texts' => [
                'TICKET' => '礼券', //优惠券
                'BONUS' => '积分', //积分
                'BLANACE' => '余额', //余额
                'MEMBER' => '会员', //会员
                'SLOGAN' => ''//宣传语，显示首页底部
            ]
        ]);


        /*事件规则*************************************************************************************/

        $eventRule = new EventRule;
        $eventRule->title = '新手礼包';
        $eventRule->event_class = 'App\Events\CardCreated';
        $eventRule->jobs = [
            ['class' => 'App\Jobs\GiveBonus','args' => 200, 'recipient' => 'SELF'],
        ];
        $eventRule->save();


        $eventRule = new EventRule;
        $eventRule->title = '一天';
        $eventRule->event_class = 'App\Events\SignEvent';
        $eventRule->conditions = [
            'days'=>['min'=>1,'max'=>2],
        ];
        $eventRule->jobs = [
            ['class' => 'App\Jobs\GiveBonus','args' =>5, 'recipient' => 'SELF'],
        ];
        $eventRule->save();

        $eventRule = new EventRule;
        $eventRule->title = '连续两天';
        $eventRule->event_class = 'App\Events\SignEvent';
        $eventRule->conditions = [
            'days'=>['min'=>2,'max'=>3],
        ];
        $eventRule->jobs = [
            ['class' => 'App\Jobs\GiveBonus','args' =>6, 'recipient' => 'SELF'],
        ];
        $eventRule->save();

        $eventRule = new EventRule;
        $eventRule->title = '连续三天及以上';
        $eventRule->event_class = 'App\Events\SignEvent';
        $eventRule->conditions = [
            'days'=>['min'=>3],
        ];
        $eventRule->jobs = [
            ['class' => 'App\Jobs\GiveBonus','args' =>7, 'recipient' => 'SELF'],
        ];
        $eventRule->save();


        $eventRule = new EventRule;
        $eventRule->title = '邀请回馈';
        $eventRule->event_class = 'App\Events\ReferrerBound';
        $eventRule->jobs = [
            ['class' => 'App\Jobs\GiveBonus','args' => 100, 'recipient' => 'SELF'],
            ['class' => 'App\Jobs\GiveBonus','args' => 100, 'recipient' => 'REFERRER'],
        ];
        $eventRule->save();

        $eventRule = new EventRule;
        $eventRule->title = '首次消费满200元';
        $eventRule->event_class = 'App\Events\OrderCompleted';
        $eventRule->conditions = [
            'FIRST'=>null, 'SINGLE_EXPENSE'=>['min'=>200,'max'=>null],
        ];
        $eventRule->jobs = [
            ['class' => 'App\Jobs\GiveBonus','args' => 100, 'recipient' => 'SELF'],
            ['class' => 'App\Jobs\GiveBalance','args' => 100, 'recipient' => 'SELF'],
        ];
        $eventRule->save();

        $eventRule = new EventRule;
        $eventRule->title = '消费满千回馈';
        $eventRule->event_class = 'App\Events\OrderCompleted';
        $eventRule->conditions = [
            'TOTAL_EXPENSE'=>['min'=>1000,'max'=>2000],
        ];
        $eventRule->jobs = [
            ['class' => 'App\Jobs\GiveBonus','args' => 100, 'recipient' => 'SELF'],
            ['class' => 'App\Jobs\GiveBalance','args' => 100, 'recipient' => 'SELF'],
        ];
        $eventRule->save();

        $eventRule = new EventRule;
        $eventRule->title = '消费满两千回馈';
        $eventRule->event_class = 'App\Events\OrderCompleted';
        $eventRule->conditions = [
            'TOTAL_EXPENSE'=>['min'=>2000,'max'=>null],
        ];
        $eventRule->jobs = [
            ['class' => 'App\Jobs\GiveBonus','args' => 100, 'recipient' => 'SELF'],
            ['class' => 'App\Jobs\GiveBalance','args' => 100, 'recipient' => 'SELF'],
            ['class' => 'App\Jobs\GiveTicket','args' => ['ticketTemplateId'=>1,'count'=>1], 'recipient' => 'SELF'],
            // ['class' => 'App\Jobs\GiveProperty','args' => ['propertyTemplateId'=>1,'count'=>2,'validity_days'=>30], 'recipient' => 'SELF'],
            ['class' => 'App\Jobs\LevelUp','args' =>2, 'recipient' => 'SELF'],
            ['class' => 'App\Jobs\PayCommission','args' =>20, 'recipient' => 'REFERRER'],
            ['class' => 'App\Jobs\LevelUp','args' =>2, 'recipient' => 'REFERRER'],
        ];
        $eventRule->save();
        $eventRule = new EventRule;
        $eventRule->title = '单次消费满500回馈';
        $eventRule->event_class = 'App\Events\OrderCompleted';
        $eventRule->conditions = [
            'SINGLE_EXPENSE'=>['min'=>500,'max'=>null],
        ];
        $eventRule->jobs = [
            ['class' => 'App\Jobs\GiveBonus','args' => 100, 'recipient' => 'SELF'],
        ];
        $eventRule->save();

        /*兑换规则*************************************************************************************/

        $redeemCode = new \App\Models\RedeemCode;
        $redeemCode->title = '开年礼包';
        $redeemCode->is_many = false;
        $redeemCode->begin_timestamp = time();
        $redeemCode->end_timestamp = time() + (7 * 24 * 60 * 60);
        $redeemCode->codes = [
            'ajsidid'
        ];
        $redeemCode->redeemed_quantity = 0;
        $redeemCode->stock_quantity = 100;
        $redeemCode->jobs = [
            ['class' => 'App\Jobs\GiveBonus','args' => 100, 'recipient' => 'SELF'],
        ];
        // $redeemCode->records = [
        //     ['card_id' => $card->id,'redeem_time' => time()],
        // ];
        $redeemCode->save();

        $redeemCode = new \App\Models\RedeemCode;
        $redeemCode->title = '年终红包';
        $redeemCode->is_many = true;
        $redeemCode->redeemed_quantity = 0;
        $redeemCode->stock_quantity = 3;
        $redeemCode->codes = [
            'fjsidid',
            'fjsidfd',
            'ajsidfd',
        ];
        $redeemCode->jobs = [
            ['class' => 'App\Jobs\GiveBonus','args' => 100, 'recipient' => 'SELF'],
            ['class' => 'App\Jobs\GiveBalance','args' => 100, 'recipient' => 'SELF'],
        ];
        // $redeemCode->records = [
        //     ['code' => 'fjisdll', 'card_id' => $card->id,'redeem_time' => time()],
        // ];
        $redeemCode->save();
	}
}
