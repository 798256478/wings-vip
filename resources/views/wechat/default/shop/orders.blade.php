@extends('wechat.default.master')

@section('title', '我的订单')

@push('links')
<style type="text/css">

body {
   background: #eee;
}

ul {
   padding-left: 0;
}

a {
   color: #000;
}

/**************************************初始化设置***********************************/
.grey {
   color: #999;
}

.money {
   font-weight: 700;
}

.fa-cny {
   margin-right: 2px;
   margin-left: -10px;
   font-size: 13px;
}

.o-error,
.refund-list,
.commodity-info .refund,
.order .button-group,
.commodity-info .commodity-refund,
.order .button-group .button,
.order-list[data-status='refunds'],
.order[data-status='PAY_SUCCESS'] .refund-commodity,
.order-list[data-status='refunds'] .refund .button-group,
.order[data-status='PAY_SUCCESS'] .button-group .refund-commodity,
.order[data-status='WAIT_DELIVER'] .button-group .refund-commodity,
.order[data-status='PAY_SUCCESS'] .order-content .refund-commodity,
.order[data-status='WAIT_DELIVER'] .order-content .refund-commodity,
.order[data-status='NOT_PAY'] .commodity-info[data-status='no-refund'] .commodity-refund,
.order[data-status='CLOSED']  .commodity-info[data-status='no-refund'] .commodity-refund {
   display: none;
}

.order[data-status='NOT_PAY'] .button-group,
.commodity-info[data-status='is-refund'] .refund,
.order[data-status='DELIVERED'] .button-group,
.commodity-info[data-status='no-refund'] .commodity-refund,
.order[data-status='PAY_SUCCESS'] .button-group.no-refund,
.order[data-status='WAIT_DELIVER'] .button-group.no-refund,
.order-list[data-status='refunds'] .refund[data-status='APPLY'] .button-group {
   display: block;
}

.order[data-status='NOT_PAY'] .button.pay,
.order[data-status='DELIVERED'] .button.confirm,
.order[data-status='PAY_SUCCESS'] .button.refund,
.order[data-status='WAIT_DELIVER'] .button.refund,
.order[data-status='NOT_PAY'] .button.cancel-order,
.order[data-status='DELIVERED'] .button-group.no-refund .button.refund {
   display: inline-block;
}

.order-list .order-header .state,
.order-list[data-status='refunds'] .refund[data-status='APPLY'] .state {
   color: #ff5000;
}

.order-list .order[data-status='FINISH'] .state,
.order-list .order[data-status='CLOSED'] .state,
.order-list[data-status='refunds'] .refund .state {
   color: #333;
}

/***************************************分类导航********************************************/
.nav-tabs {
   position: fixed;
   z-index: 2;
   top: 0;
   width: 100%;
   background: #fff;
}

.nav > li {
   width: 20%;
}

.nav > li > a {
   width: 100%;
   padding: 10px 0;
   text-align: center;
}

.nav-tabs > li.active > a,
.nav-tabs > li.active > a:focus,
.nav-tabs > li.active > a:hover {
   color: #ff5000;
   border: 0;
   border-bottom: 1px solid #ff5000;
   cursor: default;
}

/***************************************无订单**********************************************/
.o-error {
   padding: 30px 0;
}

.o-error .img {
   display: -webkit-box;
   width: 120px;
   height: 120px;
   margin: 0 auto 15px;
   color: #fff;
   border-radius: 100%;
   background: #ccc;
   -webkit-box-pack: center;
   -webkit-box-align: center;
}

.o-error p {
   text-align: center;
}

.o-error .img > p {
   font-size: 36px;
   line-height: 145px;
}

/***************************************订单列表********************************************/
.order-list-group {
   margin-top: 50px;
}

.order-list li {
   display: block;
   margin-bottom: 10px;
   border-bottom: 1px solid #e7e7e7;
   background: #fff;
}

/*订单头部*/
.order-list .order-header {
   height: 38px;
   padding: 8px 15px;
   border-bottom: 1px solid #e7e7e7;
}

.order-list .order-header img {
   position: relative;
   top: -2px;
   width: 21px;
   margin-right: 5px;
}

.order-list .order-header .date {
   margin-right: 10px;
}

/***************************************订单商品************************************************/
.order-list .order-content {
   padding: 5px 0;
   background: #f5f5f5;
}

.order .commodity-info .commodity-refund {
   font-size: 12px;
}

.order-list .order-content .commodity-info {
   position: relative;
   z-index: 1;
   height: 70px;
   padding: 5px 15px;
}

.order-list .order-content .commodity-info img {
   width: 60px;
   height: 60px;
}

.order-list .order-content .commodity-info div.pull-left {
   width: 50%;
}

.order-list .order-content .commodity-info .pull-left div {
   display: inline-block;
   overflow: hidden;
   width: 100%;
   padding: 4px 0 7px 10px;
   white-space: nowrap;
   text-overflow: ellipsis;
}

.order-list .order-content .commodity-info .pull-left .commodity-scale {
   margin-top: 1px;
   margin-left: 10px;
   font-size: 13px;
}

.order-list .order-content .commodity-info .pull-right .refund {
   position: relative;
   color: orange;
   font-size: 12px;
}

.order-list .order-content .commodity-info .pull-right {
   width: 30%;
   margin-top: 4px;
   line-height: 18px;
   text-align: right;
}

/***************************************套装子商品**********************************************/
.order-list .order-content .suite_children .commodity-info{
   height: 51px;
   padding: 5px 10px;
   margin-left: 15px;
   margin-right: 5px;
   font-size: 13px;
}

.order-list .order-content .suite_children .commodity-info img {
   position: relative;
   z-index: 1;
   width: 45px;
   height: 45px;
}

.order-list .order-content .suite_children .commodity-info .pull-left div {
   padding: 2px 0 0 10px;
}

.order-list .order-content .suite_children .commodity-info .pull-left .commodity-scale {
   font-size: 12px;
}

.order-list .order-content .suite_children {
   position: relative;
   display: none;
   padding-bottom: 10px;
}

.order-list .order-content .suite_children .money .fa-cny{
   font-size: 12px;
}

.order-list .order-content .suite_children .tree {
    position: absolute;
    z-index: 0;
    top: -30px;
    left: 15px;
    width: 2px;
    height: 100%;
    border-radius: 0 0 0 4px;
    background: #ccc;
}

.order-list .order-content .suite_children .tree-branch {
    position: relative;
    top: 25px;
    left: -10px;
    width: 15px;
    height: 1px;
    background: #ccc;
}

/***************************************订单商品统计**********************************************/
.order-list .order-total {
   height: 40px;
   padding: 10px 15px 10px;
   text-align: right;
}

.order-list .order-total span {
   margin-left: 15px;
}

/****************************************事件按钮**************************************************/
.button-group {
   padding: 8px 10px;
   border-top: 1px solid #e7e7e7;
   text-align: right;
}

.order-list .button {
   display: inline-block;
   height: 32px;
   margin: 0 5px;
   padding: 0 10px;
   color: #fff;
   border: 1px solid #5cb85c;
   border-radius: 2px;
   background: #5cb85c;
   line-height: 32px;
   text-align: center;
}

.order-list .button.pay,
.order-list .button.cancel-refund {
   color: #ff5000;
   border: 1px solid #ff5000;
   background: #fff;
}

.order-list .button.refund,
.order-list .button.cancel-order {
   color: #000;
   border: 1px solid #000;
   background: #fff;
}

.no-more {
   margin-bottom: 8px;
   text-align: center;
}

</style>
@endpush
@section('content')
<div>
   <ul id="myTab" class="nav nav-tabs">
      <li class="active" data-status='ALL'><a onclick=" changePage(this)">全部</a></li>
      <li data-status='NOT_PAY'><a onclick=" changePage(this)">待付款</a></li>
      <li data-status='WAIT_DELIVER'><a onclick="changePage(this)">待发货</a></li>
      <li data-status='DELIVERED'><a onclick="changePage(this)">待收货</a></li>
      <li data-status='REFUND'><a onclick="changePage(this)">退款</a></li>
   </ul>
   <div class="order-list-group">

      <!--     订单列表      -->
      <ul class="order-list" data-status='orders'>
         @foreach($orders['orders'] as $k=>$v)
            <li class="order" data-status="{{$v['state'] == 'PAY_SUCCESS' && $v['is_need_delivery']?'WAIT_DELIVER':$v['state']}}" id="order{{$v['id']}}">
               <div class="order-header">
                  <img src="/common/imgs/{{$v['channel']}}.png">
                  <span class="date">{{date("Y/m/d",strtotime($v['created_at']))}}</span>
                  <span class="way">{{explode(':',$v['body'])[0]}}</span>
                  <span class="state pull-right">{{$state[($v['state'] == 'PAY_SUCCESS' && $v['is_need_delivery']?'WAIT_DELIVER':$v['state'])]}}</span>
               </div>
               <div class="order-content">
                  <?php $amount =0; $refund = false;?>
                  @foreach($v['order_details'] as $val)
                     <?php $amount += $val['amount']; $refund?$refund = true:$refund = $val['is_refund'];?>

                     <!--       订单商品        -->
                     <div class="commodity-info" 
                          onclick="@if($val['commodity_specification_history']['is_suite']) showChildren(this) @else orderDetails({{$val['order_id']}}) @endif" 
                          data-status="{{$val['is_refund']?'is-refund':'no-refund'}}" id="orderDetail{{$val['id']}}"
                     >
                        <img src="{{$val['commodity_specification_history']['commodity_history']['image'][0]?$val['commodity_specification_history']['commodity_history']['image'][0]:'/common/imgs/noimg.png'}}" class="pull-left">
                        <div class="pull-left">
                           <div class="commodity-name">{{$val['commodity_specification_history']['commodity_history']['name']}}</div>
                           @if($val['commodity_specification_history']['name'])
                           <span class="commodity-scale grey">规格：{{$val['commodity_specification_history']['name']}}</span>
                           @endif
                        </div>
                        <div class="pull-right right">
                           <div class="money"><i class="fa fa-cny"></i>{{$val['commodity_specification_history']['price']}}</div>
                           <div class="commodity-num grey">x{{$val['amount']}}</div>
                           <div class="commodity-refund">
                              <a href="/wechat/order/refund/new/{{$v['id']}}/{{$val['id']}}"><span class="refund-money">退款</span><span class="refund-commodity">/退货</span></a>
                           </div>
                           @if($v['refund'])
                              <span class="refund">
                                 @if($v['refund'][0]['type'] == "ORDER")
                                    {{$v['refund']&&$v['refund'][0]['state']!='CLOSE'?$refundState[$v['refund'][0]['state']]:''}}
                                 @else
                                    {{$val['refund']&&$val['refund'][0]['state']!='CLOSE'?$refundState[$val['refund'][0]['state']]:''}}
                                 @endif
                              </span>
                           @endif
                        </div> 
                     </div>

                     <!--        套装子商品      -->
                     @if($val['commodity_specification_history']['is_suite'])
                        <?php $amount -= $val['amount'];?>
                        <div class="suite_children">
                           <div class="tree"></div>
                           @foreach ($val['commodity_specification_history']['suite_child_histories'] as $suiteChild)
                              <?php $amount += ($suiteChild['pivot']['count'] * $val['amount']);?>
                              <div class="commodity-info" 
                                   onclick="orderDetails({{$val['order_id']}})" 
                                   data-status="{{$val['is_refund']?'is-refund':'no-refund'}}" 
                                   id="orderDetail{{$val['id']}}"
                              >
                                 <div class="tree-branch"></div>
                                 <img src="{{$suiteChild['commodity_history']['image'][0]?$suiteChild['commodity_history']['image'][0]:'/common/imgs/noimg.png'}}" class="pull-left">
                                 <div class="pull-left">
                                    <div class="commodity-name">{{$suiteChild['commodity_history']['name']}}</div>
                                    @if($suiteChild['name'])
                                    <span class="commodity-scale grey">规格：{{$suiteChild['name']}}</span>
                                    @endif
                                 </div>
                                 <div class="pull-right right">
                                    <div class="money"><i class="fa fa-cny"></i>{{$suiteChild['price']}}</div>
                                    <div class="commodity-num grey">x{{$suiteChild['pivot']['count']}}</div>
                                 </div>
                              </div>
                           @endforeach
                        </div>
                     @endif

                  @endforeach
               </div>
               <div class="order-total">
                  <span>共<label class="amount">{{$amount == 0?$v['suit_amount']:$amount}}</label>件商品</span>
                  <span>合计:<span class="money"><i class="fa fa-cny"></i>{{$v['total_fee']}}</span></span>
               </div>
               <div class="button-group {{$refund?'is_refund':'no-refund'}}">
                  <span class="button refund">
                     <a href="/wechat/order/refund/new/{{$v['id']}}">
                        <span class="refund-money">退款</span>
                        <span class="refund-commodity">/退货</span>
                     </a>
                  </span>
                  <span class="button pay">付款</span>
                  <span class="button cancel-order" onclick="cancelOrder({{$v['id']}})">取消订单</span>
                  <span class="button confirm" onclick="confirmReceipt({{$v['id']}})">确认收货</span>
               </div>      
            </li>
         @endforeach
      </ul>
      
      <!--        退款列表        -->
      <ul class="order-list" data-status="refunds">
         @foreach($orders['refunds'] as $k=>$v)           
            <li class="refund" data-status="{{$v['state']}}" id="refund{{$v['id']}}">
               <div class="order-header">
                  <div class="pull-left">
                     <span class="date">{{date("Y/m/d",strtotime($v['created_at']))}}</span>
                  </div>
                  <div class="pull-right">
                     <span class="state">{{$refundState[$v['state']]}}</span>
                  </div>
               </div>
               <div class="order-content">
                  <?php $amount = 0;?>
                  @foreach($v['order']['order_details'] as $val)
                     <?php $amount += $val['amount'];?>                     
                     <div class="commodity-info" onclick="
                        @if($val['commodity_specification_history']['is_suite']) 
                           showChildren(this) 
                        @else 
                           orderDetails({{$val['order_id']}}) 
                        @endif
                     ">
                        <img src="{{$val['commodity_specification_history']['commodity_history']['image'][0]?$val['commodity_specification_history']['commodity_history']['image'][0]:'/common/imgs/noimg.png'}}" class="pull-left">
                        <div class="pull-left">
                           <div class="commodity-name">{{$val['commodity_specification_history']['commodity_history']['name']}}</div>
                           @if($val['commodity_specification_history']['name'])
                           <span class="commodity-scale grey">规格：{{$val['commodity_specification_history']['name']}}</span>
                           @endif
                        </div>
                        <div class="pull-right right">
                           <div class="money"><i class="fa fa-cny"></i>{{$val['commodity_specification_history']['price']}}</div>
                           <div class="commodity-num grey">x{{$val['amount']}}</div>  
                        </div>
                     </div>
                     @if($val['commodity_specification_history']['is_suite'])
                        <?php $amount -= $val['amount'];?>
                        <div class="suite_children">
                           <div class="tree"></div>
                           @foreach ($val['commodity_specification_history']['suite_child_histories'] as $suiteChild)
                              <?php $amount += ($suiteChild['pivot']['count'] * $val['amount']);?>
                              <div class="commodity-info" onclick="orderDetails({{$v['order_id']}})">
                                 <div class="tree-branch"></div>
                                 <img src="{{$suiteChild['commodity_history']['image'][0]?$suiteChild['commodity_history']['image'][0]:'/common/imgs/noimg.png'}}" class="pull-left">
                                 <div class="pull-left">
                                    <div class="commodity-name">{{$suiteChild['commodity_history']['name']}}</div>
                                    @if($suiteChild['name'])
                                    <span class="commodity-scale grey">规格：{{$suiteChild['name']}}</span>
                                    @endif
                                 </div>
                                 <div class="pull-right right">
                                    <div class="money"><i class="fa fa-cny"></i>{{$suiteChild['price']}}</div>
                                    <div class="commodity-num grey">x{{$suiteChild['pivot']['count']}}</div>  
                                 </div>
                              </div>
                           @endforeach
                        </div>
                     @endif
                   @endforeach  
               </div>
               <div class="order-total">
                  <span>共<label class="amount">{{$amount}}</label>件商品</span>
                  <span>合计:<span class="money"><i class="fa fa-cny"></i>{{$v['order']['total_fee']}}</span></span>
               </div>
               <div class="button-group">
                  <span class="button cancel-refund" onclick="cancelRefund({{$v['id']}}, {{$v['order_id']}}, '{{$v['type']}}')">取消申请</span>
               </div>
            </li>   
         @endforeach
      </ul>
      <div class="no-more grey">没有更多了</div>
      <div class="o-error"> 
         <div> 
            <div class="img">
               <p><i class="fa fa-2x fa-file-text-o"></i></p>
            </div> 
            <p class="txt">您还没有相关的订单</p>    
         </div> 
      </div>
   </div>
</div>
@endsection
@section('scripts')
   @parent
   <script>
   /**
    * 检查当前显示栏是否有订单，若有则隐藏无订单样式，否则显示无订单样式
    */
   function checkPage() {
      var n = 0;
      $(".order-list").each(function() {
         if ($(this).css('display') == 'block') {
            $(this).children('li').each(function() {
               if ($(this).css('display') == 'block') {
                  n++;
               }
            })
         }
      })
      if (n == 0) {
         $('.o-error').show();
         $('.no-more').hide();
      } else {
         $('.o-error').hide();
         $('.no-more').show();
      }
   }
   checkPage();

   /**
    * 分类切换
    */
   function changePage(e) {
      $(e).parent().addClass('active');
      $(e).parent().siblings().removeClass('active');
      var state = $(e).parent().attr('data-status');
      if (state == 'REFUND') {
         $(".order-list[data-status='refunds']").show();
         $(".order-list[data-status='orders']").hide();
      } else {
         $(".order-list[data-status='refunds']").hide();
         $(".order-list[data-status='orders']").show();
         if (state == 'ALL') {
            $('.order').show();
         } else {
            if ($('.order[data-status=' + state + ']').length > 0) {
                $('.order[data-status=' + state + ']').siblings('li').hide();
                $('.order[data-status=' + state + ']').show();
            } else {
                $('.order').hide();
            }
         }
      }
      checkPage();
   }

   /**
    * 控制套装子商品显隐
    * @Author   zhaowenbin
    * @DateTime 2016-10-26T17:33:18+0800
    * @param    e
    * @return   null
    */
   function showChildren(e) {
      if($(e).next(".suite_children").css('display') == "block"){
         $(e).next(".suite_children").slideUp();
      } else {
         $(e).next(".suite_children").slideDown();
      }
   }

   /**
    * 阻止<a>标签事件冒泡
    * @Author   zhaowenbin
    * @DateTime 2016-10-27T08:55:58+0800
    * @param    e 
    * @return   null
    */
   $('a').click(function(e){
      e.stopPropagation();
   })

   /**
    * 跳转到订单详情页
    *
    * @param id 订单id
    */
   function orderDetails(id) {
      window.location.href = "/wechat/order/" + id;
   }

   /**
    * 取消退款申请
    * @Author   zhaowenbin
    * @DateTime 2016-10-27T08:55:58+0800
    * @param int  id 退款id
    * @param int  target_id 订单详情id | 订单id
    * @param String   退款的类型，整单退或单个退
    * @return   null
    */
   function cancelRefund(id, target_id, type) {
      if (confirm("确定要取消退款申请吗？")) {
         var ajaxSetData = $.ajax({
            type: "delete",
            url: "/wechat/api/refund/" + id,
            error: function(data) {
               if (data.status == 404) {
                  alert('请求发送失败，请检查网络连接');
               } else {
                  alert('未知错误');
               }
               return false;
            },
            success: function(data) {
               $('#refund' + id).attr('data-status', 'CLOSE');
               $('#refund' + id + ' .state').html('退款申请已取消');
               if(type == 'ORDER'){
                  $('#order' + target_id + ' .commodity-info').attr('data-status', 'no-refund');
                  $('#order' + target_id + ' .button-group').removeClass('is_refund').addClass('no-refund');
               }else{
                  $('#orderDetail' + target_id).attr('data-status', 'no-refund');
                  $('#orderDetail' + target_id).parent().siblings('.button-group').removeClass('is_refund').addClass('no-refund');
               }
               alert('退款申请已撤销');
            },
            complete: function(XMLHttpRequest, status) {
               if (status == 'timeout') {
                  ajaxSetData.abort();
                  alert("网络连接超时失败");
               }
            }
         });
      }
   };

   /**
    * 确认订单
    * @Author   zhaowenbin
    * @DateTime 2016-10-27T08:57:58+0800
    * @param    int       id 订单id
    * @return   null
    */
   function confirmReceipt(id) {
      if (confirm("是否确认收货？")) {
         var ajaxSetData = $.ajax({
            type: "get",
            url: "/wechat/shopping/confirmReceipt/" + id,
            error: function(data) {
               if (data.status == 404) {
                  alert('请求发送失败，请检查网络连接');
               } else {
                  alert('未知错误');
               }
               return false;
            },
            success: function(data) {
               $('#order' + id).attr('data-status', 'FINISH');
               if ($('#myTab .active').attr('data-status') != "ALL") {
                  $('#order' + id).css('display', 'none');
               }
               $('#order' + id + ' .state').html('已完成');
               checkPage();
               alert('已确认收货');
            },
            complete: function(XMLHttpRequest, status) {
               if (status == 'timeout') {
                  ajaxSetData.abort();
                  alert("网络连接超时失败");
               }
            }
         });
      }
   };

   /**
    * 取消订单
    * @Author   zhaowenbin
    * @DateTime 2016-10-27T08:58:48+0800
    * @param    int    id 订单id
    * @return   null  
    */
   function cancelOrder(id) {
      if (confirm("确定要取消订单吗？")) {
         var ajaxSetData = $.ajax({
            type: "delete",
            url: "/wechat/api/" + id,
            error: function(data) {
               if (data.status == 404) {
                  alert('请求发送失败，请检查网络连接');
               } else {
                  alert('未知错误');
               }
               return false;
            },
            success: function(data) {
               $('#order' + id).attr('data-status', 'CLOSED');
               if ($('#myTab .active').attr('data-status') != "ALL") {
                  $('#order' + id).css('display', 'none');
               }
               $('#order' + id + ' .state').html('订单已取消');
               checkPage();
               alert('订单已取消');
            },
            complete: function(XMLHttpRequest, status) {
               if (status == 'timeout') {
                  ajaxSetData.abort();
                  alert("网络连接超时失败");
               }
            }
         });
      }
   }
   </script>
@endsection