@extends('wechat.default.master')

@section('title', '个人信息')

@push('links')
<link href="/components/mobiscroll/css/mobiscroll.animation.css" rel="stylesheet" type="text/css"/>
<link href="/components/mobiscroll/css/mobiscroll.frame.css" rel="stylesheet" type="text/css"/>
<link href="/components/mobiscroll/css/mobiscroll.scroller.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
	body {
		background-color: #eee;
	}

	.member-info-group {
		margin-top: 15px;
		background-color: #fff;
		padding: 0 15px 0 15px;
	}

	.member-info {
		height: 48px;
		line-height: 48px;
		border-top: 1px solid #eee;
	}

	.member-info:first-child {
		border-top: none;
	}

	.member-info-group.phone:hover {
		background-color: #ddd;
	}

	.member-info .list-input, .mbsc-control-ev {
		padding: 0;
		border: none;
		outline: none;
		display: inline-block;
		width: auto;
		text-align: right;
		background-color: inherit;
		margin-right: 10px;
		float: right;
		cursor: pointer;
	}
	.phone:hover{
		background:#ddd;
	}

	.title {
		margin: 10px auto;
		padding: 5px auto;
	}

	.title h3 {
		text-align: center;
	}

	#name-save, #ver-btn {
		float: right;
		margin: 10px 5px 0 5px;
	}

	.color-show {
		color: #777;
	}

	#sex_dummy{
		color: #777;
	}

	.theme-color {
		color: {{ $theme->colors['THEME'] or '#0092DB' }};
	}

</style>
@endpush

@section('content')
	<div class="container">
		<ol class="breadcrumb row">
			<li><a href="/wechat">会员卡</a></li>
			<li><a href="/wechat/health">我的检测</a></li>
			<li><a href="/wechat/health/info/{{$experiment_data_id}}">检测中心</a></li>
			<li class="active">个人信息</li>
		</ol>
		<div class="member-info-group  row">
			<div class="member-info">
				<label>条码</label>
				<span class="list-input color-show">{{$customer['code']==null?'暂无':$customer['code']}}</span>
			</div>
		</div>
        <div class="member-info-group row">
			<div class="member-info">
				<label>姓名</label>
				<button type="button" id="name-save" class="btn btn-sm btn-primary">保存</button>
				<input  class="list-input color-show" value="{{$customer['name']==null?'暂无':$customer['name']}}" id="name">
			</div>
			<div class="member-info">
				<label>性别</label>
				<select id="sex" readonly name="sex" class="list-input color-show">
					<option value="0" @if ($customer['sex']==0) selected @endif >女</option>
					<option value="1" @if ($customer['sex']==1) selected @endif >男</option>
				</select>
			</div>
		</div>
		<div class="member-info-group  row phone">
			<div class="member-info" id="phone">
				<label>手机</label>
				<span class="list-input color-show">{{$customer['mobile']==null?'暂无':$customer['mobile']}}</span>
			</div>
		</div>
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
			$('#sex').mobiscroll().select({
				display: "bottom",
				lang: "zh",
				onSelect: function (valueText, inst) {
                    $.ajax({
                        url: "/wechat/health/detection/save_info/{{$customer['id']}}",
                        type: 'POST',
                        data: "sex=" + inst.getVal(),
                        success:function(data){
                        	alert('保存成功');
                        },
                        error: function (data) {
                            alert("保存失败");
                        }
                    });
                }
			});

			$("#name").focus(function () {
            	$("#name-save").show();
            });

            $("#name-save").hide();

            $('#name-save').click(function(){
				$.ajax({
                        url: "/wechat/health/detection/save_info/{{$customer['id']}}",
                        type: 'POST',
                        data: "name=" + $('#name').val(),
                        success:function(data){
                        	alert('保存成功');
                        },
                        error: function (data) {
                            alert("保存失败");
                        }
                  	});
				});
            $("#phone").click(function(){
            	window.location.href="/wechat/health/detection/phone/{{$customer['barcode_id']}}/{{$experiment_data_id}}";
            })
		});
	</script>
@endsection