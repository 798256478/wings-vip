<?php

namespace App\Http\Controllers\Wechat;

use Illuminate\Http\Request;
use App;
use App\Services\MallService;
use App\Services\CommodityService;
use App\Exceptions\WingException;
use App\Services\SettingService;
use App\Services\QrCodeService;

class MallController extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}

    public function showMall(Request $request)
    {
		$mallService = new MallService;
		$commodityService = new CommodityService;

		$config = $mallService->getMallConfig();

		$categories = [
			'recommond' => [
				'layout' => 'double',
				'items' => $config[0]['commodities']['items'],
				'title' => '主推商品',
			],
			'all' => [
				'layout' => 'double',
				'items' => $commodityService->getCommodityList(),
				'title' => '全部商品',
			]
		];

		$data = [
			'shops' => $mallService->getAllShop(),
			'card_settings' => $this->settingService->get('CARD'),
			'categories' => $categories,
			'config' => $mallService->getMallConfig(),
			'cartData' => $request->session()->get('CART_DATA', '[]'),
		];

        return $this->theme_view('shop.mall', $data);
    }

	public function showShop(Request $request, $id = null)
	{
		$mallService = new MallService;
		
		$data = [
			'shop' => $mallService->getShop($id),
			'shops' => $mallService->getAllShop(),
			'cartData' => $request->session()->get('CART_DATA', '[]'),
		];
		return $this->theme_view('shop.shop', $data);
	}

	public function showItem(Request $request, $id)
	{
		$commodityService = new CommodityService;
		$mallService = new MallService;
		$qrCodeService = new QrCodeService();
		$data = [
			'item' => $commodityService->getCommodity($id),
			'shops' => $mallService->getAllShop(),
			'cartData' => $request->session()->get('CART_DATA', '[]'),
			'qrCode' => $qrCodeService->createCommodityQrCode($id),
		];

		if(isset($data['item']['suit'])) {
			return $this->theme_view('shop.suit', $data);
		}else {
			return $this->theme_view('shop.item', $data);
		}
	}

	public function putCartData(Request $request)
	{
		$request->session()->put('CART_DATA', $request->input('cartData'));
	}
}
