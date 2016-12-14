<?php

namespace App\Http\Controllers\Api;

use Dingo\Api\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
// use Dingo\Api\Routing\Helpers;

use App\Services\AuthService;
use App\Services\CardService;
use App\Services\TicketService;
use App\Services\PropertyService;
use App\Services\OperatingRecordService;

class CardController extends Controller
{

    protected $authService;
    protected $userService;
    protected $ticketService;
    protected $propertyService;
    protected $operatingRecordService;
    public function __construct(AuthService $authService, CardService $cardService, TicketService $ticketService, PropertyService $propertyService,OperatingRecordService $operatingRecordService)
    {
        $this->authService = $authService;
        $this->cardService = $cardService;
        $this->ticketService = $ticketService;
        $this->propertyService = $propertyService;
        $this->operatingRecordService = $operatingRecordService;
    }

    public function getCardSummaries ()
    {
        try{
            //$this->authService->singleRoleVerify('admin');

            return $this->cardService->getCardSummaries();
        }
        catch (\Exception $e){
            return json_exception_response($e);
        }
    }

    /**
     * 获取新注册的200名用户
     * @method getNewCardSummaries
     * @return array
     */
    public function getNewCardSummaries(){
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->cardService->getNewCardSummaries();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    public function getTotal(){
        try {
            $this->authService->singleRoleVerify('admin');
            return $this->cardService->getTotal();
        } catch (\Exception $e) {
            return json_exception_response($e);
        }
    }

    /**
     * 营销端获取单个用户详情
     * @method getCardDetail
     * @param  int        $cardId 会员卡id
     * @return array
     */
     public function getCardDetail($cardId){
         try {
             $this->authService->singleRoleVerify('admin');
             $card = $this->cardService->getCardById($cardId);
             $card->balance = number_format($card->balance, 2);
             $card->bonus = number_format($card->bonus);
             $past30Day = $this->cardService->getPast30Day($cardId);
             $avgmonth = floor((time() - strtotime($card->created_at)) / 2592000) + 1;
             $cardUsedTicket = $this->ticketService->getUsedTickets($cardId);
             $last30Ticket = 0;
             foreach ($cardUsedTicket as $val) {
                 if(strtotime($val['verified_at']) > strtotime(date("Y-m-d",strtotime("-30 day")))){
                     $last30Ticket++;
                 }
             }
             if($past30Day->cost == null){
                 $past30Day->cost = '0.00';
             }
             $result = array(
                 'cardSummaries' => $card,
                 'past30Day' => $past30Day,
                 'avgMonth' => array(
                     'avgExpense' => number_format((float)$card->total_expense / $avgmonth, 2),
                     'avgVisit' => number_format((float)$card->total_visit / $avgmonth, 2)
                 ),
                 'times' => $card->total_visit != 0 ? number_format((float)$card->total_expense/$card->total_visit, 2) : '0.00',
                 'tickets' => array(
                     'data' => $cardUsedTicket,
                     'total' => count($cardUsedTicket),
                     'times' => $card->total_visit != 0 && count($cardUsedTicket) > 0 ?
                                    number_format((float)(count($cardUsedTicket)) / $card->total_visit, 2) : '0.00',
                     'past30Day' => $last30Ticket,
                     'avgMonth' => number_format((float)count($cardUsedTicket) / $avgmonth, 2)
                 ),
                 'records' => $this->operatingRecordService->getByConditions(['show_to_member'=>true,'card_id'=>$cardId]),
             );
             if((float)$result['past30Day']->cost > (float)$result['avgMonth']['avgExpense']){
                 $result['color']['cost'] = 'red';
             }else{
                 $result['color']['cost'] = 'green';
             }
             if((float)$result['past30Day']->arrive > (float)$result['avgMonth']['avgVisit']){
                 $result['color']['arrive'] = 'red';
             }else{
                  $result['color']['arrive'] = 'green';
             }
             if((float)$result['tickets']['past30Day'] > (float)$result['tickets']['avgMonth']){
                 $result['color']['ticket'] = 'red';
             }else{
                 $result['color']['ticket'] = 'green';
             }
             return $result;
         } catch (\Exception $e) {
             return json_exception_response($e);
         }
     }

     /*获取会员卡以及相关信息*/
     public  function getCard($cardId)
     {
        try {
            $this->authService->rolesVerifyWithOr('captain,cashier');
            $card = $this->cardService->getCardById($cardId);
            $card->balance = number_format($card->balance, 2);
            $card->bonus = number_format($card->bonus);
            $properties= $this->propertyService->getCardPropertyList($cardId);
            $tickets=$this->ticketService->getCardTicketList($cardId);
            $records=$this->operatingRecordService->getByConditions(['show_to_member'=>true,'card_id'=>$cardId]);
            $data=array('card'=>$card, 'properties' => $properties , 'tickets' => $tickets,'records'=>$records);
            return $data;
        }
        catch (\Exception $e) {
              return json_exception_response($e);
         }
     }

     /**
      * 根据条件查询会员卡列表
      * @method searchCardList
      * @param  string         $val 查询条件
      * @return array
      */
     public function searchCardList($val){
         try {
             return $this->cardService->searchCardList($val);
         } catch (\Exception $e) {
             return json_exception_response($e);
         }
     }

     //新开卡初始化。
     public function initCardData(Request $request)
     {
         try{
             if(!$request->has('data')){
                   throw new \Dingo\Api\Exception\StoreResourceFailedException("数据错误，请联系管理员");
             }
             else{
                $data=$this->checkInitData($request);
                $result= $this->cardService->initCardData($data);
                if(!($result===true)){
                     throw new \Dingo\Api\Exception\StoreResourceFailedException("出现错误，请联系管理员");
                }
             }
         }
        catch(\Exception $e){
            return json_exception_response($e);
        }
     }

     private function checkInitData(Request $request)
     {
          $rules=[
            'card_id'=>['required', 'integer'],
            'bonus'=>['integer'],
            // 'balance'=>['required'],
            'level'=>['integer'],
        ];
        $data=$request->input('data');
        $validator =app('validator')->make($data,$rules);
        if ($validator->fails()){
            throw new \Dingo\Api\Exception\StoreResourceFailedException("验证错误", $validator->errors());
        }
        if(count($data['properties'])>0){
            $rulesproperty=[
                'card_id'=>['required', 'integer'],
                'property_template_id'=>['required','integer'],
                'expiry_date'=>['date'],
                'quantity'=>['required','integer'],
            ];
            foreach ($data['properties'] as $model) {
                 $validator =app('validator')->make($model,$rulesproperty);
                 if ($validator->fails()){
                     throw new \Dingo\Api\Exception\StoreResourceFailedException("服务信息验证错误", $validator->errors());
                 }
            }
        }

        return  $data;
     }
}
