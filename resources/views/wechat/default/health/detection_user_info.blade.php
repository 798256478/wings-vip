@extends('wechat.default.master')

@section('title', '会员信息')

@push('links')
<link href="/components/mobiscroll/css/mobiscroll.animation.css" rel="stylesheet" type="text/css"/>
<link href="/components/mobiscroll/css/mobiscroll.frame.css" rel="stylesheet" type="text/css"/>
<link href="/components/mobiscroll/css/mobiscroll.scroller.css" rel="stylesheet" type="text/css"/>
<link href="/common/css/wechat/default/detection_user_info.css" rel="stylesheet">

<style type="text/css">

	.submit {
		background-color: {{ $theme->colors['THEME'] or '#0092DB' }};
		color: white;
		width: 100%;
	}

	.mbsc-mobiscroll .dwb {
		color: {{ $theme->colors['THEME'] or '#0092DB' }};
	}

	.mbsc-mobiscroll .dwwol {
		border-top-color: {{ $theme->colors['THEME'] or '#0092DB' }};
		border-bottom-color: {{ $theme->colors['THEME'] or '#0092DB' }};
	}

</style>
@endpush

@section('content')
	<div class="container">
		<ol class="breadcrumb row">
			<li><a href="/wechat">会员卡</a></li>
			<li><a href="/wechat/health/detections">基因检测</a></li>
			<li class="active">个人信息</li>
		</ol>
		<div class="title"><h3>个人信息</h3></div>
		<form action="" id="userForm" method="post">
			<div class="member-info-group  row">
				<div class="member-info">
					<label>条码</label>
					<input id="barcode" name="barcode" class="list-input color-show">
				</div>
			</div>
			<span class="error">
				@if(Session::has('message.code'))
					{{session('message.code')}}
				@endif
				@if(count($errors)>0)
					{{$errors->first('barcode')}}
				@endif
            </span>

			<div class="member-info-group  row">
				<div class="member-info">
					<label>手机</label>
					<button type="button" id="ver-btn" class="btn btn-sm btn-primary ver-btn">验证码</button>
					<input id="mobile" name="mobile" class="list-input color-show" placeholder="填写手机号"
				       value="{{ $card_info['mobile'] or '' }}">
				</div>
				<div class="member-info " id="ver">
					<label for="">验证码</label>
					<input id="ver-key" name="ver_key" class="list-input color-show">
				</div>
			</div>
			<span class="error">
				@if(Session::has('message.mobile'))
					{{session('message.mobile')}}
				@endif

				@if(count($errors)>0)
					{{$errors->first('mobile')}}
				@endif
				@if(count($errors)>0)
					{{$errors->first('ver_key')}}
				@endif
			</span>

			<div class="member-info-group row">
				<div class="member-info">
					<label>姓名</label>
					<input id="name" name="name" class="list-input color-show" value="{{$card_info['name'] or '' }}">
				</div>
				<div class="member-info">
					<label>性别</label>
					<select id="sex" readonly name="sex" class="list-input ">
						<option value="0" disabled @if ($card_info['sex']) selected @endif
						>请选择性别</option>
						<option value="2" @if ($card_info['sex']===2) selected @endif >女</option>
						<option value="1" @if ($card_info['sex']===1) selected @endif >男</option>
					</select>
				</div>
			</div>
			<span class="error">
				@if(count($errors)>0)
					{{$errors->first('name')}}
				@endif
				@if(count($errors)>0)
					{{$errors->first('sex')}}
				@endif
			</span>

			@if(!(isset($card_info) && $card_info['mobile'] != "" && $card_info['mobile'] != null))
				<div class="member-info-group row">
					<div class="member-info">
						<input type="checkbox" id="is-member" name="is-member" value="1">
						<label for="is-member">同时作为会员注册</label>
						<br>
					</div>
				</div>
			@endif
			<span class="error">
				@if(Session::has('message.save'))
					{{session('message.save')}}
				@endif
			</span>

			<div class="submit-div">
				<input type="submit" class="btn submit" id="submit" value="下一步">
			</div>
		</form>
	</div>
@endsection

@section('scripts')
	@parent
	<script src="/components/mobiscroll/js/mobiscroll.appframework.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.core.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.frame.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.scroller.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.util.datetime.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.select.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.datetimebase.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.datetime.js"></script>
	<script src="/components/mobiscroll/js/i18n/mobiscroll.i18n.zh.js"></script>
	<script>
		$(function () {
			var original = "{{ $card_info['mobile'] or ''}}";
			var cardId="{{$card_info->id}}";
			$('#sex').mobiscroll().select({
				display: "bottom",
				lang: "zh"
			});

			$("#mobile").bind("input propertychange",function () {
				if($(this).val().length==11){
					if($(this).val()!=original) {
						$("#ver-btn").show();
					}
				}else{
					$("#ver-btn").hide();
				}
			});
			$("#ver-btn").hide();
			$("#ver-btn").click(function () {
				$("#ver-btn").hide();
				if ($("#mobile").val() != undefined && $("#mobile").val() != "") {
					var mobile = $("#mobile").val();
					var reg = /^1\d{10}$/;
					if (!reg.exec(mobile)) {
						alert("手机号格式不正确！");
						return false;
					}
					var data = {
						mobile : $("#mobile").val(),
						type : 'mobile'
					};
					$.ajax({
						url: '/wechat/health/detection/detectionSendSms',
						type: 'POST',
						data: data,
						error: function (data) {
							alert(data.responseText);
						},
						success: function (data) {
							$('#ver').show();
							var count = 60;
							var countdown = setInterval(CountDown, 1000);

							function CountDown() {
								$("#ver-btn").attr("disabled", true);
								$("#ver-btn").html(count);
								if (count == 0) {
									$("#ver-btn").html("验证码").removeAttr("disabled");
									clearInterval(countdown);
								}
								count--;
							}
						}
					});
				} else {
					alert("手机号不能为空！");
				}
			});

//			数据验证
			var inputVerify=function () {
				if (!$("#barcode").val()) {
					alert('请输入条码');
					return false;
				}
				if (!$("#sex").val()) {
					alert('请选择性别');
					return false;
				}
				if (!$("#name").val()) {
					alert('请输入姓名');
					return false;
				}
				if (!$("#mobile").val()) {
					alert('请输入手机号');
					return false;
				}
				if ($("#mobile").val() != original) {
					if ($("#ver-key").val().length != 6) {
						alert('请输入6位验证码');
						return false;
					}
				}
				return true;
			};

			//提交
			$("#userForm").submit(function(){
				if(!inputVerify()){
					return false;
				}
				$.ajax({
					type:"POST",
					url:"/wechat/health/detection/saveUserInfo",
					data:$('#userForm').serialize(),
					error:function(data){
						alert(data.responseText);
						return false;
					},
					success:function(data){
						window.location.href="/wechat/health/detection/" + data + "/questionnaire";
					}
				});
				return false;
			});

		});
	</script>
@endsection