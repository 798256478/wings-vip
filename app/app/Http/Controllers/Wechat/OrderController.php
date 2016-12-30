<?php

namespace App\Http\Controllers\Wechat;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Services\AddressService;
use App\Services\CommodityService;
use App\Services\TicketService;
use App\Services\MallService;
use App;
use App\Exceptions\WingException;

class OrderController extends Controller
{
	protected $orderService;
	protected $commodityService;
	protected $addressService;
	public function __construct(OrderService $orderService,CommodityService $commodityService,AddressService $addressService, TicketService $ticketService)
	{
		parent::__construct();
		$this->orderService = $orderService;
		$this->commodityService = $commodityService;
		$this->addressService = $addressService;
		$this->ticketService = $ticketService;
	}

    //订单列表页
	public function showOrders(Request $request)
	{
		$data['orders']=$this->orderService->getOrderListByCardId($this->currentCard->id);
		$data['state']=['NOT_PAY'=>'待付款','PAY_SUCCESS'=>'已付款','WAIT_DELIVER'=>'待发货','DELIVERED'=>'已发货','FINISH'=>'已完成','CLOSED'=>'订单已取消'];
		$data['refundState']=['APPLY'=>'申请退款','REFUND'=>'同意退款','REFUSED'=>'拒绝退款','CLOSE'=>'退款申请已取消'];
		return $this->theme_view('shop.orders',$data);
	}

//	public function newSuitOrder(Request $request,$suitId,$amount)
//	{
//		if(!$amount){
//			return redirect('/wechat/mall/shop');
//		}
//		$cartData = [
//			'suitId' => $suitId,
//			'amount' => $amount
//		];
//
//		$request->session()->put('CART_DATA', json_encode($cartData));
//		return $this->newOrder($request);
//	}

	public function newOrder(Request $request)
	{
//		$cartData = [
//			[
//				'specificationId' => 3,
//				'quantity' => 1
//			],
//			[
//				'specificationId' => 1,
//				'quantity' => 1
//			],
//			[
//				'specificationId' => 2,
//				'quantity' => 1
//			]
//		];
//		$request->session()->put('CART_DATA', json_encode($cartData));
		$data['items'] = json_decode($request->session()->get('CART_DATA'), true);

		if(count($data['items']) <= 0){
			return redirect('/wechat/mall');
		}

		$address_id = null;
		if($request->has('addressId')) {
			$address_id = $request->input('addressId');
			$data['address_id'] = $address_id;
		}

		$orderDetails = $this->orderService->buildGoodsOrderDetails($data);
		$order = $this->orderService->buildGoodsOrder($data, $orderDetails, 'WECHAT', $this->currentCard);
		$canUseTickets = $this->ticketService->canUseTickets($orderDetails);

		$bonusRule = $this->orderService->getBonusRule();
		if($canUseTickets){
			$availableTickets = $this->ticketService->getAvailableTickets($this->currentCard, $order->type, $order->total_fee);
			$bestTicketId = $this->ticketService->findBestTicket($availableTickets, $order->total_fee);
		}else{
			$availableTickets = [];
			$bestTicketId = null;
		}
		$data = [
			'order' => $order,
			'orderDetails' => $orderDetails,
			'availableTickets' => $availableTickets,
			'bestTicketId' => $bestTicketId ? $bestTicketId : null,
			'addressId' => $address_id,
			'canUseTickets' => $canUseTickets,
			'bonusRule' => $bonusRule,
//			'suit' => isset($suit) ? $suit : false,
		];
//		dd($data);

		return $this->theme_view('shop.new_order',$data, true);
	}

	public function postBalanceOrder(Request $request){
		$this->validate($request, [
			'balance_fee' => 'required|Numeric'
		]);

		$data = [
			'balance_fee' => $request->input('balance_fee'),
			'payments' => [
				['type' => 'WECHAT', 'amount' => $request->input('balance_fee')],
			]
		];

		return $this->orderService->createBalanceOrder($data,
		'WECHAT', $this->currentCard);
	}

	// 参数说明
    // $data = [
    //     'address_id' => 3,
    //     'remark' => '备注',
    //     'ticket_id' => 2,
	//     'balance_pay' => 2,
	//     'wechat_pay' => 2,
    // ];
	public function postGoodsOrder(Request $request){
		$this->validate($request, [
			'address_id' => 'Numeric',
			'ticket_id' => 'Numeric',
			'balance_pay' => 'Numeric',
			'wechat_pay' => 'Numeric',
			'remark' => 'String',
			'bonus_pay' => 'Numeric'
		]);
		$data = $request->only(['address_id','ticket_id','remark']);

		$data['items'] = json_decode($request->session()->get('CART_DATA'), true);
		if(count($data['items'])===0){
			return redirect('/wechat/order/');
		}

		$payments = [];
		if ($request->has('wechat_pay') && $request->input('wechat_pay') != 0) {
			$payments[] = ['type' => 'WECHAT', 'amount' => $request->input('wechat_pay')];
		}
		if ($request->has('balance_pay') && $request->input('balance_pay') != 0) {
			$payments[] = ['type' => 'BALANCE', 'amount' => $request->input('balance_pay')];
		}
		if ($request->has('bonus_pay') && $request->input('bonus_pay') != 0) {
			$payments[] = ['type' => 'BONUS', 'use_bonus' => $request->input('bonus_pay')];
		}
		$data['payments'] = $payments;

		$results = $this->orderService->createGoodsOrder($data,
		'WECHAT', $this->currentCard);

		if (is_array($results) && $results['order_id']) {
			//清空购物车
			$request->session()->set('CART_DATA', '');
		}

		return $results;
	}

    //订单详情
	public function showOrder($id)
	{
		$data['order'] = $this->orderService->getOrder($id);
		if($data['order'] && $data['order']->card_id==$this->currentCard->id ) {
			$data['refundState'] = ['APPLY'=>'申请退款','REFUND'=>'同意退款','REFUSED'=>'卖家拒绝退款','CLOSE'=>'退款申请已取消'];
			$mallService = new MallService();
			$data['shops'] = $mallService->getAllShop();
			$data['bonusPay']=false;
			foreach($data['order']->orderPayments as $key=>$val){
				if($val->type === 'BONUS'){
					$data['bonusPay'] = $val;
				}
			}
			return $this->theme_view('shop.order_details', $data);
		}else {
			return $this->theme_view('shop.shopping_error');
		}

	}

	//支付成功
	public function showPaySuccess($id)
	{
		$data['order'] = $this->orderService->getOrderAndPayments($this->currentCard->id,$id);
		if(!$data['order']){
			return $this->theme_view('shop.shopping_error');
		}
		return $this->theme_view('shop.shopping_success',$data);
	}



    //确认收货， 不确定是否需要删除
	public function confirmReceipt($id){
		$this->orderService->orderComplete($id);
	}

    //取消订单
	public function deleteOrder($id){
		$this->orderService->orderClose($id);
	}

	//显示线下扫描二维码支付页面
	public function showQrCodePay()
	{
		$seting = App\Models\Setting::where('key','CARD')->first();
		$data['shopName'] = isset($seting->value['title']) ? $seting->value['title'] : '确认支付';
		return $this->theme_view('shop.qr_code_pay',$data,true);
	}

	//新建线下扫描二维码支付订单
	public function postQrCodePay(Request $request)
	{
		try {
			$this->validate($request, [
					'wechat_pay' => 'Numeric',
			]);
			$totalFee = $request->input('wechat_pay',0);
			if($totalFee<=0){
				throw new WingException('订单金额异常。', 401);
			}
			return $this->orderService->createQrCodeOrder($totalFee,$this->currentCard);
		} catch (\Exception $e) {
			return json_exception_response($e);
		}
	}
}
