<?php

namespace App\Services;
use App\Events\TicketVerified;
use App\Models\Ticket;
use App\Models\TicketTemplate;
use App\Models\Card;
use App\Models\CommoditySpecification;
use App;
use App\Services\SettingService;

class TicketService
{
    /**
     * 获取用户现有的所有优惠券
     * @method getCardTicketList
     * @param  int        $cardId 会员id
     * @return array
     */
    public function getCardTicketList($cardId){
        return Ticket::whereHas('ticketTemplate', function($query){
            $query->where('end_timestamp', '>', date('Y-m-d H:i:s', time()));
        })->with('ticketTemplate')->where('card_id', $cardId)->orderBy('created_at', 'desc')->get();
    }

    public function getUsedTickets($cardId){
        return Ticket::onlyTrashed()->where('card_id', $cardId)->get();
    }

//  购买商品时核销卡券,但不触发核销卡券事件
    public function writeoffTicketNotEvent($id)
    {
        $ticket=Ticket::find($id);
        $ticket->verified_at=time();
        $ticket->save();
        $ticket->delete();
        if($ticket->is_wechat_received){
            $wechat = App::make('WechatApplication');
            $cardWechatService = $wechat['card'];
            $cardWechatService->consume($ticket->ticket_code,$ticket->ticket_template_id);
        }
    }

    public function writeoffTicket($id){
        $ticket=Ticket::find($id);
        $ticket->verified_at=time();
        $ticket->save();
        $ticket->delete();
        $card=Card::find($ticket->card_id);
        if($ticket->is_wechat_received){
            $wechat = App::make('WechatApplication');
            $cardWechatService = $wechat['card'];
            $cardWechatService->consume($ticket->ticket_code,$ticket->ticket_template_id);
        }
        event(new TicketVerified($card,$ticket));
    }

    public function getTicket($id)
    {
        return Ticket::with('ticketTemplate')->where('id', $id)->first();
    }

    public function getOneTicket($cardId,$ticketId)
    {
        return Ticket::with('ticketTemplate')->where('card_id', $cardId)->where('id', $ticketId)->first();
    }

    /******************ticket template****************************/
    public function getTicketTemplateList(){
        return TicketTemplate::all();
    }

    public function getTicketTemplateUsableList(){
        return TicketTemplate::where('end_timestamp', '>', date('Y-m-d H:i:s', time()))->get();
    }

    public function getTicketTemplate($id){
        return TicketTemplate::where('id', $id)->first();
    }

    public function addTicketTemplate($ticketTemplate){
        //提交到微信
        $wechat = App::make('WechatApplication');
        $card = $wechat->card;
        $wechatArray = $ticketTemplate;
        $especial = [];
        unset($wechatArray['card_type'], $wechatArray['wechat_status'],
            $wechatArray['disable_shop'], $wechatArray['disable_online']);
        //特殊字段
        switch ($ticketTemplate['card_type']) {
            case 'GENERAL_COUPON':
                $especial['default_detail'] = $wechatArray['general_coupon_default_detail'];
                break;
            case 'GROUPON':
                $especial['deal_detail'] = $wechatArray['groupon_deal_detail'];
                break;
            case 'GIFT':
                $especial['gift'] = $wechatArray['gift_gift'];
                break;
            case 'DISCOUNT':
                $especial['discount'] = $wechatArray['discount_discount'];
                $especial['discount'] = 100 - $especial['discount'];
                break;
            case 'CASH':
                $especial['least_cost'] = $wechatArray['cash_least_cost'];
                $especial['least_cost'] = $especial['least_cost'] * 100;
                $especial['reduce_cost'] = $wechatArray['cash_reduce_cost'];
                $especial['reduce_cost'] = $especial['reduce_cost'] * 100;
                break;
            default:
                # code...
                break;
        }
        unset($wechatArray['cash_least_cost'], $wechatArray['cash_reduce_cost'], $wechatArray['discount_discount'],
            $wechatArray['gift_gift'], $wechatArray['general_coupon_default_detail'], $wechatArray['groupon_deal_detail']);
        //时间
        $wechatArray['begin_timestamp'] = strtotime($wechatArray['begin_timestamp']);
        $wechatArray['end_timestamp'] = strtotime('+1 day', strtotime($wechatArray['end_timestamp'])) - 1;
        $ticketTemplate['end_timestamp'] = date('Y-m-d H:i:s', $wechatArray['end_timestamp']);
        $wechatArray['date_info'] = [
            'type' => $wechatArray['date_info_type'],
            'begin_timestamp' => $wechatArray['begin_timestamp'],
            'end_timestamp' => $wechatArray['end_timestamp'],
        ];
        unset($wechatArray['date_info_type'], $wechatArray['begin_timestamp'], $wechatArray['end_timestamp']);
        //库存
        $wechatArray['sku'] = [
            'quantity' => $wechatArray['quantity']
        ];
        unset($wechatArray['quantity']);
        //基础信息
        $setting = new SettingService();
        $ticket = $setting->get('TICKET');
        $wechatArray['logo_url'] = $ticket['logo_url'];
        $wechatArray['brand_name'] = $ticket['brand_name'];
        $wechatArray['service_phone'] = $ticket['service_phone'];
        $wechatArray['custom_url_name'] = $ticket['custom_url_name'];
        $wechatArray['custom_url_sub_title'] = $ticket['custom_url_sub_title'];
        $wechatArray['code_type'] = 'CODE_TYPE_TEXT';
        $result = $card->create($ticketTemplate['card_type'], $wechatArray, $especial);
        if($result['errcode'] == 0){
            //本地保存
            $TicketTemplate = new TicketTemplate;
            $TicketTemplate->title = $ticketTemplate['title'];
            $TicketTemplate->sub_title = $ticketTemplate['sub_title'];
            $TicketTemplate->card_type = $ticketTemplate['card_type'];
            $TicketTemplate->groupon_deal_detail = $ticketTemplate['groupon_deal_detail'];
            $TicketTemplate->cash_least_cost = $ticketTemplate['cash_least_cost'];
            $TicketTemplate->cash_reduce_cost = $ticketTemplate['cash_reduce_cost'];
            $TicketTemplate->discount_discount = $ticketTemplate['discount_discount'];
            $TicketTemplate->gift_gift = $ticketTemplate['gift_gift'];
            $TicketTemplate->general_coupon_default_detail = $ticketTemplate['general_coupon_default_detail'];
            $TicketTemplate->color = $ticketTemplate['color'];
            $TicketTemplate->description = $ticketTemplate['description'];
            $TicketTemplate->notice = $ticketTemplate['notice'];
            $TicketTemplate->begin_timestamp = $ticketTemplate['begin_timestamp'];
            $TicketTemplate->end_timestamp = $ticketTemplate['end_timestamp'];
            $TicketTemplate->quantity = $ticketTemplate['quantity'];
            $TicketTemplate->get_limit = $ticketTemplate['get_limit'];
            $TicketTemplate->date_info_type = $ticketTemplate['date_info_type'];
            $TicketTemplate->location_id_list = $ticketTemplate['location_id_list'];
            $TicketTemplate->wechat_ticket_id = $result['card_id'];
            $TicketTemplate->wechat_status = $ticketTemplate['wechat_status'];
            $TicketTemplate->disable_online = $ticketTemplate['disable_online'];
            $TicketTemplate->disable_shop = $ticketTemplate['disable_shop'];
            $TicketTemplate->save();
            return $TicketTemplate->id;
        }else {
            throw new \Dingo\Api\Exception\StoreResourceFailedException('优惠券微信创建失败', $result);
        }
    }

    /**
     * 更新优惠券
     * @method updateTicketTemplate
     * @param  array               $ticketTemplate 将要修改的内容
     * @param  int               $id             要修改的id
     * @return int
     */
    public function updateTicketTemplate($ticketTemplate, $id){
        $TicketTemplate = TicketTemplate::find($id);
        $wechat = App::make('WechatApplication');
        $card = $wechat->card;
        $type = $ticketTemplate['card_type'];
        $basic_set = [];
        if (isset($ticketTemplate['description'])) {
            $basic_set['description'] = $ticketTemplate['description'];
        }
        if (isset($ticketTemplate['begin_timestamp'])) {
            if (!isset($basic_set['date_info'])) {
                $basic_set['date_info'] = [];
            }
            $basic_set['date_info']['begin_timestamp'] = strtotime($ticketTemplate['begin_timestamp']);
        }
        if (isset($ticketTemplate['end_timestamp'])) {
            if (!isset($basic_set['date_info'])) {
                $basic_set['date_info'] = [];
            }
            $basic_set['date_info']['end_timestamp'] = strtotime($ticketTemplate['end_timestamp']);
        }
        if (isset($ticketTemplate['color'])) {
            $basic_set['color'] = $ticketTemplate['color'];
        }
        if ($basic_set != []) {
            $card->update($TicketTemplate->wechat_ticket_id, strtolower($type), $basic_set);
        }
        unset($ticketTemplate['card_type']);
        foreach ($ticketTemplate as $key => $value) {
            $TicketTemplate[$key] = $ticketTemplate[$key];
        }
        $TicketTemplate->save();
        return $id;
    }

    public function deleteTicketTemplate($id){
        $TicketTemplate = TicketTemplate::find($id);
        $wechat = App::make('WechatApplication');
        $card = $wechat->card;
        $card->delete($TicketTemplate->wechat_ticket_id);

        TicketTemplate::destroy($id);
    }

    public function ticketTemplateIdIsUsed($id){
        return CommoditySpecification::where('sellable_type', 'App\Models\TicketTemplate')->where('sellable_id', $id)->get();
    }

    public function getAvailableTickets($card, $orderType, $totalFee) {
        $tickets = Ticket::whereHas('ticketTemplate', function($query){
            $query->where('end_timestamp', '>', date('Y-m-d H:i:s', time()));
        })->with('ticketTemplate')->where('card_id', $card->id)->orderBy('created_at', 'desc')->get();
        //TODO:优惠券适用范围筛选
        $results = [];
        foreach ($tickets as $ticket) {
            $template = $ticket->ticketTemplate;
            if ($template->card_type === 'CASH') {
                if ($template->cash_least_cost === 0 || $template->cash_least_cost <= $totalFee) {
                    $results[] = $ticket;
                }
            }
            elseif ($template->card_type === 'DISCOUNT'){
                $results[] = $ticket;
            }
        }
        return $results;
    }

    public function findBestTicket($tickets, $totalFee)
    {
        $off = 0;
        $baseTicketId = null;
        foreach ($tickets as $ticket) {
            if ($ticket->ticketTemplate->card_type === 'CASH' &&
            $ticket->ticketTemplate->cash_reduce_cost >= $totalFee) {
                continue;
            }

            $cOff = $this->calculateOffByTicket($ticket, $totalFee);

            if ($cOff >= $off) {
                $off = $cOff;
                $baseTicketId = $ticket->id;
            }
        }
        return $baseTicketId;
    }

    public function calculateOffByTicket($ticket, $totalFee)
    {
        $template = $ticket->ticketTemplate;
        if ($template->card_type === 'CASH') {
            if($template->cash_reduce_cost >= $totalFee){
                return $totalFee;
            }else{
                return $template->cash_reduce_cost;
            }
        }
        elseif ($template->card_type === 'DISCOUNT'){
            return $totalFee * (100 - $template->discount_discount) / 100;
        }
    }

    public function canUseTickets($orderDetails)
    {
        $canUseTickets = true;
        foreach($orderDetails as $orderDetail){
            $commodityHistory = $orderDetail->commoditySpecificationHistory->commodityHistory;
            if($commodityHistory->disable_coupon){
                $canUseTickets = false;
                break;
            }
        }
        return $canUseTickets;
    }
}
