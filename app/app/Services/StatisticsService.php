<?php

namespace App\Services;

use App\Models\Card;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\TICKET;
use App\Models\CommoditySpecification;
use App\Models\Commodity;
use DB;
use App\Models\OperatingRecord;
use App\Services\SettingService;

class StatisticsService
{
    public function getSettingVerify($key)
    {
        $setting = new SettingService();
        $statistics = $setting->get('StatisticeVerify');
        if (isset($statistics)) {
            return $statistics[$key];
        } else {
            return;
        }
    }

    public function getStatisticsDate($statistics)
    {
        $dateList = $this->getDays($statistics['year'], $statistics['month']);
        $result = $this->getData($dateList, $statistics['key']);
        return $result;
    }

    public function getCommoditiesStatistics($request)
    {
        $result = $this->getCommoditiesData($request['year'], $request['month']);
        return $result;
    }

    public function getCommodityStatisticsData($date,$id)
    {
        $dateList = $this->getDays($date['year'], $date['month']);
        $result = $this->getCommodityData($dateList,$id);
        return $result;
    }


    public function getDateList()
    {
        $dateList = [
            'year' => [],
            'month' => ['all', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'],
        ];
        $year = (int) date('Y', time());
        for ($i = 0; $i < 5; ++$i) {
            $dateList['year'][] = $year - 4 + $i;
        }

        return $dateList;
    }

    private function getData($dateList, $key)
    {
        $colArr = [];
        foreach ($dateList as $date) {
            switch ($key) {
                case 'CARD':
                    $newCard = Card::where('created_at', '>=', date('Y-m-d H:i:s', $date['begintime']))
                        ->where('created_at', '<', date('Y-m-d H:i:s', $date['endtime']))->get();
                    $arrive = Order::where('created_at', '>=', date('Y-m-d H:i:s', $date['begintime']))
                        ->where('created_at', '<', date('Y-m-d H:i:s', $date['endtime']))->where('channel', 'SHOP')
                        ->where('type', '<>', 'BALANCE')->where('state', 'FINISH')->get();
                    $colArr['arrive'][] = count($arrive->toArray());
                    $colArr['new'][] = count($newCard->toArray());
                    $colArr['catearr'][] = $date['catearr'];
                    // $colArr['data']['arrive'] = $arrive;
                    // $colArr['data']['new'] = $newCard;
                    break;
                case 'BALANCE':
                    $balance = Order::select('balance_fee')->where('created_at', '>=', date('Y-m-d H:i:s', $date['begintime']))
                        ->where('created_at', '<', date('Y-m-d H:i:s', $date['endtime']))->where('type', 'BALANCE')
                        ->where('state', 'FINISH')->get();
                    $totalBalance = 0;
                    foreach ($balance as $value) {
                        $totalBalance += $value->balance_fee;
                    }
                    $useBalcnce = OrderPayment::select('amount')->where('type', '余额')->whereHas('order', function($query) use ($date){
                        $query->where('created_at', '>=', date('Y-m-d H:i:s', $date['begintime']))
                            ->where('created_at', '<', date('Y-m-d H:i:s', $date['endtime']))->where('type', '<>','BALANCE')
                            ->where('state', 'FINISH');
                    })->get();
                    $totalUseBalance = 0;
                    foreach ($useBalcnce as $value) {
                        $totalUseBalance += $value->amount;
                    }
                    $colArr['balance'][] = $totalBalance;
                    $colArr['useBalance'][] = $totalUseBalance;
                    $colArr['catearr'][] = $date['catearr'];
                    break;
                case 'TICKET':
                    $ticket = Ticket::where('created_at', '>=', date('Y-m-d H:i:s', $date['begintime']))
                        ->where('created_at', '<', date('Y-m-d H:i:s', $date['endtime']))->get();
                    $useTicket = Ticket::onlyTrashed()->where('deleted_at', '>=', date('Y-m-d H:i:s', $date['begintime']))
                        ->where('deleted_at', '<', date('Y-m-d H:i:s', $date['endtime']))->get();
                    $colArr['ticket'][] = count($ticket);
                    $colArr['useTicket'][] = count($useTicket);
                    $colArr['catearr'][] = $date['catearr'];
                    break;
                case 'PROPERTY':
                    $property = OperatingRecord::where('create_time', '>=', $date['begintime'])
                        ->where('create_time', '<', $date['endtime'])
                        ->where('type', '<>', 'MASS')->get();
                    $totalProperty = 0;
                    $totalUseProperty = 0;
                    foreach ($property as $value) {
                        if(isset($value->changes['property'])){
                            if($value->changes['property'] > 0){
                                $totalProperty += $value->changes['property'] * count($value->cards);
                            }elseif ($value->changes['property'] < 0) {
                                $totalUseProperty += $value->changes['property'] * count($value->cards);
                            }
                        }
                    }
                    $colArr['property'][] = $totalProperty;
                    $colArr['useProperty'][] = abs($totalUseProperty);
                    $colArr['catearr'][] = $date['catearr'];
                    break;
                case 'COST':
                    //当天总消费
                    $Amount = OrderPayment::select('amount')
                        ->where('created_at', '>=', date('Y-m-d H:i:s', $date['begintime']))
                        ->where('created_at', '<', date('Y-m-d H:i:s', $date['endtime']))
                        ->whereHas('order', function($query){
                            $query->where('type', '<>', 'BALANCE')
                                ->where('state', 'FINISH');
                        })->get();
                    $tAmount = 0;
                    foreach ($Amount as $value) {
                        $tAmount += $value->amount;
                    }
                    $colArr['total'][] = $tAmount;
                    //根据消费类型计算消费
                    $typeCol = OrderPayment::select('type')->groupBy('type')->get();
                    $typeList = $typeCol->toArray();
                    $colArr['typeList'] = $typeList;
                    foreach ($typeList as $value) {
                        $typeAmount = OrderPayment::select('amount')
                            ->where('created_at', '>=', date('Y-m-d H:i:s', $date['begintime']))
                            ->where('created_at', '<', date('Y-m-d H:i:s', $date['endtime']))
                            ->where('type', $value['type'])
                            ->whereHas('order', function($query) use ($date){
                                $query->where('state', 'FINISH');
                            })->get();
                        $totalAmount = 0;
                        foreach ($typeAmount as $val) {
                            $totalAmount += $val->amount;
                        }
                        $colArr['type'][$value['type']][] = $totalAmount;
                    }
                    //根据端计算消费
                    $channel = ['SHOP', 'WECHAT', 'DELIVERY'];
                    foreach ($channel as $value) {
                        $shopAmount = OrderPayment::select('amount')
                            ->where('created_at', '>=', date('Y-m-d H:i:s', $date['begintime']))
                            ->where('created_at', '<', date('Y-m-d H:i:s', $date['endtime']))
                            ->whereHas('order', function($query) use ($value){
                                $query->where('channel', $value)->where('type', '<>', 'BALANCE')
                                    ->where('state', 'FINISH');
                            })->get();
                        $totalShopAmount = 0;
                        foreach ($shopAmount as $val) {
                            $totalShopAmount += $val->amount;
                        }
                        $colArr['channel'][$value][] = $totalShopAmount;
                    }
                    $colArr['catearr'][] = $date['catearr'];
                    break;
                default:
                    # code...
                    break;
            }
        }
        switch ($key) {
            case 'CARD':
                $result['table'] = [
                    ['name' => '到店', 'data' => $colArr['arrive']],
                    ['name' => '新会员', 'data' => $colArr['new']],
                ];
                $result['type'] = ['type' => 'line', 'style' => 'smooth'];
                $result['catearr'] = $colArr['catearr'];
                return $result;
                break;
            case 'BALANCE':
                $result['table'] = [
                    ['name' => '储值', 'data' => $colArr['balance']],
                    ['name' => '消费', 'data' => $colArr['useBalance']],
                ];
                $result['type'] = ['type' => 'line', 'style' => 'smooth'];
                $result['catearr'] = $colArr['catearr'];
                return $result;
                break;
            case 'TICKET':
                $result['table'] = [
                    ['name' => '领券', 'data' => $colArr['ticket']],
                    ['name' => '用券', 'data' => $colArr['useTicket']],
                ];
                $result['type'] = ['type' => 'line', 'style' => 'smooth'];
                $result['catearr'] = $colArr['catearr'];
                return $result;
                break;
            case 'PROPERTY':
                $result['table'] = [
                    ['name' => '购买', 'data' => $colArr['property']],
                    ['name' => '核销', 'data' => $colArr['useProperty']],
                ];
                $result['type'] = ['type' => 'line', 'style' => 'smooth'];
                $result['catearr'] = $colArr['catearr'];
                return $result;
                break;
            case 'COST':
                $result['table'] = [
                    'channel' => [
                        ['name' => '店内消费', 'data' => $colArr['channel']['SHOP']],
                        ['name' => '微信消费', 'data' => $colArr['channel']['WECHAT']],
                        ['name' => 'DELIVERY', 'data' => $colArr['channel']['DELIVERY']],
                        // ['name' => '总消费', 'data' => $colArr['total']],
                    ],
                    'costtype' => [],
                ];
                foreach ($colArr['typeList'] as $value) {
                    $result['table']['costtype'][] = ['name' => $value['type'], 'data' => $colArr['type'][$value['type']]];
                }
                // $result['table']['costtype'][] = ['name' => '总消费', 'data' => $colArr['total']];
                $result['type'] = ['type' => 'area', 'stack' => true];
                $result['catearr'] = $colArr['catearr'];
                return $result;
                break;
            default:
                # code...
                break;
        }
    }

    public function getTotalData($day){
        switch($day){
            case 'today':
                $begintime = strtotime("now");
                $newCard = Card::where("created_at",">",date('Y-m-d',$begintime)." 00:00:00")->get();
                $arrive = Order::where("created_at",">",date('Y-m-d',$begintime)." 00:00:00")->where('channel', 'SHOP')
                            ->where('type', '<>', 'BALANCE')->where('state', 'FINISH')->get();
                $order = Order::where("created_at",">",date('Y-m-d',$begintime)." 00:00:00")->get();
                $balance = Order::select('balance_fee')->where("created_at",">",date('Y-m-d',$begintime)." 00:00:00")->where('type', 'BALANCE')->where('state', 'FINISH')->get();
                $totalBalance = 0;
                foreach ($balance as $value) {
                    $totalBalance += $value->balance_fee;
                }
                $consume = Order::select('total_fee')->where("created_at", '>',date('Y-m-d',$begintime).' 00:00:00')->where('type', 'CONSUME')->where('state', 'FINISH')->get();
                $totalConsume = 0;
                foreach ($consume as $value) {
                    $totalConsume += $value->total_fee;
                }
                break;
            case 'yesterday':
                $begintime = strtotime("now");
                $endtime=strtotime("-1 day");
                $newCard = Card::where("created_at", '>=',date('Y-m-d',$endtime)." 00:00:00")
                    ->where("created_at", '<',date('Y-m-d',$begintime).' 00:00:00')->get();
                $arrive = Order::where("created_at", '>=',date('Y-m-d',$endtime)." 00:00:00")
                    ->where("created_at", '<',date('Y-m-d',$begintime).' 00:00:00')->where('channel', 'SHOP')
                    ->where('type', '<>', 'BALANCE')->where('state', 'FINISH')->get();
                $order = Order::where("created_at", '>=',date('Y-m-d',$endtime)." 00:00:00")
                    ->where("created_at", '<',date('Y-m-d',$begintime).' 00:00:00')->get();
                $balance = Order::select('balance_fee')->where("created_at", '>=',date('Y-m-d',$endtime)." 00:00:00")
                    ->where("created_at", '<',date('Y-m-d',$begintime).' 00:00:00')->where('type', 'BALANCE')->where('state', 'FINISH')->get();
                $totalBalance = 0;
                foreach ($balance as $value) {
                    $totalBalance += $value->balance_fee;
                }
                $consume = Order::select('total_fee')->where("created_at", '>=',date('Y-m-d',$endtime)." 00:00:00")
                    ->where("created_at", '<',date('Y-m-d',$begintime).' 00:00:00')->where('type', 'CONSUME')->where('state', 'FINISH')->get();

                $totalConsume = 0;
                foreach ($consume as $value) {
                    $totalConsume += $value->total_fee;
                }

                break;
            default:
                $endtime=strtotime("-".$day." day");
                $newCard = Card::where("created_at",">",date('Y-m-d H:i:s',$endtime))->get();
                $arrive = Order::where("created_at",">",date('Y-m-d H:i:s',$endtime))->where('channel', 'SHOP')
                            ->where('type', '<>', 'BALANCE')->where('state', 'FINISH')->get();
                $order = Order::where("created_at",">",date('Y-m-d H:i:s',$endtime))->get();
                $balance = Order::select('balance_fee')->where("created_at",">",date('Y-m-d H:i:s',$endtime))->where('type', 'BALANCE')
                            ->where('state', 'FINISH')->get();
                $totalBalance = 0;
                foreach ($balance as $value) {
                    $totalBalance += $value->balance_fee;
                }
                $consume = Order::select('total_fee')->where("created_at", '>=',date('Y-m-d',$endtime)." 00:00:00")->where('type', 'CONSUME')->where('state', 'FINISH')->get();
                $totalConsume = 0;
                foreach ($consume as $value) {
                    $totalConsume += $value->total_fee;
                }
            }
            $colArr['balance']= $totalBalance;
            $colArr['consume'] = $totalConsume;
            $colArr['arrive']= count($arrive->toArray());
            $colArr['new'] = count($newCard->toArray());
            $colArr['order']=count($order->toArray());
            return $colArr;
    }

    public function getDaysData(){
        $data['today']=$this->getTotalData('today');
        $data['yesterday']=$this->getTotalData('yesterday');
        $data['week']=$this->getTotalData(7);
        $data['month']=$this->getTotalData(30);
        foreach($data as $k=>$v){
            $result['arrive'][$k]=$data[$k]['arrive'];
            $result['new'][$k]=$data[$k]['new'];
            $result['order'][$k]=$data[$k]['order'];
            $result['consume'][$k]=$data[$k]['consume'];
            $result['balance'][$k]=$data[$k]['balance'];
        }
        $result['arrive']['title']='到店';
        $result['new']['title']='新增';
        $result['order']['title']='订单';
        $result['consume']['title']='消费';
        $result['balance']['title']='储值';
        return $result;
    }

    public function getCommoditiesData($year,$month){
        if ((int) $month > 0) {
            $begintime = strtotime($year.'/'.$month.'/1 00:00:00');
            $endtime = strtotime('+1 month', $begintime);
        } elseif ($month == 'all') {
            $begintime = strtotime($year.'/1/1 00:00:00');
            $endtime = strtotime('+1 year', $begintime);
        }
        $saleTop=OrderDetail::with('CommoditySpecification')->select(DB::raw("commodity_specification_id,sum(amount) as sum,sum(total_price) as total_price"))->where('created_at','>=',date('Y-m-d H:i:s', $begintime))->where('created_at','<',date('Y-m-d H:i:s', $endtime))->groupBy('commodity_specification_id')->get();
        $commoditiesData=Commodity::get();
        foreach ($commoditiesData as $key => $val) {
            foreach ($saleTop as $k => $v) {
                if($val['id']==$v->CommoditySpecification->commodity_id){
                    $val['sum']=$v['sum'];
                    $val['total_price']=$v['total_price'];
                }
            }
        }
        return $commoditiesData;
    }

    public function getCommodityData($dateList,$id){
        $commoditySpecification=CommoditySpecification::with("commodity")->where('commodity_id',$id)->first();
        foreach ($dateList as $key=>$date) {
            $saleCommodity=OrderDetail::select(DB::raw("sum(amount) as sum,sum(total_price) as total_price"))
                ->where('created_at', '>=', date('Y-m-d H:i:s', $date['begintime']))
                ->where('created_at', '<', date('Y-m-d H:i:s', $date['endtime']))->groupBy('commodity_specification_id')
                ->where(function($query) use ($commoditySpecification) {
                    $query->where('commodity_specification_id',$commoditySpecification->id);
                })->get()->toArray();
            if($saleCommodity){
                $data['data'][$key]=$saleCommodity[0]['sum'];
                $saleCommodity['total_price']=$saleCommodity[0]['total_price'];
            }else{
                $data['data'][$key]=0;
            }
            $saleData[$key]=$saleCommodity;
            $saleData[$key]['catearr']=$date['time'];
            $data['catearr'][] = $date['catearr'];
        }
        $data['table'] = [
                    ['name' => '销量', 'data' => $data['data']],
                ];
        $data['result']=$saleData;
        $data['commodity']=$commoditySpecification->commodity;
        return $data;
    }

    private function getDays($year, $month)
    {
        $catearr = array();
        if ((int) $month > 0) {
            $begintime = strtotime($year.'/'.$month.'/1 00:00:00');
            $endtime = strtotime('+1 month', $begintime);
            $day = ($endtime - $begintime) / (3600 * 24);
            for ($i = 1; $i <= $day; ++$i) {
                $arr = [
                    'val' => $i,
                    'begintime' => $begintime,
                ];
                $begintime = strtotime('+1 day', $begintime);
                $arr['endtime'] = $begintime - 1;
                $arr['time'] = date('Y年m月d日', $arr['begintime']);
                $arr['catearr'] = $arr['val'];
                $catearr[] = $arr;
            }
        } elseif ($month == 'all') {
            for ($i = 1; $i <= 12; ++$i) {
                $arr = [
                    'val' => $i,
                    'begintime' => strtotime($year.'/'.$i.'/1 00:00:00'),
                ];
                $endtime = strtotime('+1 month', $arr['begintime']) - 1;
                $arr['endtime'] = $endtime;
                $arr['time'] = date('Y年m月', $arr['begintime']);
                $arr['catearr'] = date('m月', $arr['begintime']);
                $catearr[] = $arr;
            }
        }

        return $catearr;
    }

}
