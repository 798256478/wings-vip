@extends('wechat.default.master')

@section('title', '确认订单')

@push('links')
<link href="/common/css/wechat/default/order.css" rel="stylesheet">
<style type="text/css">
	.price {
		color: #FF9933;
	}

	.bonus-require {
		color: #339900;
	}

	.order-submit {
		background-color: {{ $theme->colors['BUTTON1'] or '#0092DB' }};
	}
</style>
@endpush

@section('content')
	<div class="container">
		{{--配送--}}
		<div class="group deliver-group">
			<div class="title">
				<i class="fa fa-map-marker title-icon"></i>
				<h4>配送地址:</h4>
				<small></small>
			</div>
			<p class="text ">
				@if($order->is_need_delivery)
					@if($order->address)
						<p>{{$order->address}}</p>
					@else
						<p>未找到默认收获地址，请点击设置。</p>
					@endif
				@else
					<p>本订单商品均为无需配送的非实体商品。其中优惠券、服务次卡等
						可在订单完成后至会员中心查看、使用。</p>
				@endif
			</p>
		</div>
		{{--商品列表--}}
		{{--@if($suit)--}}
		{{--<div class="group suit ">--}}
		{{--</div>--}}
		{{--@else--}}
		<div class="group commodity-list">
			@foreach($orderDetails as $detail)
				<div class="@if($detail->commoditySpecificationHistory->is_suite)
						suite
						@else
						not-suite
						@endif">
					<div class="order-detail ">
						<img src="{{$detail->commoditySpecificationHistory->commodityHistory->image[0] or '/common/imgs/noimg.png'}}">

						<div class="caption">
							<h4 class="name">{{$detail->commoditySpecificationHistory->commodityHistory->name}}</h4>

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
						<div class="quantity">
							×&nbsp;{{$detail->amount}}
						</div>
					</div>
					@if($detail->commoditySpecificationHistory->is_suite)
						<div class="suite-detail" style="display: none">
							@foreach($detail->commoditySpecificationHistory->suiteChildHistories as $suiteDetail)
								{{--<div class="suit-commodity">--}}
								{{--<span class="name">{{$suitDetail->commodityHistory->name.$suitDetail->name}}</span>--}}
								{{--<span class="quantity">×&nbsp;{{$suitDetail->pivot->count * $detail->amount}}</span>--}}
								{{--</div>--}}
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
		{{--<div class="not-suit">--}}
		{{--	<div class="order-detail">--}}
		{{--		<img src="{{$detail->commoditySpecificationHistory->commodityHistory->image[0] or '/common/imgs/noimg.png'}}">--}}
		{{--		<div class="caption">--}}
		{{--			<h4 class="name">{{$detail->commoditySpecificationHistory->commodityHistory->name}}</h4>--}}
		{{--			<div class="specification">--}}
		{{--				{{$detail->commoditySpecificationHistory->name or ''}}--}}
		{{--			</div>--}}
		{{--			<div>--}}
		{{--				@if($detail->unit_price > 0)--}}
		{{--					<span class="price">--}}
		{{--						<i class="fa fa-cny" aria-hidden="true"></i>--}}
		{{--						<span class="value">{{$detail->unit_price}}</span>--}}
		{{--					</span>--}}
		{{--				@endif--}}
		{{--				@if($detail->unit_price > 0 && $detail->unit_bonus_require > 0)--}}
		{{--					<span class="price-and-bonus">--}}
		{{--						+--}}
		{{--					</span>--}}
		{{--				@endif--}}
		{{----}}
		{{--				@if($detail->unit_bonus_require > 0)--}}
		{{--					<span class="bonus-require">--}}
		{{--					<i class="fa fa-btc" aria-hidden="true"></i>--}}
		{{--					<span class="value">{{$detail->unit_bonus_require}}</span>--}}
		{{--					</span>--}}
		{{--				@endif--}}
		{{--			</div>--}}
		{{--		</div>--}}
		{{--		<div class="quantity">--}}
		{{--			×&nbsp;{{$detail->amount}}--}}
		{{--		</div>--}}
		{{--	</div>--}}
		{{--</div>--}}
		{{--@endif--}}
		{{--@endif--}}
		@if ($order->total_fee != 0)
			{{--优惠券--}}
			<div class="group ticket-group ">
				<div class="title">
					<i class="fa fa-ticket title-icon"></i>
					<h4>优惠券</h4>
					<small></small>
				</div>
				<i class="fa fa-angle-down right-icon"></i>
				<span class="sub-title">
				<span class="value"></span>
				</span>
			</div>
			<div class="row">
				<div class="tickets-pack" style="display:none;">
					@foreach ($availableTickets as $ticket)
						<details class="ticket" data-ticket-id={{$ticket->id}}>
							<summary>
								<div class="left">
									<small class="type">{{ $ticket->ticketTemplate['card_type'] }}</small>
									<span class="icon fa fa-check-circle {{ $ticket->ticketTemplate['color'] }}Front
									{{$ticket->id == $bestTicketId ? 'checked' : '' }}"></span>
									<span class="title {{ $ticket->ticketTemplate['color'] }}Front">
										{{ $ticket->ticketTemplate['title'] }}
									</span>
								</div>
								<div class="right {{ $ticket->ticketTemplate['color'] }}">
									<span class="sub-title">{{ $ticket->ticketTemplate['sub_title'] or '无限制' }}</span>
									@if ( time() < strtotime($ticket->ticketTemplate['begin_timestamp']) )
										<small class="date-info">{{ $ticket->ticketTemplate['begin_display_time'] }}</small>
										<small class="date-info">/</small>
										<small class="date-info">{{ $ticket->ticketTemplate['end_display_time'] }}</small>
									@else
										<small class="date-info">{{ $ticket->ticketTemplate['end_display_time'] }}</small>
										<small class="date-info">到期</small>
									@endif

									<div class="point p1"></div>
									<div class="point p2"></div>
									<div class="point p3"></div>
									<div class="point p4"></div>
									<div class="point p5"></div>
									<div class="point p6"></div>
								</div>
							</summary>
						</details>
					@endforeach
				</div>
			</div>

			{{--余额支付--}}
			<div class="group pay-group ">
				<div class="balance-group">
					<div class="title">
						<h4>会员余额支付</h4>
						<small></small>
					</div>
					<div class="price">
						<i class="fa fa-cny"></i>
						<span class="value">
						@if ($card_info->balance < $order->total_fee)
								{{$card_info->balance}}
							@else
								{{$order->total_fee}}
							@endif
						</span>
					</div>
					@if ($card_info->balance === 0)
						<div class="balance-link">
							去储值
							<span class="glyphicon glyphicon-chevron-right"></span>
						</div>
					@else
						<div class="check-btn">
							@if($card_info->balance > 0)
								<span class="icon fa fa-toggle-on"></span>
							@else
								<span class="icon fa fa-toggle-off"></span>
							@endif
						</div>
					@endif
				</div>
				@if(!$bonusRule['disabled'])
					<div class="bonus-group">
						<div class="title">
							<h4>会员积分抵扣</h4>
							<small></small>
						</div>
						<div class="bonus-require">
							<i class="fa fa-btc"></i>
							<span class="value">0<small>(积分不足)</small></span>
						</div>
						<div class="check-btn">
							{{--@if($card_info->bonus-$order->bonus_require > 0)--}}
							{{--<span class="icon fa fa-toggle-on"></span>--}}
							{{--@else--}}
							<span class="icon fa fa-toggle-off"></span>
							{{--@endif--}}
						</div>
					</div>
				@endif
			</div>
		@endif

		{{--积分消费--}}
		@if ($order->bonus_require !== 0)
			<div class="group pay-group bonus">
				<div class="title">
					<h4>会员积分消费</h4>
					<small>(订单支付后自动扣除)</small>
				</div>
				<div class="bonus-require">
					<i class="fa fa-btc"></i>
					<span class="value">{{$order->bonus_require}}</span>
				</div>
			</div>
		@endif
		{{--留言--}}
		<div class="group messages-group">
			<div contenteditable="true" class="message" name="message" id="message" placeholder="如您有特别需求,可在此留言"></div>
		</div>

		{{--底栏--}}
		<div class="order-bar">
			<i class="back-icon fa fa-bars"></i>
			<span class="split">|</span>
			<span class="order-amount">
			<span>微信支付：</span>
			<span class="pay-amount">
			<i class="fa fa-cny"></i>
			<span class="value"></span>
			</span>
			</span>

			<div class="order-submit">提交订单</div>
		</div>
	</div>
@endsection

@section('scripts')
	@parent
	<script type="text/javascript">
		var order = <?php echo $order ? $order: 'null'?>;
		var availableTickets = [
			@foreach($availableTickets as $ticket)
			<?php echo $ticket?>,
			@endforeach
			];
		var ticketId = <?php echo $bestTicketId ? $bestTicketId: 'null'?>;
		var cardBalance = {{$card_info->balance}};
		var cardBonus = {{$card_info->bonus}};
		var balancePay = 0;
		var wechatPay = 0;
		var balanceFirst = true;//是否余额支付,默认使用
		var addressId = {{$addressId or 'null'}};
		var canUseTickets = {{$canUseTickets ? $canUseTickets : 'null'}};

		var bonusRule = <?php echo json_encode($bonusRule) ? json_encode($bonusRule) : 'null'?>;//积分抵扣余额规则
		var useBonusPay = false;//是否使用积分抵扣余额,默认不使用
		var bonusPay = 0;

		function tryUseTicket() {
			var totalFee = order.total_fee;
			if (canUseTickets) {
				if (availableTickets.length == 0) {
					$(".ticket-group .value").html('无可用优惠券');
					order.money_pay_amount = totalFee;
				}
				else {
					if (ticketId != null) {
						var tickets = $.grep(availableTickets, function (value) {
							return value.id == ticketId;
						});

						if (tickets.length == 1) {
							var off = 0;
							var ticket = tickets[0];
							var template = ticket.ticket_template;
							if (template.card_type == 'CASH') {
								if (template.cash_reduce_cost >= totalFee) {
									off = totalFee;
								}
								else {
									off = template.cash_reduce_cost;
								}
							}
							else if (template.card_type == 'DISCOUNT') {
								off = (100 - template.discount_discount) * totalFee / 100;
							}

							order.money_pay_amount = totalFee - off;
							$(".ticket-group .value").html('已使用一张优惠券，抵扣<span class="price">' + off + '</span>元');
						}

					}
					else {
						order.money_pay_amount = totalFee;
						$(".ticket-group .value").html('未选择优惠券');
					}
				}
			} else {
				$(".ticket-group .value").html('该订单包含不可用优惠券的商品');
				order.money_pay_amount = totalFee;
			}
			if (!bonusRule.disabled && cardBonus - order.bonus_require > 0) {
				tryUseBonus();
			}
			flushPayment();
		}

		function tryUseBonus() {
			if (useBonusPay) {
				bonusRule.max = (order.money_pay_amount * bonusRule.use > bonusRule.limit) ? bonusRule.limit : order.money_pay_amount * bonusRule.use;
				if (cardBonus - order.bonus_require < 0) {
					bonusPay = 0;
				}
				else if (bonusRule.max > cardBonus - order.bonus_require) {
					bonusPay = cardBonus - order.bonus_require;
				}
				else {
					bonusPay = bonusRule.max;
				}
				bonusPay = Math.floor(bonusPay);
				var deduction = bonusPay * bonusRule.exchange;
				deduction = deduction.toFixed(2);
				$(".bonus-group .value").html(bonusPay + '<small>(抵扣<span class="price">'
						+ deduction + '</span>元)</small>');
				order.money_pay_amount -= deduction;
			} else {
				bonusPay = 0;
				$(".bonus-group .value").html('0<small>(未使用积分支付)</small>');
			}
		}

		function flushPayment() {
			if (cardBonus < order.bonus_require) {
				var $small = $('.pay-group.bonus>.title>small');
				$small.html('（您的积分不足）');
				$small.css('color', 'red');
				$('.order-submit').addClass("disabled");
			}

			if (cardBalance != 0 && balanceFirst) {
				if (cardBalance >= order.money_pay_amount) {
					balancePay = order.money_pay_amount;
					wechatPay = 0;
				}
				else {
					balancePay = cardBalance;
					wechatPay = order.money_pay_amount - cardBalance;
				}
			}
			else {
				balancePay = 0;
				wechatPay = order.money_pay_amount;
			}
			wechatPay = Number(wechatPay).toFixed(2);
			$('.order-bar .pay-amount>.value').html(wechatPay);
			$('.balance-group .value').html(balancePay);

		}

		function onTicketGroupClick() {
			var $icon = $('.ticket-group .right-icon');
			var $tickets_pack = $('.tickets-pack');

			if ($tickets_pack.is(':hidden')) {
				$tickets_pack.show();
				$icon.removeClass('fa-angle-down');
				$icon.addClass('fa-angle-up');
			}
			else {
				$tickets_pack.hide();
				$icon.removeClass('fa-angle-up');
				$icon.addClass('fa-angle-down');
			}
		}

		function onTicketClick() {
			if ($(this).attr('data-ticket-id') == ticketId) {
				ticketId = null;
				$(this).find('.left .icon').removeClass('checked');
			}
			else {
				ticketId = $(this).attr('data-ticket-id');
				$('.tickets-pack .ticket .left .icon').removeClass('checked');
				$(this).find('.left .icon').addClass('checked');
			}
			tryUseTicket();
			onTicketGroupClick();
		}

		function onBalanceCheckBtnClick() {
			if (cardBalance > 0) {
				if (balanceFirst) {
					balanceFirst = false;
					$(this).find('.check-btn .icon').removeClass('fa-toggle-on');
					$(this).find('.check-btn .icon').addClass('fa-toggle-off');
//				$(this).removeClass('checked');
				}
				else {
					balanceFirst = true;
					$(this).find('.check-btn .icon').removeClass('fa-toggle-off');
					$(this).find('.check-btn .icon').addClass('fa-toggle-on');
//				$(this).addClass('checked');
				}
				flushPayment();
			} else {
				return;
			}
		}

		function onBonusCheckBtnClick() {
			if (cardBonus - order.bonus_require > 0) {
				if (useBonusPay) {
					useBonusPay = false;
					$(this).find('.check-btn .icon').removeClass('fa-toggle-on');
					$(this).find('.check-btn .icon').addClass('fa-toggle-off');
				} else {
					useBonusPay = true;
					$(this).find('.check-btn .icon').removeClass('fa-toggle-off');
					$(this).find('.check-btn .icon').addClass('fa-toggle-on');
				}
				tryUseTicket();
			} else {
				$(".bonus-group .value").html('0<small>(积分不足)</small>');
				return;
			}
		}

		function onOrderSubmit() {

			if ($('.order-submit').hasClass("disabled"))
				return;

			if (order.is_need_delivery && !order.address) {
				swal('请填写收货地址', '请填写地址后重新提交订单', "warning");
				return;
			}

			var data = {
				'remark': $('.messages-group .message').html(),
				'address_id': addressId,
				'ticket_id': parseInt(ticketId) ? parseInt(ticketId) : null,
				'balance_pay': balancePay,
				'wechat_pay': wechatPay,
				'bonus_pay': bonusPay
			};
			$.ajax({
				type: "POST",
				url: "/wechat/order",
				data: data,
				beforeSend: function () {
					$('.loading').show();
				},
				success: function (res) {
					$('.loading').hide();
					if (res.pay_config) {
						wx.chooseWXPay({
							timestamp: res.pay_config.timestamp,
							nonceStr: res.pay_config.nonceStr,
							package: res.pay_config.package,
							signType: res.pay_config.signType,
							paySign: res.pay_config.paySign,
							success: function (res) {
//								swal('订单支付成功', res, "success");
								window.location.href = '/wechat/order/paySuccess/' + res.order_id;
							}
						});
					}
					else if (res.state == 'PAY_SUCCESS' || res.state == 'FINISH') {
						window.location.href = '/wechat/order/paySuccess/' + res.order_id;
//						swal('订单支付成功', res, "success");
					}
					else {
						swal('订单异常，请重试', res, "warning");
					}
				},
				error: function (res) {
					$('.loading').hide();
					swal('订单异常，请重试', jQuery.parseJSON(res.responseText).message, "warning");
//					console.log(res.responseText);
				}
			});
		}

		$(function () {
			@if($order->is_need_delivery)
			$(".deliver-group").click(function () {
				window.location.href = '/wechat/address';
			});
			@endif

			$('.ticket-group').click(onTicketGroupClick);
			$('.order-submit').click(onOrderSubmit);
			$('.ticket').click(onTicketClick);
			$('.balance-group').click(onBalanceCheckBtnClick);
			$('.bonus-group').click(onBonusCheckBtnClick);

			tryUseTicket();

//			$('.suite .order-detail').on('click',function(){alert(1)});
			$('.suite>.order-detail').on('click',function(){$(this).next().slideToggle(500)});
		});
	</script>
@endsection