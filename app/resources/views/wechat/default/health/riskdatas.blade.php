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
  
  .panel{
    position: relative;
    border-top:1px solid #f1f1f1;
    border-radius: 0;
    margin:0 -15px;
  }

  .panel-group .panel+.panel {
     margin-top: 0px; 
  }

  .panel-heading{
    padding: 5px 15px;
    height: 75px;
  }

  .panel-title{
    color: #5c5c5c;
    font-size: 18px;
  }

  .panel-heading .left{
  	line-height: 40px;
    float: left;
    height: 70px;
  }

  .panel-heading .right{
    color: #fff;
    float: right;
    position: absolute;
    right: 10px;
    top: 10px;
  }
    .panel-heading .info{
    height: 20px;
    line-height: 20px;
    }
  .panel-heading .info>span{
    display: block;
    width: 95px;
    margin-right: 0px;
    font-size: 12px;
    color: #999;
    height: 20px;
    float: left;
  }

  .panel-body{
    font-size: 13px;
    color: #8c8c8c;
    line-height: 1.5em;
    padding:5px 15px;
  }
  
  .panel-group .panel-heading+.panel-collapse>.list-group, .panel-group .panel-heading+.panel-collapse>.panel-body {
    border-top: 0;
  }

   .panel-body{
       background-color:#f8f8f8;  
   }
  
  .suggest{
    color:#8c8c8c;
  }
  
  .level{
    border-radius: 20px;
    width: 40px;
    height: 40px;
    vertical-align: middle;
    line-height: 40px;
    font-size: 12px;
    text-align: center;
    margin-top: 10px;
  }

  .level1{
    background-color: #F7908D;
  }

  .level2{
    background-color: #ff9900;
  }

  .level3{
    background-color: #3399ff;
  }

  .unfinish{
    margin-top: 10px;
    text-align: center;
    font-size: 16px;
    color: #8c8c8c;
  }
	
  .unfinish span{
  	font-size: 100px;
  	margin-bottom: 10px;
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
        <li><a href="/wechat/health">我的检测</a></li>
        <li><a href="/wechat/health/info/{{$experiment_data->id}}">检测中心</a></li>
        <li class="active">评估报告</li>
      </ol>
       @if($experiment_data->progress_id==6||$experiment_data->progress_id==5)
            <h3>{{$experimentName}}</h3>
            <div class="title-info">
                <span>{{$customerName}}</span>
                <span>{{$code}}</span>
                <span>{{substr($created_at,0,11)}}</span>
            </div>
            <div class="panel-group" id="accordion">
                @foreach($projects as $project)
                <div class="panel">
                        <div class="panel-heading clear">
                            <div class="left" data-toggle="collapse" data-parent="#accordion"  href="#{{$project['id']}}">
                                <div class="panel-title">{{$project['name']}} </div>
                                <div class="info">    
                                    <span>基因评分:<span>{{$project['projectdata']['score']}}</span></span>
                                    <span>基因水平:<span>{{$project['riskdata']['abilityLevel']}}</span></span>
                                    <span>环境评分:<span>{{$project['riskdata']['circum_score']}}</span></span>
                                </div>
                            </div> 
                            <div class="right">
                                <div class="level level{{$project['riskdata']['level']}}">{{$project['riskdata']['total_score']}}</div>
                            </div>      		    
                        </div>
                    <div id="{{$project['id']}}" class="panel-collapse collapse">
                        <div class="panel-body">
                            @if($project['riskdata']['riskAssessment'])
                                <div class="suggest">
                                {{$project['riskdata']['riskAssessment']}}
                                </div>
                            @else
                                <div class='nosuggest'>暂无建议</div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
      @else
        <div class="unfinish">
                <span class="glyphicon glyphicon-piggy-bank"></span>
                <div>亲，您的指导报告正在加紧进行中。。。</div>
                您可以<a href="/wechat/health/detection/progress/{{$experiment_data->id}}">查看检测进度</a>
        </div>
      @endif
    </div>
@endsection