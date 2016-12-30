@extends('wechat.default.master')

@section('title', '会员中心')

@push('links')
<style>
	body{
		background-color: #eee;
	}

	.service .sub-title .value{
		color: {{ $theme->color['HIGHLIGHT'] or '#0092DB' }};
	}
	
	.foot-slogan{
		color: #bfbebe;
		font-size: 13px;
		margin-top: 10px;
		text-align: center;
	}
	
</style>
@endpush

@section('content')
<div class="container ">
	@include($theme->getViewPath('card_part'))
	<div class="service-group row">
		<a class="service" href="/wechat/redeem_code">
			<span class="icon glyphicon glyphicon-barcode"></span>
			<span class="title">兑换码</span>
			<span class="glyphicon glyphicon-chevron-right"></span>
		</a>
	</div>

	<div class="service-group row">
		<a class="service" href="/wechat/balance">
			<span class="icon glyphicon glyphicon-ruble"></span>
			<span class="title">余额</span>
			<span class="glyphicon glyphicon-chevron-right"></span>
			<span class="sub-title">
				<span class="value">{{$card_info['balance']}}</span>
			&nbsp;元</span>
		</a>
		<a class="service" href="/wechat/bonus">
			<span class="icon glyphicon glyphicon-bitcoin"></span>
			<span class="title">积分</span>
			<span class="glyphicon glyphicon-chevron-right"></span>
			<span class="sub-title">
				<span class="value">{{$card_info['bonus']}}</span>
			&nbsp;分</span>
		</a>
		<a class="service tickets-link" href="#">
			<span class="icon glyphicon glyphicon-gift"></span>
			<span class="title">礼券</span>
			<span class="right glyphicon glyphicon-chevron-down"></span>
			<span class="sub-title">
				<span class="value">{{ count($tickets) }}</span>
			&nbsp;张</span>
		</a>
	</div>
	
	<div class="row" style="margin-top:0;">
		<div class="tickets-pack" style="display:none;">
			@foreach ($tickets as $ticket)
				@include($theme->getViewPath('ticket_part'))
			@endforeach

			<div style="text-align: center;">
				<button class="collapse-button btn btn-success collapsed"
				ticket-count="{{count($tickets)}}">
					收起券包
				</button>
			</div>
		</div>
	</div>
	
	<div class="service-group row">
        {{--<a class="service" href="/wechat/attendance">--}}
			{{--<span class="title">签到</span>--}}
			{{--<span class="glyphicon glyphicon-chevron-right"></span>--}}
		{{--</a>--}}
		{{--<a class="service" href="/wechat/mall">--}}
			{{--<span class="title">会员商城</span>--}}
			{{--<span class="glyphicon glyphicon-chevron-right"></span>--}}
		{{--</a>--}}
        {{--<a class="service" href="/wechat/order">--}}
			{{--<span class="title">商城订单</span>--}}
			{{--<span class="glyphicon glyphicon-chevron-right"></span>--}}
		{{--</a>--}}
		<a class="service" href="/wechat/records">
			<span class="title">交易记录</span>
			<span class="glyphicon glyphicon-chevron-right"></span>
		</a>
        <a class="service" href="/wechat/member_info">
			<span class="title">
				会员信息
			</span>
			<span class="glyphicon glyphicon-chevron-right"></span>
			<span class="sub-title" style="color:#aaa">
				{{ $card_info['mobile'] or '' }}
			</span>
		</a>
	</div>
	<p class="foot-slogan"><span>{{ $theme->texts['SLOGAN'] or '' }}</span></p>
</div>
@endsection

@section('scripts')
@parent
<script type="text/javascript">
	function getUrlParam(name) {
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
		var r = window.location.search.substr(1).match(reg);  //匹配目标参数
		if (r != null) return unescape(r[2]); return null; //返回参数值
	}

	$(function(){

		//如果参数包含Code，则尝试自动兑换
		var code = getUrlParam('code');

		if (code != null){
			$('.loading').show();
            
            $.post('/wechat/api/redeem_code' , {
                code: code
            }).success(function(data){
                
                if (data.status == 'SUCCEED')
                    swal(data.title, data.message, "success");
                else{
                    swal({   
                        title: data.title,   
                        text: data.message,   
                        imageUrl: "/common/imgs/empty.jpg" 
                    });
                }
                
                $('.loading').hide();
                
            }).error(function(){
                swal("异常", "系统出现异常，请与服务人员联系。", "warning");
                $('.loading').hide();
            });
		}
		
		$('.tickets-link').click(
			function (e){
				var $icon = $('.tickets-link .right.glyphicon');
				var $tickets_pack = $('.tickets-pack');

				if ($tickets_pack.is(':hidden')){
					$tickets_pack.show();
					$icon.removeClass('glyphicon-chevron-down');
					$icon.addClass('glyphicon-chevron-up');
				}
				else{
					$tickets_pack.hide();
					$icon.removeClass('glyphicon-chevron-up');
					$icon.addClass('glyphicon-chevron-down');
				}
			}
		);
		$('.collapse-button').click(
				function (e) {
					$('.tickets-pack').hide();
					var $icon = $('.tickets-link .glyphicon');
					$icon.removeClass('glyphicon-chevron-up');
					$icon.addClass('glyphicon-chevron-down');

				}
		);

	});
</script>
@endsection