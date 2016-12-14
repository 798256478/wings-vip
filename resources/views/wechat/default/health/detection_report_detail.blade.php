@extends('wechat.default.master')

@section('title', '报告详情')

@push('links')
<style type="text/css">
  table{
    width: 100%;
  }
  
  .header .project-info{
    text-align: center;
  }

  .header .project-info span{
    margin-left: 15px;
  }

  .project-title{
    text-align: center;
  }

  .panel-group{
    margin-top: 10px;
  }
  
  .panel{
    border-top:1px solid #eee;
    border-radius: 0;
    margin:0 -15px;
  }

  .panel .panel-title{
    color: #5c5c5c;
  }

  .panel-group .panel+.panel {
     margin-top: 0px; 
  }

  .panel-heading .project-info{
    margin-top: 10px;
    font-size: 15px;
  }

  .panel .project-info span{
    margin-left: 8px;
    margin-right: 55px;
  }

  .panel-body dl{
    margin-bottom: 10px;
    padding-bottom: 5px;
    border-bottom: 1px solid #e4e4e4;
  }

  .panel-body dl:last-child{
    border-bottom: 0;
    padding:0;
    margin-bottom: 0px;
  }

  .panel-body th{
    font-weight: normal;
  }

  .panel-body dd{
    margin:5px 0;
    font-size: 13px;
  }
  
  .panel-body .name{
    font-weight: 700;
  }

  .panel-heading{
    padding:10px 15px;
    min-height: 65px;
    position: relative;
  }

  .panel-heading .right{
    position: absolute;
    right: 0px;
    top: 0px;
    line-height: 70px;
    height: 100%;
    width: 55px;
    text-align: center;
    color: #777;
  }

  .panel-body{
    font-size: 12px;
    color: #8c8c8c;
    padding:5px 15px 10px;
    background: #f8f8f8;
  }

  .project-info .score{
    display: inline-block;
    width: 90px;
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

</style>
@endpush

@section('content')
    <div class="container">
      <ol class="breadcrumb row">
        <li><a href="/wechat">会员中心</a></li>
        <li><a href="/wechat/health/info/{{$experiment_data_id}}">检测信息</a></li>
        <li><a href="/wechat/health/detection/report/{{$experiment_data_id}}">检测报告</a></li>
        <li class="active">报告详情</li>
      </ol>
      <div class="header">
         <h3 class="project-title">{{$parentName}}</h3>
      </div>
        <div class="panel-group" id="accordion">
            @foreach($projectDetails as $projectDetail)
            <div class="panel">
            <div class="panel-heading" data-toggle="collapse" data-parent="#accordion"href="#{{$projectDetail['id']}}"  onclick="change(this)">
                <div class="panel-title">{{$projectDetail['name']}}</div>
                <div class="project-info">
                <span class="score">{{$projectDetail['project_dto']['score']}} 分</span>
                <span class="">{{$projectDetail['project_dto']['abilityLevel']}}</span>
                </div><i class="glyphicon glyphicon-chevron-down right"></i>
            </div>
            <div id="{{$projectDetail['id']}}" class="panel-collapse collapse">
                <div class="panel-body">
                @foreach($projectDetail['gene_dtos'] as $gene)
                <dl>
                    <dd>
                    <span class="name">基因名称：{{$gene['name']}}</span>
                    @if($gene['effect'])
                        （{{$gene['effect']}}）
                    @endif
                    </dd>
                    <table>
                    <tr>
                        <th>SNP编号</th>
                        <th>SNP位点</th>
                        <th>风险基因</th>
                        <th>结果</th>
                    </tr>
                    @foreach($gene['site_dtos'] as $site)
                    <tr>
                        <td style="width: 100px">{{$site['code']}}</td>
                        <td style="padding: 0 4%">{{$site['SNPSite']}}</td>
                        <td style="padding: 0 6%">{{$site['mutation']}}</td>
                        <td style="padding: 0 2%"> <span style="color:rgb({{$site['color']}});">{{$site['showGenotype']}}</span></td>
                    </tr>
                    @endforeach
                    </table>
                </dl>
                @endforeach
                </div>
            </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection
@section('scripts')
  @parent
  <script>
    function change(e){
        if($(e).next().hasClass('in')){
        $(e).children('i').removeClass().addClass('glyphicon glyphicon-chevron-down right');
        }else{
        $('.panel-heading').children('i').removeClass().addClass('glyphicon glyphicon-chevron-down right');
        $(e).children('i').removeClass().addClass('glyphicon glyphicon-chevron-up right');
        }
    }
  </script>
@endsection