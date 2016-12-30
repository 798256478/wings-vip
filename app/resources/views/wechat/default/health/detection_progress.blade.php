@extends('wechat.default.master')

@section('title','检测过程记录')

@push('links')
<style type="text/css">

body {
  color: #7f8c97;
  background-color: #eee;
}

.breadcrumb{
  margin-bottom:-15px;
}

.cd-container {
  max-width: 1170px;
  margin: 0 auto;
}

#cd-timeline {
  position: relative;
  margin-bottom: 1.5em;
}

#cd-timeline::before {
  content: '';
  position: absolute;
  top: 0;
  left: 40px;
  height: 100%;
  width: 4px;
  background: #d7e4ed;
}

.cd-timeline-block {
  position: relative;
  margin: 2.5em 0;
}

.cd-timeline-img {
  position: absolute;
  top: -2px;
  left: 20px;
  width: 40px;
  height: 40px;
  text-align: center;
  font-size: 22px;
  color: aliceblue;
  border-radius: 50%;
  box-shadow: 0 0 0 4px white, inset 0 2px 0 rgba(0, 0, 0, 0.08), 0 3px 0 4px rgba(0, 0, 0, 0.05);
}

/*.unfinished{
  margin-bottom: 60px;
}*/

/*.unfinished .cd-timeline-img{
  height: 80px;
  width: 80px;
  left:0;
}*/

.cd-timeline-img.cd-complete {
  background: lightskyblue;
  line-height: 45px;
}

.cd-timeline-img.cd-unfinished {
  background: #eee;
  line-height: 67px;
}

.fa{
  position:relative;
  top:-11px;
}

.cd-unfinished .fa{
  color:silver;
}

.cd-timeline-content {
  position: relative;
  margin-left: 95px;
  background: white;
  border-radius: 0.25em;
  padding: 5px 10px;
  box-shadow: 0 3px 0 #d7e4ed;
}

/*.unfinished .cd-timeline-content{
  top:20px;
  margin-left: 100px;
}*/

.cd-timeline-content h4 {
  color: #303e49;
  margin:5px 0;
}
.cd-timeline-content .cd-date {
  font-size: 14px;
  color: #8c8c8c;
}

.cd-timeline-content::before {
  content: '';
  position: absolute;
  top: 13px;
  right: 100%;
  height: 0;
  width: 0;
  border: 7px solid transparent;
  border-right: 7px solid white;
}

.display{
    position: relative;
    top: 10px;
}

.last{
  margin-bottom: 45px;
}

.last .cd-timeline-img{
  width:80px;
  height: 80px;
  left:0;
}

.last .cd-timeline-img span{
  font-size: 45px;
  line-height: 80px;
}

.last .cd-timeline-content{
  margin-left: 95px;
  padding:8px 15px;
}

.last .cd-timeline-content span{
  margin-top:16px;
  display: block; 
}

.last .cd-timeline-content:before{
  top:28px;
}

</style>
@endpush

@section('content')
<div class="container">
    <ol class="breadcrumb row">
      <li><a href="/wechat">会员卡</a></li>
      <li><a href="/wechat/health">我的检测</a></li>
      <li><a href="/wechat/health/info/{{$experiment_data_id}}">检测中心</a></li>
      <li class="active">检测进度</li>
    </ol>
    <section id="cd-timeline" class="cd-container">
        @foreach($progress as $key=>$val)
            <div class="cd-timeline-block">
              	<div class="cd-timeline-img cd-complete">
                    <span class="icon glyphicon glyphicon-ok"></span>
             	</div>
              	<div class="cd-timeline-content">
              		<h4>{{$val->name}}&nbsp;&nbsp;<span class="cd-date">{{$val->created_at}}</span></h4>                               		
              	</div>
            </div>
        @endforeach
        @foreach($noProgress as $key=>$val)
            <div class="cd-timeline-block unfinished">
            	<div class="cd-timeline-img cd-unfinished">
                  	<i class="fa fa-minus"></i>
            	</div>
            	<div class="cd-timeline-content">
            		<h4>{{$val->name}}<span class="cd-date">&nbsp;&nbsp;(尚未完成)</span></h4>     			
            	</div>
            </div>
        @endforeach
    </section>
</div>
@endsection

@section('scripts')
@parent
<script>
  $(function(){
    $(".cd-complete").last().parent().addClass('last');
    }
  )  
</script>
@endsection