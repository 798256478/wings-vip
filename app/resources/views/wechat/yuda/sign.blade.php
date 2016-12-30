@extends('wechat.default.master')

@section('title', '签到')

@push('links')
<style type="text/css">

html,
body {
   height: 100%;
}

body {
   background: url("{{ $theme->getWechatImage('sign_background.png') }}");
}

.header {
   height: 70px;
   padding: 10px 15px;
   color: #fff;
   background: #3687d6;
}

.header img {
   position: absolute;
   width: 70px;
   height: 70px;
   border: 2px solid #fff;
   border-radius: 100%;
   box-shadow: 0 3px 0 #3687d6;
}

.header .name,
.header .bonus {
   display: inline-block;
   margin: 5px 0 5px 85px;
}

.header .pull-right {
   margin-top: 5px;
   color: #fff;
}

.continuous-group {
   height: 250px;
   display: -webkit-box;
   -webkit-box-align: center;
   -webkit-box-pack: center;
}

.continuous,
.continuous-next {
   min-width: 130px;
   padding: 15px;
   color: #fff;
   border: 2px solid #fff;
   border-radius: 4px;
   transition: ALL .4s;
   transform: rotateX(0deg);
   transform-origin: 0 0;
   text-align: center;
}

.continuous-next {
   display: none;
}

.continuous .days,
.continuous-next .days {
   margin-right: 3px;
   font-size: 60px;
}

.sign-step {
   overflow: hidden;
   width: 100%;
   height: 140px;
   padding-top: 64px;
   padding-left: 15px;
}

.sign-step ul {
   list-style-type: none;
   width: 2000px;
   padding: 0;
}

.sign-step ul li {
   float: left;
   position: relative;
   width: 12px;
   color: #fff;
   text-align: center;
}

.sign-step ul li .number {
   margin-left: .5px;
}

.sign-step ul li .ball {
   float: left;
   width: 12px;
   height: 12px;
   margin-bottom: 3px;
   border: 2px solid #fff;
   border-radius: 100%;
}

.sign-step ul li.signed-step .ball {
   border: 2px solid yellow;
}

.sign-step .fa-map-marker {
   position: relative;
   top: -3px;
   color: yellow;
   font-size: 16px;
}

.sign-step ul li .info {
   position: absolute;
   top: -40px;
   left: -6px;
   width: 25px;
   height: 25px;
   opacity: 1;
   color: gold;
   border: 3px solid gold;
   border-radius: 100%;
   background: sandybrown;
   box-shadow: 0 1.5px 0 sandybrown;
   text-shadow: 0 2px 0 sandybrown;
   font-size: 14px;
}

.sign-step ul li .info .fa-btc {
   position: relative;
   top: -1px;
}

.signed-step img {
   position: relative;
   top: 5px;
   left: -9px;
   width: 30px;
   height: 30px;
   border: 2px solid #fff;
   border-radius: 100%;
   transition: ALL .6s;
}

.sign-step ul span.progress {
   display: inline-block;
   float: left;
   width: 40px;
   height: 2px;
   margin-top: 5px;
   background: #fff;
}

.sign-step ul span.sign-progress {
   background: yellow;
}

.sign-button-group {
   margin: 40px 0;
}

.sign-button-group .sign-button {
   display: -webkit-box;
   width: 150px;
   height: 40px;
   margin: 20px auto;
   color: green;
   background: yellow;
   box-shadow: 0 4px 5px gold;
   font-size: 18px;
   font-weight: 700;
   -webkit-box-align: center;
   -webkit-box-pack: center;
}

.sign-button-group .signed {
   color: #fff;
   font-size: 20px;
   text-align: center;
}

.sign-button-group .sign-button i {
   margin-left: 15px;
}

</style>
@endpush

@section('content')
	<div class="container">
      <div class="row header">
         <img src="{{$card_info['headimgurl']}}" class="pull-left">
         <span class="name">{{$card_info['nickname']}}</span><br>
         <span class="bonus">积分:<label>{{$card_info['bonus']}}</label></span>
         {{--<a href="/wechat/mall" class="pull-right">前往商城>></a>--}}
      </div>
      <div class="continuous-group">
         <div class="continuous">
            <div>已连续签到</div>
            <label class="days">{{$continuousDays}}</label>天
         </div>
         <div class="continuous-next">
            <div>已连续签到</div>
            <label class="days">{{$continuousDays+1}}</label>天
         </div>
      </div>
      <div class="row">
         <div class="sign-step">
            <ul>
               @if($signData)
                  @for($i=0;$i<intval($continuousDays);$i++)
                  <div class="step-group step{{$i}}">
                     <li class="signed-step">
                        <span class="ball"></span>
                        <span class="number">{{$i+1}}</span>
                        <span class="info"><label><i class="fa fa-btc"></i></label></span>
                     </li>
                     <span class="progress sign-progress"></span>
                  </div>
                  @endfor
               @endif
               @for($i=1;$i<10;$i++)
               <li class="no-sign-step">
                  <span class="ball sign"></span>
                  <span class="number">{{$continuousDays+$i}}</span>
               </li>
               <span class="progress"></span>
               @endfor
            </ul>
         </div>
      </div>
      <div class="sign-button-group">
         <span class="sign-button" onclick="sign()" style="display:{{count($todayData)?'none':'-webkit-box'}}">
            <span class="sign-text">签到</span>
            <i class="fa fa-pencil"></i>
         </span>
         <div style="display:{{count($todayData)?'block':'none'}}" class="signed">
            <span>今日已签到</span>
         </div>
      </div>
	</div>
@endsection

@section('scripts')
   @parent
   <script type="text/javascript">
   $(function() {
      $('.sign-progress').last().css('background', '#fff');
      $('.signed-step').last().children('.ball').replaceWith("<i class='fa fa-map-marker'></i>");
      if (<?php echo $continuousDays>4?1:0;?>) {
         for(var i =0; i< <?php echo intval($continuousDays)-8 ;?>; i++){
            $(".step" + i).remove();
         }
         if (<?php echo $continuousDays<8?1:0;?>) {
            $('.sign-step ul').css('margin-left', '-'+ <?php echo $continuousDays * 20;?> +'px');
         } else {
            $('.sign-step ul').css('margin-left', '-'+ 200 +'px');
         }
      }
      if (<?php echo $continuousDays>10?1:0;?>) {
         $('.number').css('margin-left', '-'+ parseInt($('.number').css("width"))/5 +'px');
      }

      $('.signed-step').last().append("<img src='<?php echo $card_info['headimgurl'];?>'>");
   })

   function sign() {
      $.ajax({
         type: 'post',
         url: '/wechat/api/attendance',
         success: function(data) {
            $('.sign-button').css('display', 'none');
            $('.signed').css('display', 'block');
            $('.no-sign-step').first().append('<span class="info"><label><i class="fa fa-btc"></i></label></span>');
            $('.bonus label').html(data['card']['bonus']);
            $('.no-sign-step').first().removeClass('no-sign-step').addClass('signed-step');
            $('.sign-progress').last().css('background', 'yellow');
            $('.continuous').css('transform', 'rotateX(270deg)');
            $('.continuous-next').css('display', 'inline-block');
            $('.signed-step').last().children('.ball').replaceWith("<i class='fa fa-map-marker'></i>");
            $('.signed-step').last().siblings().children('i').replaceWith("<span class='ball'></span>");
            if ($('.sign-step img').length > 0) {
               $('.signed-step img').css('left', '42px');
            } else {
               $('.signed-step').last().append("<img src='<?php echo $card_info['headimgurl']?>'>");
            }
         },
      });
   }
   </script>
@endsection