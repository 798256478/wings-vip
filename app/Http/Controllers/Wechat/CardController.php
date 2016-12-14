<?php

namespace App\Http\Controllers\Wechat;

use App\Http\Controllers\Wechat\Controller;
use Illuminate\Http\Request;
use App;

use App\Events\SignEvent;

use App\Services\TicketService;
use App\Services\OrderService;
use App\Services\SettingService;
use App\Services\OperatingRecordService;
use App\Services\SignService;
use App\Services\CardService;
use App\Services\RedeemCodeService;
use App\Services\NoticeService;

class CardController extends Controller
{
	public function __construct()
    {
        parent::__construct();
    }
    
	public function showCard()
	{
		$ticketService = new TicketService();
        $settingService = new SettingService();
		$data = [
			'tickets' => $ticketService->getCardTicketList($this->currentCard->id),
			'card_settings' => $this->settingService->get('CARD'),
            'ticket_settings' => $this->settingService->get('TICKET'),
		];

		return $this->theme_view('welcome', $data);
	}

    public function showBalance()
    {
		$settingService = new SettingService();
		$balance_rule = $settingService->get('BALANCE');
		if (!empty($balance_rule) && isset($balance_rule['buy']))
			$data['is_percentage'] = array_values($balance_rule['buy'])[0] <= 1;

        $orderService = new OrderService();
		$data['balance_rule_json'] = json_encode($balance_rule['buy']);
		$data['card_settings'] =$this->settingService->get('card');
		$data['saving_records'] = $orderService->getOrderListByIdAndType($this->currentCard->id,'BALANCE');
		$data['consume_records'] = $orderService->getOrderListByIdAndType($this->currentCard->id,'CONSUME');
		$data['balance_rule'] =$balance_rule;

		return $this->theme_view('balance', $data, true);
    }

    public function showBonus()
    {
        $service = new OperatingRecordService();

		$data['redemption_records'] = $service->getBonusExchange($this->currentCard->card_code);
		$data['gain_records'] = $service->getBonusList($this->currentCard->card_code);
		
        return $this->theme_view('bonus', $data);
    }

    public function showRecords()
    {
        $service = new OperatingRecordService();

        $data['records']=$service->GetOperatingRecordsByCardCode($this->currentCard->card_code);

        return $this->theme_view('record',$data);
    }

    public function showMemberInfo()
    {
        return $this->theme_view('member_info');
    }

    public function showRedeemCode()
    {
        return $this->theme_view('redeem_code');
    }

    public function showAttendance()
    {
        $service = new SignService;

        $signData = $service->getSignData($this->currentCard->id);
		$data['todayData']=$service->getTodaySignData($this->currentCard->id);
		$data['signData']=$signData['data'];
		$data['continuousDays']=$signData['days'];
		$data['card']=$this->currentCard;

		return $this->theme_view('sign',$data);
    }

    public function redeemCode(Request $request)
    {
		$this->validate($request, [
			'code' => 'required'
		]);

		$code = $request->input('code');
        $service = new RedeemCodeService;

        return $service->redeem($code, $this->currentCard);
    }

	public function receiveNotice(Request $request)
	{
		$cardId = $this->currentCard->id;
		$noticeId = null;
		if ($request->has('noticeId')) {
			$noticeId = $request->input('noticeId');
		}

		$service = new NoticeService;
		$service->receive($cardId, $noticeId);
	}

    public function add(Request $request)
	{
		try {
			$data=$request->all();
			if(!isset($data['balance_fee']) || $data['balance_fee']==0){
				throw new WingException('储值金额必须大于0', 401);
			}
			$order = [];
			$order['order']['card_id'] = $this->currentCard->id;
			$order['order']['body'] = '微信储值';
			$order['order']['remark'] = '';
			$order['order']['type'] = 'BALANCE';
			$order['order']['channel'] = 'WECHAT';
			$order['order']['money_pay_amount'] = $data['balance_fee'];
			$order['order']['bonus_pay_amount'] = 0;
			$order['order']['balance_fee'] = $data['balance_fee'];
			$order['order']['balance_present'] = $data['balance_present'];
			$order['details']=[];
			$orderService=new OrderService();
			$id=$orderService->createOrder($order);
			return $id;
		} catch (\Exception $e) {
			return json_exception_response($e);
		}

	}

	public function postAttendance(){
        $signService = new SignService();
        $cardService = new CardService();

    	$data= $signService->getTodaySignData($this->currentCard->id);
    	if(!count($data)){
    		event(new SignEvent($this->currentCard));
    	}

    	return $cardService->getCard($this->currentCard->id);;
    }
}