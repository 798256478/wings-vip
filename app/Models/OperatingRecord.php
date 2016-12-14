<?php 

namespace App\Models;

use Jenssegers\Mongodb\Model as Eloquent;

class OperatingRecord extends Eloquent {

    protected $collection = 'operating_records';
	
	protected $connection = 'mongodb';
    
    public function getDisplayTimeAttribute(){
        return convart_timestamp_to_display($this->attributes['create_time']);
    }
    protected $appends = ['display_time'];
    /**
	 *写入日志
	 *@param array $log 操作日志内容
	 *$log = [
	 *	'operator' => [
	 *		'display_name' => '张三',
	 *		'roles' => 'cashier'
	 *	],-------------------------------------------------当前操作人
     *  'cards' => [1,2,3],--------------------------------相关的card的id
     *  'channel'=>'shop',---------------------------------shop wechat DELIVERY可选
	 *  'show_to_menber' => TRUE,--------------------------是否显示
	 *	'create_time' => time(),---------------------------创建时间
     *	'action' => '储值',--------------------------------行为标题
     *  'data'=>'[]'，------------------------------------ 数据
 	 *	'summary' => '',-----------------------------------日志总结
	 *	'minimal' => '',-----------------------------------日志简写
     *  'type'=>'event',-----------------------------------类别，可选项为'EVENT','JOB','MASS'
     *  'event_type'=>'BALANCE'----------------------------事件类别'BALANCE'，'GOODS','CONSUME','CARD_CREATE','CODE_REDEEMED','TicketVerified'等
     *  'event_class'=>'app/events/orderCompleted',--------事件类别,可空
     *  'card'=>[],----------------------------------------card表中所有字段,可空
     *  'changes'=>[
            'bonus'=>0,-------------------------------------积分,可空
            'consume'=>0,-----------------------------------消费,可空
            'balance'=>0,-----------------------------------余额,可空
            'payCommission'=>0,-----------------------------佣金,可空
            'level'=>0,-------------------------------------等级,可空
            'ticket'=>0,------------------------------------优惠券,可空
            'property'=>0-----------------------------------服务,可空
     ]
	 *];
	 */
}