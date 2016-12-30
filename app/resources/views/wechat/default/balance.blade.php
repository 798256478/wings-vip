@extends('wechat.default.master')

@section('title', '会员中心')

@push('links')

<style type="text/css">
	.header-property {
		background-color: {{ $theme->colors['THEME'] or '#0092DB' }};
	}

	.header-buttons .glyphicon {
		color: {{ $theme->colors['THEME'] or '#0092DB' }};
	}

	.rule-item {
		height: 60px;
		padding-top: 10px;
		padding-bottom: 10px;
	}

	.rule-item > div {
		border-bottom: 1px solid #eee;
		padding-right: 5px;
	}

	.rule-item small {
		color: #777;
	}

	.rule-item big {
		font-size: 28px;
		color: #555;
	}

	#amount {
		width: 95px;
		font-size: 28px;
		border: none;
		height: 40px;
	}

	#amount:focus {
		border: none;
		outline: none;
	}

	.btn-pay {
		width: 80%;
		margin: 20px auto;
		display: block;
	}
</style>
@endpush

@section('content')
	<div class="container">
		<ol class="breadcrumb row">
			<li><a href="/wechat">会员卡</a></li>
			<li class="active">我的余额</li>
		</ol>

		<div>
			<button type="button" class="header-explain" data-container="body" data-toggle="popover"
			        data-placement="left" data-content="储值需到店进行，储值不可退。">
				储值说明
			</button>
		</div>

		<div class="header-property">
			<label>余额</label>
			<br/>
			<span> {{$card_info->balance}}</span>
		</div>

		<div class='header-buttons'>
			<a href="#saving-record" role="tab" data-toggle="tab">
			<span>
				&nbsp<span class="glyphicon glyphicon-plus"></span>
				&nbsp储值明细&nbsp
			</span>
			</a>
			&nbsp&nbsp&nbsp
			<a href="#consume-record" role="tab" data-toggle="tab">
			<span>
				&nbsp<span class="glyphicon glyphicon-minus"></span>
				&nbsp消费明细&nbsp
			</span>
			</a>
		</div>

		<div class="content-title" href="#saving">
			<div class="line"></div>
		<span>
			&nbsp<span class="glyphicon glyphicon-piggy-bank"></span>
			&nbsp储值优惠&nbsp
		</span>
		</div>

		<div class="tab-content">

			<div id="saving" role="tabpanel" class="tab-pane active">
				@if (!empty($balance_rule) && count($balance_rule) > 0)
					@foreach($balance_rule['buy'] as $key => $value)
						<div class="rule-item row">
							<div class="col-xs-5 col-xs-offset-1">
								<small>满&nbsp</small>
								<big>{{ $key }} </big>
							</div>
							<div class="col-xs-4 col-xs-offset-1">
								<small>赠&nbsp</small>
								<big>
									{{ $is_percentage ? ($value * 100) . '%' : $value }}
								</big>
							</div>
						</div>
					@endforeach
					<form name="balance" id="balance">
						<div class="rule-item row">
							<div class="col-xs-5 col-xs-offset-1">
								<small>储值&nbsp</small>
								<input type='number' id='amount' name='amount' placeholder='----'>
							</div>
							<div class="col-xs-4 col-xs-offset-1">
								<small>送&nbsp</small>
								<big id="gift"></big>
							</div>
						</div>
						<div class="row">
							<input name='action' type="hidden" value="balance">
							<button class='btn btn-success btn-pay' disabled type='submit'>请输入储值金额</button>
						</div>
					</form>
				@else
					<div class="content-none">
						<div class="icon">
							<span class="glyphicon glyphicon-piggy-bank"></span>
						</div>

						储值规则制定中~
					</div>
				@endif
			</div>

			<div id="consume-record" role="tabpanel" class="tab-pane">
				@if (!empty($consume_records) && count($consume_records) > 0)
					<div class="record-group value-change">
						@foreach($consume_records as $record)
							@if(!empty($record->orderPayments{0}))
							<div class="record">
								<span class="record-time">{{$record['created_at']}}:</span>
								<span class="record-value minus">-{{number_format(isset($record->orderPayments{0}->amount) ?$record->orderPayments{0}->amount : 0,2)}} </span>

								<p>
									<span class="record-summary">{{$record['body'] or ''}}</span>
								</p>
							</div>
							@endif
						@endforeach
					</div>
				@else
					<div class="content-none">
						<div class="icon">
							<span class="glyphicon glyphicon-retweet"></span>
						</div>

						没有余额消费记录~
					</div>
				@endif
			</div>

			<div id="saving-record" role="tabpanel" class="tab-pane">
				@if (!empty($saving_records) && count($saving_records) > 0)
					<div class="record-group value-change">
						@foreach($saving_records as $record)
							<div class="record">
								<span class="record-time">{{$record['created_at']}}:</span>
								<span class="record-value add">+{{number_format($record['balance_fee']+$record['balance_present'],2)}} </span>

								<p>
									<span class="record-summary">{{ $record['body'] or ''}}</span>
								</p>
							</div>
						@endforeach
					</div>
				@else
					<div class="content-none">
						<div class="icon">
							<span class="glyphicon glyphicon-retweet"></span>
						</div>

						没有储值记录~
					</div>
				@endif
			</div>

		</div>

	</div>
@endsection

@section('scripts')
	@parent
	<script type="text/javascript">
        $(function () {
	        $('#amount').keyup(function () {
                var balance = parseFloat($('#amount').val());
                var rules = {!! $balance_rule_json !!};

                var currentkey = -1;
                var gift = 0;
                $.each(rules, function (i, val) {
                    var k = parseFloat(i);
                    if (balance >= k && k > currentkey)
                        currentkey = i;
                })
                if (currentkey == -1)
                    gift = 0;
                else {
                    var given = rules[currentkey];
                    if (given <= 1)
                        gift = balance * given;
                    else
                        gift = given;
                }

				gift = parseInt(gift);

				$('#gift').html(gift);

				$pay_btn = $('.btn-pay');
				if (balance <= 0) {
					$pay_btn.attr("disabled", true);
					$pay_btn.text('请输入储值金额');
				} else {
					$pay_btn.attr("disabled", false);
					$pay_btn.text('在线储值');
				}
			});

			$('#balance').submit(function(){
				var data={
					'balance_fee':$("#amount").val(),
					'balance_present':$('#gift').html()
				};
				$.ajax({
					type:'POST',
					url:'/wechat/order/balance',
					data:data,
					beforeSend:function(){
						$('.loading').show();
					},
					success:function(res){
						$('.loading').hide();
						wx.chooseWXPay({
							timestamp: res.timestamp,
							nonceStr: res.nonceStr,
							package: res.package,
							signType: res.signType,
							paySign: res.paySign,
							success: function (res) {
								swal('储值成功', res, "success");
							}
						});
					},
					error:function(res){
						$('.loading').hide();
						swal("支付失败", "储值请求失败，请刷新重试。", "warning");
					}
				});
				return false;
			});
		})
	</script>
@endsection