@extends('wechat.default.master')

@section('title', '退款申请')

@push('links')
<style type="text/css">

body {
   background: #eee;
}

.member-info-group {
   margin-bottom: 20px;
   padding: 0 15px 0 15px;
   background-color: #fff;
}

.member-info {
   min-height: 50px;
   padding-top: 15px;
   border-top: 1px solid #eee;
}

.member-info .title {
   float: left;
}

.member-info input,
#reason_text {
   margin-left: 15px;
   border: 0;
}

.member-info .reason i {
   font-size: 20px;
}

#select .reasonSelect {
   min-height: 50px;
   padding-top: 0;
   border-top: 1px solid #eee;
   line-height: 50px;
}

.information {
   margin-left: 75px;
   color: #999;
}

.submit {
   width: 100%;
   height: 40px;
   margin: 0;
   color: #fff;
   border: 0;
   background: tomato;
   font-size: 24px;
   line-height: 40px;
}

</style>
@endpush
@section('content')
<div class="formMain" style="display:;">
   <form action="" id="refundForm" method="post">
      <div class="container">
            <div class="member-info-group row">
               <div class="member-info people">
                  <label class="title">联&nbsp;&nbsp;系&nbsp;&nbsp;人:</label>
                  <input type="text" name="name" value="{{$card['order']['card']['name']}}" id='name'>
               </div>
               <div class="member-info phone">
                  </span><label class="title">联系电话:</label>
                  <input type="text" name="phone" value="{{$card['order']['card']['phone']}}" id="phone">
               </div>
               <div class="member-info reason">
                  <label class="title">退款原因:</label>
                  <span id='reason_text'></span>
                  <input type="hidden" name="reason" id="reason">
                  <i class="fa fa-2x fa-angle-right pull-right"></i>
               </div>
            </div>
            <div class="member-info-group row">
               <div class="member-info">
                  <label class="title">友情提示:</label>
                  <p class="information">您的退货请求提交完成之后，客服会主动与您联系，协商解决</p>
               </div>
            </div>
            <input type="hidden" value="{{$order_id}}" name="order_id">
            <input type="hidden" value="{{$order_detail_id}}" name="order_detail_id">
      </div>
      <input type="submit" value="提交申请" class="submit"> 
   </form>
</div>
<div id="select" style="display: none;">
   <div class="container">
      <div class="member-info-group row">
         @foreach($refund_reason['refund_reason'] as $k=>$v)
         <div class="reasonSelect" value="{{$k}}">{{$v}}</div>
         @endforeach
      </div>
   </div>
</div>
@endsection
@section('scripts')
   @parent
   <script type="text/javascript">
      $('.reason').click(function() {
         $('.formMain').css('display', 'none');
         $("#select").css('display', 'block');
         window.history.pushState({
            page: 1
         }, "title 1", "?select");
      });

      $(window).on('popstate', function() {
         if ($('.formMain').css('display') == 'none') {
            $('.formMain').css('display', 'block');
            $("#select").css('display', 'none');
            window.history.pushState(null, null);
            history.back();
         }
      });

      $('.reasonSelect').click(function() {
         var select = $(this).html();
         var reason = $(this).attr('value');
         $('#reason_text').html(select);
         $('#reason').val(reason);
         $('.formMain').css('display', 'block');
         $("#select").css('display', 'none');
         window.history.pushState(null, null);
         history.go(-2);
      });

      var inputVerify = function() {
         if (!$("#name").val()) {
            alert('请输入联系人');
            return false;
         }
         if (!$("#phone").val()) {
            alert('请输入联系电话');
            return false;
         } else {
            var phone = $("#phone").val();
            var reg = /^1\d{10}$/;
            if (!reg.exec(phone)) {
               alert("手机号格式不正确！");
               return false;
            }
         }
         if (!$("#reason").val()) {
            alert('请选择退款原因');
            return false;
         }
         return true;
      };

      $("#refundForm").submit(function() {
         if (!inputVerify()) {
            return false;
         }
         $data = $('#refundForm').serialize();
         $.ajax({
            type: "POST",
            url: "/wechat/order/refund",
            data: $('#refundForm').serialize(),
            error: function(data) {
               alert(data.responseText);
               return false;
            },
            success: function(data) {
               alert('申请已提交');
               window.location.href = "/wechat/order";
            }
         });
         return false;
      });
   </script>
@endsection