@extends('wechat.default.master')

@section('title', '我的检测')

@push('links')
<style type="text/css">
    body{
        background:#eee;
    }
    
    a{
        color:#000;
    }

    a:hover{
        color:#000;
        text-decoration:none;
    }
    
	.title{
		color: #666;
		font-weight: normal;
		font-size: 12px;
	}
	
	.icon{
		margin: 0 13px 0 20px;
        padding: 8px 10px;
        height: 45px;
        width: 45px;
        float: left;
        background: lightskyblue;
        border-radius: 100%;
        color: #fff;
	}

    .bind{
        height:84px;
        line-height:84px;
        font-size: 32px;
    }

    .bind div{
        padding-left: 20%;
    }

    .bind .glyphicon{
    	float: left;
        margin:17px 12px;
        color:#fff;
    }
    
    .detection{
        padding:10px 0;
        position: relative;
    }

    .detection h4{
        margin-bottom: 15px;
    }

    .detection label{
        margin:-10px 0 5px 10px;
        font-size:14px;
        font-weight: normal;
        color: #8c8c8c; 
    }

    .detection .time{
    	position: absolute;
    	left:75%;
    	bottom: 10px;
    }
    
    .detection .nowprogress{
        position: absolute;
        left:43%;
        bottom: 10px;
    }

    .service-group{
        border-radius: 4px;
        margin-top: 8px;
    }

    .bind-group{
        border:1px dashed #eee;
        background:lightskyblue;
        margin: 8px 0 15px;
        box-shadow: inset 0 0 3px #eee;
    }

    .bind-group label{
        color:#fff;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <ol class="breadcrumb row">
            <li><a href="/wechat">会员中心</a></li>
            <li class="active">我的检测</li>
        </ol>
        @foreach($experiment_datas as $experiment_data)
        <div class='service-group'>
            <a href="/wechat/health/info/{{$experiment_data['id']}}">
                <div class='detection'>
                    <table>
                        <td>
                            <div class="icon">
                                <span class="fa fa-2x fa-flask"></span>
                            </div>
                        </td>
                        <td>
                        <h4>{{$experiment_data['experiment_name']}}</h4>
                            <div>
                                <label>{{$experiment_data['customer_name']}}</label>
                                <label class="time">{{convart_timestamp_to_display(strtotime($experiment_data['created_at']))}}</label>
                                <label class="nowprogress">{{$experiment_data['nowProgress']}}</label>
                            </div>
                        </td>
                    </table>
                </div>
            </a>  
        </div>
        @endforeach 
        <div class="service-group bind-group">
            <div class='bind'>
                <a href="/wechat/health/detection/new">
                    <div>
                        <span><i class='glyphicon glyphicon-plus'></i></span>
                        <label for="">绑定检测</label>
                    </div>
                </a>
            </div>
        </div>   
    </div>
@endsection

