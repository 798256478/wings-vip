<?php

namespace App\Http\Controllers\wechat;

use App\Http\Controllers\Controller;

use Session;
use Request;
use App;

use App\Services\OrderService;

class OrderNoticeController extends Controller
{

	public function noticeListen()
	{
        $wechat = App::make('WechatApplication');

        $response = $wechat->payment->handleNotify(function($notify, $successful){
            
            $setting = App::make('SettingService');
            
            try{
                $orderService = new OrderService();
                // 使用通知里的 "商户订单号" 去自己的数据库找到订单
                $order = $orderService->getOrderByNumber($notify->out_trade_no);
                if (!$order) { // 如果订单不存在
                    return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
                }

                // 如果订单存在,检查订单是否已经更新过支付状态
                if ($order->state != 'NOT_PAY') { 
                    return true; // 已经支付成功了就不再更新了
                }

                // 用户是否支付成功
                if ($successful) {
                    $order = $orderService->orderPayment($order->id);
                } else { 
                    // 用户支付失败
                }

                $setting->set('notice', '3x'. $order);
                return true; // 返回处理完成
            }
            catch (\Exception $e) {
                $setting->set('notice', 'xx'.$e->getMessage());
            }
            
        });
        return $response;// Laravel 里请使用：return $response;
	}

}