@extends('wechat.default.master')

@section('title', '确认支付')

@push('links')
{{--<link href="/common/css/wechat/default/order.css" rel="stylesheet">--}}
<style type="text/css">
	html,body{
		min-height: 100%;
		background-color: #eee;
	}

	.container{
		padding: 0 20px;
	}

	.main{
		background-color: #ffffff;
		padding: 0 20px 50px 20px;
		margin-top: 20px;
	}

	.shop-name{
		width: 100%;
		margin: 60px 0 30px 0;
		text-align: center;
	}

	.main .introduction{
		margin-bottom: 10px;
	}
	
	.main .money i{
		font-size: 24px;
		width: 6%;
		float: left;
		margin-top: 16px;
	}

	.main .money .money-input{
		border: none;
		outline: none;
		border-bottom: 1px solid #dfdfdf;
		width: 94%;
		font-size: 50px;
		float: left;
		background: none;
	}

	.order-submit {
		background-color: {{ $theme->colors['BUTTON1'] or '#0092DB' }};
		width: 100%;
		font-size: 20px;
		/*margin-left: 5%;*/
		color: #ffffff;
		margin-top: 40px;
	}

	.order-submit[disabled]{
		background-color: #ccc;
		color: #888;
	}
</style>
@endpush

@section('content')
	<div class="container">
		<div class="main clearfix">
			<div class="shop-name">
				<h2>{{$shopName}}</h2>
			</div>
			<div class="introduction">
				<span>支付金额</span>
			</div>
			<div class="money">
				<i class="fa fa-cny"></i>
				<input type="tel" class="money-input" id="money-input" placeholder="0.00">
			</div>
		</div>
		<div class="button">
			<input type="button" class="order-submit btn " value="立即支付" disabled>
		</div>
	</div>
@endsection

@section('scripts')
	@parent
	<script type="text/javascript">

		function onOrderSubmit() {

			var data = {
				'wechat_pay': parseFloat($('.money-input').val()).toFixed(2)
			};
			$.ajax({
				type: "POST",
				url: "/wechat/order/qrCodePay",
				data: data,
				beforeSend: function () {
					$('.loading').show();
				},
				success: function (pay_config) {
					$('.loading').hide();
					wx.chooseWXPay({
						timestamp: pay_config.timestamp,
						nonceStr: pay_config.nonceStr,
						package: pay_config.package,
						signType: pay_config.signType,
						paySign: pay_config.paySign,
						success: function (res) {
//								swal('订单支付成功', res, "success");
						window.location.href = '/wechat/order/paySuccess/' + res.order_id;
						}
					});
				},
				error: function (res) {
					$('.loading').hide();
					swal('订单异常，请重试', jQuery.parseJSON(res.responseText).message, "warning");
//					console.log(res.responseText);
				}
			});
		}

		$(function () {
			$("#money-input").keydown(function(event){
				if(event.which!=8) {
					$pointIndex = $(this).val().indexOf('.');
					if ($pointIndex > 0 && $(this).val().substring($pointIndex + 1).length >= 2) {
						event.preventDefault();
					}
				}
			}).keyup (function (event) {
//				event.preventDefault();
				var val = $(this).val();
//					console.log(val);
				val = val.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
				val = val.replace(/^\./g,""); //验证第一个字符是数字
				val = val.replace(/\.{2,}/g,"."); //只保留第一个, 清除多余的
				val = val.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
				val = val.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
				$(this).val(val);
				if(val>0){
					$('.order-submit').removeAttr('disabled');
				}else{
					$('.order-submit').attr('disabled',true);
				}

			});
			$('.order-submit').click(onOrderSubmit);

		});
	</script>
@endsection