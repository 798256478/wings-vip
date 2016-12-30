<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CardService;
use App\Models\OperatingRecord;
use App\Services\SettingService;
use App\Models\Health\ProjectSite;
use App\Models\Notice;
use App\Services\NoticeService;
use App\Services\ThemeService;
use App\Models\GoodSpecification;


use Session;
use Request;
use App;
use DB;

class CommandController extends Controller
{

	public function index()
	{

	}

    public function test(SettingService $service)
	{

        $service->set('THEME', [
            'key' => 'default',
            'colors' => [
                'THEME' => '#0092DB', //主题色，用于装饰性色块
                'HIGHLIGHT' => '#0092DB', //数值颜色，
                'PRICE' => '#FF9933',//价格，用于商品价格显示
                'BONUS' => '#339900',//积分，用于商品积分显示
                'BUTTON1' => '#0092DB',//按钮1，用于标准按钮
                'BUTTON2' => '#0092DB',//按钮2，用于小按钮
            ],
            'texts' => [
                'TICKET' => '礼券', //优惠券
                'BONUS' => '积分', //积分
                'BLANACE' => '余额', //余额
                'MEMBER' => '会员', //会员
                'SLOGAN' => ''//宣传语，显示首页底部
            ]
        ]);
        // $themeService = new ThemeService;
        // $str = $themeService->loadWechatCss('order');
        // var_dump($str);
        // $str = $themeService->getViewPath('shop.showcase');
        // var_dump($str);

        // $result = GoodSpecification::find(3);
        // var_dump($result->suit);
        // var_dump($result->goodSuit);
        //var_dump($result);
        
        // $redeemCode = new \App\Models\RedeemCode;
        // $redeemCode->title = '测试大红包6';
        // $redeemCode->is_many = true;
        // $redeemCode->redeemed_quantity = 50;
        
        // $redeemCode->jobs = [
        //     ['class' => 'App\Jobs\GiveBonus','args' => 100, 'recipient' => 'SELF'],
        //     ['class' => 'App\Jobs\GiveBalance','args' => 100, 'recipient' => 'SELF'],
        // ];
        // $redeemCode->records = [
        //     ['code' => 'fjisdll', 'card_id' => '404','redeem_time' => time()],
        // ];
        
        // $codes = [
        //     'asdasd11',
        //     'asdasd12',
        //     'asdasd13',
        //     'asdasd14',
        //     'asdasd15',
        //     'asdasd16',
        //     'asdasd17',
        //     'asdasd18',
        //     'asdasd19',
        //     'asdasd20',
        // ];
        
        // unset($codes[0]);
        // $codes = array_values($codes);
        
        // $redeemCode->codes = $codes;
        
        // $redeemCode->save();
        // $mall = new \App\Models\Mall;
        // $mall=[
        //     [
        //      'name' => '头部广告位',
        //      'type' => 'carousel',
        //      'disabled' => false,
        //      'is_show_name' => false,
        //      'is_show_logo' => true,
        //      'items' => ['http://aaa/bbb/ccc.png','http://aaa/bbb/ccc.png'],
        //      'order'=> 1,
        //     ],
        //     [
        //         'name' => '分类',
        //         'type' => 'shop',
        //         'disabled' => false,
        //         'is_show_name' => false,
        //         'order'=> 2,
        //     ],
        //     [
        //         'name' => '主推商品',
        //         'type' => 'goods',
        //         'disabled' => false,
        //         'is_show_name' => false,
        //         'items' => [2,7,1,8],
        //         'order'=> 3,
        //     ],
        //     [
        //         'name' => '联系方式',
        //         'type' => 'img',
        //         'disabled' => false,
        //         'is_show_name' => false,
        //         'url' =>'http://aaa/bbb/ccc.png',
        //         'order'=> 4,
        //     ]

        // ];
        // $mall->save();
        
        // $shop = new \App\Models\Shop;
        // $shop->title = '积分商城';
        // $shop->icon = 'cart';
        // $shop->recommended_items = [1,4];
        // $shop->categories = [
        //     [
        //         'layout' => 'double',//double，once,detail
        //         'title' => '分类1',
        //         'title_en' => 'Categrory',
        //         'icon' => 'coffee', 
        //         'featured' => 1,
        //         'items' => [2,7]
        //     ],
        //     [
        //         'layout' => 'double',
        //         'title' => '分类2',
        //         'icon' => 'cart', 
        //         'featured' => 4,
        //         'items' => [1,2,4]
        //     ],
        //     [
        //         'layout' => 'double',
        //         'title' => '分类3',
        //         'icon' => 'cart', 
        //         'featured' => 4,
        //         'items' => [7,2,1]
        //     ],
        // ];
        // $shop->save();

        // $service->set('MALL_MENU', [
        //     ['type' => 'shop',  'title' => '积分商城', 'shopId' => '3423432423'],
        //     ['type' => 'split'],
        //     ['type' => 'orders',  'title' => '我的订单'],
        //     ['type' => 'member',  'title' => '会员中心'],
        //  ]);
        //  echo 'ok';

        // $service = new App\Services\ShoppingService;
        // var_dump($service->getShop()->categories);

        // $record = OperatingRecord::find('56bee5f72e26740c0b00002c');
        // var_dump($record->card);

        // $data = [
        //   'openid' => '89890380',
        //   'nickname' => 'testname',
        //   'sex' => 1,
        //   'headimgurl' => '11111111',
        // ];

        // $cardService = new CardService();
        // $cardService->createCardByWechatUser($data);

        // $value = $service->get('EVENT_RULES');
        // var_dump( $value);

        // $service->set('card', [
        //     'logo_url' => '/common/imgs/logo2.jpg',
        //     'color' => 'Color102',
        //     'title' => 'BAST CAFE',

        //     'sub_title' => 'Change the world. one cup at a time.',
        //     'font_color' => '#fff',
        // ]);

        // $service->set('THEME', [
        //     'key' => 'default',
        //     'value_color' => '#0092DB',
        //     'theme_color' => '#0092DB',
        //     'ticket_color'=>'#cf3e36',
        //     'goods_color'=>'#0092DB',
        //     'balance_color'=>'#0092DB',
        //     'slogan' => '我们缺少庸俗艳媚的成分，这就是区别。',

        //     //自定义卡面
        //     'card_font_color' => '#fff',
        //     'card_background_img' => '',
        // ]);

        echo 'OK';

        // $viewPath = 'wechat.' . $themeKey . '.' . 'hello';

        // echo $viewPath;
	}
    public function reset(){
        // DB::table('login_records')->delete();
        // DB::connection('mongodb')->collection('operating_records')->delete();
        // DB::connection('mongodb')->collection('mass')->delete();
        // DB::connection('mongodb')->collection('carts')->delete();
        // DB::table('order_payments')->delete();
        // DB::table('refunds')->delete();
        // DB::table('order_details')->delete();
        // DB::table('orders')->delete();
        
        // DB::table('address')->delete();
        // DB::table('signs')->delete();
        // DB::table('stores')->delete();
        // DB::connection('mongodb')->collection('shops')->delete();
        // DB::table('tickets')->delete();
        // DB::table('ticket_templates')->delete();
        // DB::table('properties')->delete();
        // DB::table('property_templates')->delete();
        // DB::table('good_specifications')->delete();
        // DB::table('goods')->delete();
        // echo 'ok';
        DB::table('customers')->delete();
        DB::table('site_datas')->delete();
        DB::table('risk_datas')->delete();
        DB::table('project_datas')->delete();
        // DB::table('reservations')->delete();
        DB::table('records')->delete();
        DB::table('progress_datas')->delete();
        DB::table('experiment_datas')->delete();
        DB::table('barcodes')->delete();
        //DB::table('progress')->delete();
        DB::table('project_site')->delete();
        DB::table('sites')->delete();
        DB::table('project_gene')->delete();
        DB::table('genes')->delete();
        DB::table('circum_risks')->delete();
        DB::table('project_risks')->delete();
        DB::table('projects')->delete();
        DB::table('experiments')->delete();

    }
    
    public function update()
    {
        $project_sites=ProjectSite::all();
        foreach ($project_sites as $model) {
            $weight=array();
            foreach ($model->weight as $key => $value) {
               $weight[$key]=array();
               $weight[$key]['score']=$value;
               $weight[$key]['tag']=null;
               $weight[$key]['mean']=null;
            }
            $model->weight=$weight;
            $model->save();
        }
        
    }

    private function noticeSeed()
    {
        $service = new NoticeService;
        $service->sendByApp([3], '第一条测试通知', date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)));
        $service->sendByApp([2,4], '第二条测试通知', date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)));
        $service->sendByApp([4], '第三条测试通知', date('Y-m-d H:i:s', time()));
        $service->sendByApp('ALL', '第四条测试通知', date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)));
        $service->sendByApp('ALL', '第五条测试通知', date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)));
        $service->sendByApp([1,2,3], '第六条测试通知', date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)));
        $service->sendByApp('ALL', '第七条测试通知', date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60)));
    }
}