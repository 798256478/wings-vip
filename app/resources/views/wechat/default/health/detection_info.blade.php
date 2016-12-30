@extends('wechat.default.master')

@section('title', '检测中心')

@push('links')
<style type="text/css">
    body {
        background-color: #eee;
    }
	
	.breadcrumb,.header{
		margin-bottom: -15px;
	}

	.header{
		padding-bottom: 10px;
	}

	.service-group .service:first-child {
    	border-top:1px solid #eee;
	}

    .title-h3 {
        margin: 10px auto;
        padding: 5px auto;
    }

    .title-h3 h3 {
        text-align: center;
    }
	
	.top{
		position: relative;
		padding:5px 15px;
		min-height: 45px;
	}
	
    h3{
        text-align: center;
    }

	.info{
		text-align: center;
        color: #8c8c8c;
	}
	
    .info span{
		margin: 30px;
	}
</style>
@endpush

@section('content')
    <div class="container">
        <ol class="breadcrumb row">
            <li><a href="/wechat">会员卡</a></li>
            <li><a href="/wechat/health">我的检测</a></li>
            <li class="active">检测中心</li>
        </ol>
        <div class="service-group row header">
        	<h3>{{$experiment_data['experiment_name']}}</h3>       
	        <div class="info">
		        <span>{{$experiment_data['customer_name']}}</span>
		        <!--<span>{{substr($experiment_data['created_at'],0,11)}}</span>-->
	        </div>
		</div>

        <div class="service-group row">
            <a class="service" href="/wechat/health/detection/questionnaire/{{$experiment_data['barcode_id']}}/{{$experiment_data['id']}}">
                <span class="icon glyphicon glyphicon-list-alt"></span>
                <span class="title">健康问卷</span>
                <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            <a class="service" href="/wechat/health/detection/progress/{{$experiment_data['id']}}">
                <span class="icon glyphicon glyphicon-time"></span>
                <span class="title">检测进度</span>
                <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            <a class="service" href="/wechat/health/detection/report/{{$experiment_data['id']}}">
                <span class="icon glyphicon glyphicon-file"></span>
                <span class="title">电子报告</span>
                <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            <a class="service" href="/wechat/health/detection/riskdatas/{{$experiment_data['id']}}">
                <span class="icon glyphicon glyphicon-file"></span>
                <span class="title">评估报告</span>
                <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
            <a class="service" href="/wechat/health/detection/reservation/{{$experiment_data['id']}}">
                <span class="icon glyphicon glyphicon-calendar"></span>
                <span class="title">预约解读</span>
                <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
        </div>
        <div class="service-group row">
            <a class="service" href="/wechat/health/detection/userInfo/{{$experiment_data['barcode_id']}}/{{$experiment_data['id']}}">
                <span class="icon glyphicon glyphicon-user"></span>
                <span class="title">个人信息</span>
                <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
        </div>
    </div>
@endsection
