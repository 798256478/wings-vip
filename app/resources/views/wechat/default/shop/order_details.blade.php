@extends('wechat.default.master')

@section('title', '订单详情')

@push('links')
<link href="/common/css/wechat/default/order.css" rel="stylesheet">
<link href="/common/css/wechat/default/order_details.css" rel="stylesheet">

<style type="text/css">
	.cart-bar .order-submit {
		background-color: {{ $theme->colors['BUTTON1'] or '#0092DB' }};
	}

	.price {
		color: #FF9933;
	}

	.bonus-require {
		color: #339900;
	}
</style>
@endpush

@section('content')
	<div class="container">
		{{--进度--}}
		<div class="group clearfix state">
			<div class="pay  col-xs-3 col-sm-3">
				<div class="state-title">订单支付</div>
				@if($order['state']=='PAY_SUCCESS'||$order['state']=='FINISH'||$order['state']=='DELIVERED')
					<div class="state-icon yes"><i class="fa fa-dot-circle-o"></i></div>
				@else
					<div class="state-icon no"><i class="fa fa-circle-o"></i></div>
				@endif
			</div>
			<div class="pay-shipping col-xs-1 col-sm-1">
				@if($order['state']=='DELIVERED'||$order['state']=='FINISH')
					<span class="connect yes">..................................................</span>
				@else
					<span class="connect no">...................................................</span>
				@endif
			</div>
			<div class="shipping  col-xs-3 col-sm-3 ">
				<div class="state-title">订单发货</div>
				@if($order['state']=='DELIVERED'||$order['state']=='FINISH')
					<div class="state-icon yes"><i class="fa fa-dot-circle-o"></i></div>
				@else
					<div class="state-icon no"><i class="fa fa-circle-o"></i></div>
				@endif
			</div>
			<div class="shipping-complete col-xs-1 col-sm-1">
				@if($order['state']=='FINISH')
					<span class="connect yes">...................................................</span>
				@else
					<span class="connect no">.....................................................</span>
				@endif
			</div>
			<div class="complete  col-xs-3 col-sm-3 ">
				<div class="state-title">订单完成</div>
				@if($order['state']=='FINISH')
					<div class="state-icon yes"><i class="fa fa-dot-circle-o"></i></div>
				@else
					<div class="state-icon no"><i class="fa fa-circle-o"></i></div>
				@endif
			</div>
		</div>
		{{--收货地址--}}
		<div class="group clearfix delivery">
			@if($order['is_need_delivery'])
				<div class="address-one clearfix">
					<span class="address-key">收货地址:</span>
					<span class="address-val">{{$order['address']}}</span>
				</div>
				<div class="address-one clearfix">
					<span class="address-key">物流信息:</span>
				<span class="address-val">
					@if($order['express_type']=='SELF')
						自己配送
					@elseif($order['express_type']=='EXPRESS')
						{{$order['express_company']}}&nbsp;&nbsp;&nbsp;{{$order['express_code']}}
					@else
						暂无
					@endif
				</span>
				</div>
			@else
				<span class="title ">配送:</span>
				<span class="text ">本订单商品均为无需配送的非实体商品。其中优惠券、积分、服务次卡等
				虚拟产品可在订单完成后至会员中心查看、使用。</span>
			@endif
		</div>
		{{--优惠券--}}
		<div class="group ticket ">
			<div class="ticket-cont clearfix">
				<span class="ticket-key">优惠券</span>
				<span class="ticket-val">{{$order->ticket?$order->ticket->ticketTemplate->title : '未使用'}}</span>
			</div>
		</div>
		{{--支付方式--}}
		@if(count($order['orderPayments'])>0)
			<div class="group pay-group clearfix">
				@foreach($order['orderPayments'] as $key=>$val)
					@if($val['type']==='BALANCE')
						<div class="title">
							<h4>会员余额支付</h4>
							<small></small>
						</div>
						<div class="price">
							<i class="fa fa-cny"></i>
							<span class="value">{{$val['amount']}}</span>
						</div>
					@elseif($val['type']==='WECHAT')
						<div class="title">
							<h4>微信在线支付</h4>
							<small></small>
						</div>
						<div class="price">
							<i class="fa fa-cny"></i>
							<span class="value">{{$val['amount']}}</span>
						</div>
					@endif
				@endforeach
			</div>
		@endif
		@if($order['bonus_pay_amount']>0 || $bonusPay)
			<div class="group pay-group bonus-group">

				<div class="title">
					<h4>会员积分消费</h4>
					<small>(订单支付后自动扣除)</small>
				</div>
				<div class="bonus-require">
					<i class="fa fa-btc"></i>
					<span class="value">{{$order['bonus_pay_amount'] + ($bonusPay ? $bonusPay['use_bonus'] : 0)}}</span>
					@if($bonusPay)
						<small class="remark">(其中
							<span class="bonus-require">{{$bonusPay['use_bonus']}}</span>积分抵扣
							<span class="price">{{$bonusPay['amount']}}</span>
							元)
						</small>
					@endif
				</div>
			</div>
		@endif
		{{--商品详情--}}
		<div class="group commodity-list">
			@foreach($order->orderDetails as $detail)
				<div class="@if($detail->commoditySpecificationHistory->is_suite)
						suite
						@else
						not-suite
						@endif">
					<div class="order-detail ">
						<img src="{{$detail->commoditySpecificationHistory->commodityHistory->image[0] or '/common/imgs/noimg.png'}}">
						<div class="caption">
							<h4 class="name">
								{{$detail->commoditySpecificationHistory->commodityHistory->name}}
								<span class="refund">{{$detail->is_refund && count($detail->refund)>0
								?'('.$refundState[$detail->refund[count($detail->refund)-1]->state].')':''}}</span>
							</h4>
							<div class="specification">
								{{$detail->commoditySpecificationHistory->name or ''}}
							</div>
							<div>
								@if($detail->unit_price > 0)
									<span class="price">
											<i class="fa fa-cny" aria-hidden="true"></i>
											<span class="value">{{$detail->unit_price}}</span>
										</span>
								@endif
								@if($detail->unit_price > 0 && $detail->unit_bonus_require > 0)
									<span class="price-and-bonus">
											+
										</span>
								@endif

								@if($detail->unit_bonus_require > 0)
									<span class="bonus-require">
											<i class="fa fa-btc" aria-hidden="true"></i>
											<span class="value">{{$detail->unit_bonus_require}}</span>
										</span>
								@endif
							</div>
						</div>
						<div class="operation">
							<div class="quantity">×{{$detail['amount']}}</div>
							<div class="refund">
								@if(!$detail['is_refund'])
									@if($order['state']=="PAY_SUCCESS")
										<a href="/wechat/order/refund/new/{{$order->id}}/{{$detail->id}}">退款</a>
									@elseif($order['state']=="DELIVERED"||$order['state']=="FINISH")
										<a href="/wechat/order/refund/new/{{$order->id}}/{{$detail->id}}">退款/退货</a>
									@endif
								@endif
							</div>
						</div>
					</div>
					@if($detail->commoditySpecificationHistory->is_suite)
						<div class="suite-detail" style="display: none">
							@foreach($detail->commoditySpecificationHistory->suiteChildHistories as $suiteDetail)
								<div class="order-detail">
									<div class="connecting-line">
										<div>
											<div></div>
										</div>
									</div>
									<img src="{{$suiteDetail->commodityHistory->image[0] or '/common/imgs/noimg.png'}}">

									<div class="caption">
										<h4 class="name">{{$suiteDetail->commodityHistory->name}}</h4>

										<div class="specification">
											{{$suiteDetail->name or ''}}
										</div>
										<div>
											@if($suiteDetail->price > 0)
												<span class="price">
													<i class="fa fa-cny" aria-hidden="true"></i>
													<span class="value">{{$suiteDetail->price}}</span>
												</span>
											@endif
											@if($suiteDetail->price > 0 && $suiteDetail->bonus_require > 0)
												<span class="price-and-bonus">
													+
												</span>
											@endif

											@if($suiteDetail->bonus_require > 0)
												<span class="bonus-require">
													<i class="fa fa-btc" aria-hidden="true"></i>
													<span class="value">{{$suiteDetail->bonus_require}}</span>
												</span>
											@endif
										</div>
									</div>
									<div class="quantity">
										×&nbsp;{{$suiteDetail->pivot->count * $detail->amount}}
									</div>
								</div>
							@endforeach
						</div>
					@endif
				</div>
			@endforeach
		</div>
		<div class="cart-bar">
			<i class="menu-icon fa fa-bars" aria-hidden="true" onclick="onMenuClick()"></i>
			<span class="split">|</span>
		    <span class="cart-click-zone">
		        <i class="cart-icon fa fa-shopping-cart" aria-hidden="true"></i>
			    @if($order['money_pay_amount']>0)
				    <span class="total-price">
			            <i class="fa fa-cny" aria-hidden="true"></i>
			            <span class="value">{{$order['money_pay_amount']}}</span>
			        </span>
			    @else
				    <span class="total-price null-value">
		            <i class="fa fa-cny" aria-hidden="true"></i>
		            <span class="value">0.00</span>
		            </span>
			    @endif
			    {{--	        @if($order['money_pay_amount']>0 && $order['bonus_pay_amount']>0)--}}
			    <span class="total-price-and-bonus">
			            +
			        </span>
			    {{--@endif--}}
			    @if($order['bonus_pay_amount']>0)
				    <span class="total-bonus-require">
			            <i class="fa fa-btc" aria-hidden="true"></i>
			            <span class="value">{{$order['bonus_pay_amount']}}</span>
			        </span>
			    @else
				    <span class="total-bonus-require null-value">
			            <i class="fa fa-btc" aria-hidden="true"></i>
			            <span class="value">0</span>
			        </span>
			    @endif
		    </span>
			@if($order['state']=='NOT_PAY')
				<div class="order-submit">
					付款
				</div>
			@endif

			<div class="shop-menu" style="display:none">
				@foreach ($shops as $item)
					<div class="section">
						<a href="/wechat/mall/shop/{{$item->id}}">{{$item->title}}</a>
					</div>
				@endforeach
				<div class="split"></div>
				<div class="section">
					<a href="/wechat/order">我的订单</a>
				</div>
				<div class="section">
					<a href="/wechat">会员中心</a>
				</div>
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	@parent
	<script type="text/javascript">
		function onMenuClick() {
			var $menu = $(".shop-menu");
			if ($menu.is(':hidden'))
				$menu.show();
			else
				$menu.hide();
		}
		$(function () {
			$(".group .text").width(($(".delivery").width() - $(".delivery .title").width() - 6) + 'px');
			$(".total-pay .pay-cont-right").height($(".total-pay .pay-cont-left").height());
			$(".total-pay .pay-cont-right").css('line-height', $(".total-pay .pay-cont-right").height() + 'px');
			$(".group .oneShop .oneShop-left").width($(".group .oneShop").width()
					- $(".group .oneShop .img-cont").width()
					- $(".group .oneShop .oneShop-right").width() - 30 + 'px');
			$(".delivery .address-val").width($(".delivery .address-one").width() - $(".delivery .address-key").width() - 7 + 'px');
			$(".order-submit").click(function () {
				@if($order['state']!='NOT_PAY')
					window.location.href = '/wechat/shopping/refund/{{$order['id']}}';
				@else
					if (confirm("确定要取消订单吗？")) {
					$.ajax({
						type: "get",
						url: "/wechat/shopping/cancelOrder/{{$order['id']}}",
						error: function (data) {
							alert(data.responseText);
							return false;
						},
						success: function (data) {
							alert('订单已取消');
							window.location.href = "/wechat/shopping/order";
						}
					});
				}
				@endif


			});

			$('.suite>.order-detail').on('click',function(){$(this).next().slideToggle(500)});
		});
	</script>
@endsection