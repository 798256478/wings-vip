@extends('wechat.default.master')

@section('title', '检测报告')

@push('links')
<style type="text/css">
  .title-info{
  	text-align: center;
  	margin-bottom: 10px;
  	color: #8c8c8c;
  }
  
  .title-info span{
    margin:0 8%;
  }

  h3,.nosuggest{
    text-align: center;
  }
  
  .projects{
  	padding:5px 15px;
    position: relative;
  }

  .panel-title{
    color: #5c5c5c;
    font-size: 18px;
  }

  .projects .left{
  	line-height: 30px;
  }

  .projects .right{
  	position: absolute;
    right: 0px;
    top: 0px;
    line-height: 70px;
    height: 100%;
    width: 50px;
    text-align: center;
    color: #777;
  }

  .projects .info span{
    display: inline-block;
    width: 60px;
    margin-left: 7px;
    margin-right: 50px;
    font-size: 15px;
  }


  .level{
    position: absolute;
    left:70px;
  }
  
  .suggest{
    position: absolute;
    left:180px;
    color: #3399ff;
  }

  .danger{
    color: #a94442;
  }

  .warning{
    color: #ff9900;
  }

  .success{
    color: #3399ff;
  }

  .unfinish{
    margin-top: 10px;
    text-align: center;
    font-size: 16px;
    color: #8c8c8c;
  }
	
  .unfinish span{
  	font-size: 120px;
  	margin-bottom: 8px;
    margin-top: 20px;
    color:#dedede;
  }

  .unfinish div{
    margin:10px 0;
  }


</style>
@endpush

@section('content')
    <div class="container">
      <ol class="breadcrumb row">
        <li><a href="/wechat">会员中心</a></li>
        <li><a href="/wechat/health/info/{{$experiment_data->id}}">信息中心</a></li>
        <li class="active">检测报告</li>
      </ol>
      @if($experiment_data->progress_id==5||$experiment_data->progress_id==6||$experiment_data->progress_id==8)
        <h3>{{$experimentName}}</h3>
        <div class="title-info">
          <span>{{$customerName}}</span>
          <span>{{$code}}</span>
          <span>{{substr($created_at,0,11)}}</span>
        </div>
        <div>
        @foreach($projects as $project)
            <div class="projects">
  	          	<div class="left" href="#{{$project->id}}">
  	          		<div class="panel-title">
  	        		    {{$project->name}} 
                    </div>
  	          	</div>        		    
                <a class="right" href="/wechat/health/detection/reportDetail/{{$experiment_data->id}}/{{$project->id}}">   				        
                    <i class="glyphicon glyphicon-chevron-right" ></i>
                </a>
            </div>
         @endforeach
        </div>
      @else
      <div class="unfinish">
      	<span class="glyphicon glyphicon-piggy-bank"></span>
        <div>亲，您的报告正在加紧进行中。。。</div>
        您可以<a href="/wechat/health/detection/progress/{{$experiment_data->id}}">查看检测进度</a>
      </div>
      @endif
    </div>
@endsection