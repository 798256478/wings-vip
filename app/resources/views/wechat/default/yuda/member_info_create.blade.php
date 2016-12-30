@extends('wechat.default.master')

@section('title', '会员信息')

@push('links')
<link href="/components/mobiscroll/css/mobiscroll.animation.css" rel="stylesheet" type="text/css"/>
<link href="/components/mobiscroll/css/mobiscroll.frame.css" rel="stylesheet" type="text/css"/>
<link href="/components/mobiscroll/css/mobiscroll.scroller.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
    body {
        background-color: #eee;
    }

    .member-info-group{
        margin-top: 15px;
        background-color: #fff;
        padding: 0 15px 0 15px;}

    .member-info{
        height: 48px;
        line-height: 48px;
        border-top: 1px solid #eee;
    }

    .member-info:first-child{
        border-top: none;
    }

    .member-info .list-input, .mbsc-control-ev{
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

    #key-button {
        float: right;
        margin: 10px 5px 0 5px;
    }

    input{
        color: #777;
    }

    .mbsc-mobiscroll .dwb{
        color: {{ $theme->colors['THEME'] or '#0092DB' }};
    }

    .mbsc-mobiscroll .dwwol{
        border-top-color: {{ $theme->colors['THEME'] or '#0092DB' }};
        border-bottom-color: {{ $theme->colors['THEME'] or '#0092DB' }};
    }

    .button-group{
        margin-top: 20px;
        width: 100%;
    }

    .button-group .hint{
        color: #999;
    }

    .button-group .button-div{
        margin-top: 40px;
        width: 90%;
        margin-left: 5%;
    }

    .button-group .button-div button{
        width: 100%;
    }

    .button-group .button-div #save-button{
        margin-top: 12px;
    }

</style>
@endpush

@section('content')
    <div class="container">
        <ol class="breadcrumb row">
            <li><a href="/wechat">会员卡</a></li>
            <li class="active">会员信息</li>
        </ol>
        <div class="member-info-group phone row">
            <div class="member-info">
                <label>手&nbsp;&nbsp;&nbsp;&nbsp;机</label>
                <button type="button" id="key-button" style="display:none" class="btn btn-sm btn-primary">验证码</button>
                <input id="mobile" class="list-input" placeholder="填写手机号，立获优惠券" >
            </div>
            <div class="member-info">
                <label>验证码</label>
                <input id="key" class="list-input" placeholder="验证码" >
            </div>
        </div>
        <div class="member-info-group row">
            <div class="member-info">
                <label>姓&nbsp;&nbsp;&nbsp;&nbsp;名</label>
                <input id="name" class="list-input" placeholder="未设置" value="{{$card_info['name'] or '' }}">
            </div>
            <div class="member-info">
                <label>性&nbsp;&nbsp;&nbsp;&nbsp;别</label>
                <select id="sex" readonly class="list-input">
                    <option value="0" disabled >请选择性别</option>
                    <option value="1" >男</option>
                    <option value="2" >女</option>
                </select>
            </div>
            <div class="member-info">
                <label>生&nbsp;&nbsp;&nbsp;&nbsp;日</label>
                <input id="birthday" readonly class="list-input" placeholder="生日输入后无法修改">
            </div>
        </div>
        <div class="member-info-group row">
            <div class="member-info">
                <label>密&nbsp;&nbsp;&nbsp;&nbsp;码</label>
                <input id="password" class="list-input" placeholder="未设置(只允许数字)">
            </div>
        </div>
        <div class="button-group">
            <div class="hint">小提示:填写手机号,店内消费可直接使用手机号</div>
            <div class="button-div">
                <button id="skip-button" class="btn btn-danger ">跳过</button>
                <button id="save-button" class="btn btn-success ">提交</button>
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

        function validateMobile() {
            var mobile = $("#mobile").val();
            var reg = /^1\d{10}$/;
            if (reg.exec(mobile)) {
                return true;
            }else{
                alert("手机号格式不正确");
                return false;
            }
        }

        function validateData(){
            var data = {};
            data.mobile = $("#mobile").val();
            data.key = $("#key").val();
            data.name = $("#name").val();
            data.sex = $("#sex").val();
            data.birthday = $("#birthday").val();
            data.password = $("#password").val();
            if(data.mobile){
                if(!validateMobile()){
                    return false;
                }
                if(data.key.length != 6 || !$.isNumeric(data.key)){
                    alert('验证码为六位数字');
                    return false;
                }
            }
            if(data.name && data.name.length>10){
                alert('名字不能超过十位');
                return false;
            }
            if(data.password.length>0 && !$.isNumeric(data.password)){
                alert('密码只能是数字');
                return false;
            }
            return data;
        }

        $(function () {
            $('#birthday').mobiscroll().date({
                display: "bottom",
                lang: "zh",
            });

            $('#sex').mobiscroll().select({
                display: "bottom",
                lang: "zh",
            });

            $("#mobile").focus(function () {
                $("#key-button").show();
            });

            $("#key-button").click(function(){
                if (validateMobile()) {
                    var data = {
                        mobile: $("#mobile").val(),
                        type: 'mobile'
                    };

                    $.ajax({
                        url: '/wechat/api/sendSms',
                        type: 'POST',
                        data: data,
                        beforeSend:function(){
                            $('#key-button').attr('disabled', 'true');
                        },
                        error: function (data) {
                            if (data.responseText){
                                alert(data.responseText);
                            }
                            $('#key-button').removeAttr("disabled");
                        },
                        success: function (data) {
                            var count = 60;
                            var countdown = setInterval(CountDown, 1000);

                            function CountDown() {
                                $("#key-button").attr("disabled", true);
                                $("#key-button").html(count);
                                if (count == 0) {
                                    $("#key-button").html("验证码").removeAttr("disabled");
                                    clearInterval(countdown);
                                }
                                count--;
                            }
                        }
                    });
                }
            });

            $("#save-button").click(function () {
                var data = validateData();
                if(data){
                    $.ajax({
                        url: '/wechat/api/member_info',
                        type: 'PUT',
                        data: data,
                        beforeSend:function(){
                            $('#save-button').attr('disabled', 'true');
                        },
                        success:function(){
//                            alert('成功');
//                            $('#save-button').removeAttr("disabled");
                            window.location.href = "/wechat";
                        },
                        error: function (data) {
                            alert(data.responseText);
                            $('#save-button').removeAttr("disabled");
                        }
                    });
                }
            });

            $("#skip-button").click(function(){
                window.location.href = "/wechat";
            });
        });
    </script>
@endsection