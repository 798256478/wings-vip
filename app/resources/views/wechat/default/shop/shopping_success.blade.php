@extends('wechat.default.master')

@section('title', '购买成功')

@push('links')
<style type="text/css">
    .container{
        padding: 0;
    }

    body{
        background-color: #eee;
    }

    .group{
        background-color: #fff;
        margin-bottom: 10px;
        padding: 19px 19px;

    }

    /*正文*/
    .success-div .main-div{
        height: 70px;
        line-height: 70px;
    }

    .success-div .main-div .icon-span{
        font-size: 66px;
        height: 70px;
        display: inline-block;
        margin-left: 20px;
        color: #009E10;
        width: 75px;
    }

    .success-div .main-div .main-span{
        font-size: 23px;
        height: 70px;
        display: inline-block;
        vertical-align: top;
        font-weight: 600;
    }

    .detail-div{
        margin-bottom: 10px;
    }

    .detail-div .detail-key{
        margin-right: 5px;
        margin-left: 100px;
    }

    .detail-div .detail-val{
        color: #009E10;
    }

    .detail-div .detail-val .fa-btc{
        position: relative;
        top:-1px;
    }

    .button-div{
        margin-top: 30px;
        margin-bottom: 30px;
    }

    .button-div a{
        display: inline-block;
    }

    .button-div a:first-child{
        margin-left: 100px;
    }

    .button-div a:last-child{
        margin-left: 30px;
    }

    /*提示*/
    .hint-div .hint-key-div{
        float: left;
    }

    .hint-div .hint-val-div{
        float: left;
        margin-left: 5px;
        color: #888888;
    }
</style>
@endpush

@section('content')
<div class="container">
	<div class="group success-div">
        <div class="main-div">
            <span class="icon-span">
                <i class="fa fa-check-circle"></i>
            </span>
            <span class="main-span">恭喜您，购买成功</span>
        </div>
        <div class="detail-div">
            <span class="detail-key">支付方式:</span>
            <span class="detail-val">
                @foreach($order['orderPayments'] as $key=>$val){{$val->name}}支付 @endforeach</span>
        </div>
        <div class="detail-div">
            <span class="detail-key">订单金额:</span>
            <span class="detail-val">
                @if($order['money_pay_amount']>0)
                    <i class="fa fa-cny" ></i>{{$order['money_pay_amount']}}
                @endif
                @if($order['money_pay_amount']>0 && $order['bonus_pay_amount']>0)
                    +
                @endif
                @if($order['bonus_pay_amount']>0)
                    <i class="fa fa-btc" ></i>{{$order['bonus_pay_amount']}}
                @endif
            </span>
        </div>
        <div class="button-div clearfix">
            <a href="/wechat/order/{{$order['id']}}" class="button-one">查看订单</a>
            <a href="/wechat/mall" class="button-two">返回首页</a>
        </div>
    </div>
	<div class="group hint-div clearfix">
        <div class="hint-key-div">
            <span>友情提示:</span>
        </div>
        <div class="hint-val-div">
            <span>我们不会以订单异常、系统升级等理由,要求您点击任何链接进行退款操作。</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
	    $(".hint-val-div").width($(".hint-div").width()-$(".hint-key-div").width()-6+'px');
    </script>
@endsection