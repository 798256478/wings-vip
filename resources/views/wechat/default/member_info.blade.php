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

    #name-save,#password-save {
        float: right;
        margin: 10px 5px 0 5px;
    }

    input{
        color: #777;
    }

    .theme-color{
        color: {{ $theme->colors['THEME'] or '#0092DB' }};
    }

    .mbsc-mobiscroll .dwb{
        color: {{ $theme->colors['THEME'] or '#0092DB' }};
    }

    .mbsc-mobiscroll .dwwol{
        border-top-color: {{ $theme->colors['THEME'] or '#0092DB' }};
        border-bottom-color: {{ $theme->colors['THEME'] or '#0092DB' }};
    }

    #word-div,#word-div label{
        cursor:pointer;
    }

    .first{
        margin-top: 20px;
    }

    .first .hint{
        color: #999;
    }

    .first a{
        float: right;
        margin-top: 10px;
        margin-right: 10px;
        color: {{ $theme->colors['THEME'] or '#0092DB' }};
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
                <label>手机</label>
                <input id="mobile" readonly class="list-input" placeholder="填写手机号，立获优惠券" value="{{ $card_info['mobile'] or '' }}">
            </div>
        </div>
        <div class="member-info-group row">
            <div class="member-info">
                <label>姓名</label>
                <button type="button" id="name-save" style="display:none" class="btn btn-sm btn-primary">保存</button>
                <input id="name" class="list-input" placeholder="未设置" value="{{$card_info['name'] or '' }}">
            </div>
            {{--yuda使用--}}
            <div class="member-info">
                <label>密码</label>
                <button type="button" id="password-save" style="display:none" class="btn btn-sm btn-primary">保存</button>
                <input id="password" class="list-input" placeholder="未设置" value="{{$card_info['password'] or '' }}">
            </div>
            <div class="member-info">
                <label>性别</label>
                <select id="sex" readonly class="list-input">
                    <option value="0" @if ($card_info['sex']==0) selected @endif disabled >请选择性别</option>
                    <option value="1" @if ($card_info['sex']==1) selected @endif >男</option>
                    <option value="2" @if ($card_info['sex']==2) selected @endif >女</option>
                </select>
            </div>
            <div class="member-info">
                <label>生日</label>
                <input id="birthday" readonly class="list-input" placeholder="生日输入后无法修改"
                @if($card_info['birthday'] != "" && $card_info['birthday'] != null)
                    value="{{$card_info['birthday']->format('Y/m/d')}}"
                @endif
                >
            </div>
        </div>
        <div class="member-info-group row">
            <div class="member-info" id="word-div">
                <label>口令</label>
            <span class="list-input theme-color">
                @if(isset($card_info) && $card_info['pin'] != "" && $card_info['pin'] != null)
                    已设置
                @else
                    建议开启消费更安全
                @endif
            </span>
            </div>
        </div>
        <div class="first">
            <div class="hint">填写手机号,店内消费可直接使用手机号</div>
            <a href="/wechat">以后再说</a>
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
        $(function () {
            @if(isset($card_info) && ($card_info->birthday == null || $card_info->birthday == ""))
            function birthday(){
                $('#birthday').mobiscroll().date({
                    display: "bottom",
                    lang: "zh",
                    onSelect: function (valueText, inst) {
                        var val = $('#birthday').val();
                        if (confirm("是否要将生日设置为：" + val + "\r\n设置后无法修改")) {
                            $.ajax({
                                url: '/wechat/api/member_info',
                                type: 'PUT',
                                data: "birthday=" + $('#birthday').val(),
                                beforeSend:function(){
                                    $("#birthday").unbind();
                                },
                                error: function (data) {
                                    alert('保存失败');
                                    birthday();
                                },
                                success: function (data) {
                                    $("#birthday").unbind();
                                    $("#birthday").click(function () {
                                        alert("生日不能修改");
                                    });
                                }
                            });
                        } else {
                            $('#birthday').val('生日设置后无法修改');
                        }
                    }
                });
            }
            birthday();
            @else
            $("#birthday").click(function(){
                alert("生日不能修改");
            });
            @endif
            
            $('#sex').mobiscroll().select({
                display: "bottom",
                lang: "zh",
                onSelect: function (valueText, inst) {
                    $.ajax({
                        url: '/wechat/api/member_info',
                        type: 'PUT',
                        data: "sex=" + inst.getVal(),
                        error: function (data) {
                            alert("保存失败");
                        }
                    });
                }
            });

            $("#mobile").click(function () {
                window.location.href = "/wechat/member_info/phone";
            });

            $("#name").focus(function () {
                $("#name-save").show();
            });

            $("#password").focus(function () {
                $("#password-save").show();
            });

            $("#name-save").click(function () {
                $("#name-save").hide();
                $.ajax({
                    url: '/wechat/api/member_info',
                    type: 'PUT',
                    data: "name=" + $("#name").val(),
                    error: function (data) {
                        alert('保存失败');
                    }
                });
            });

            $("#password-save").click(function () {
                $("#password-save").hide();
                $.ajax({
                    url: '/wechat/api/member_info',
                    type: 'PUT',
                    data: "password=" + $("#password").val(),
                    error: function (data) {
                        alert('保存失败');
                    }
                });
            });

            $("#word-div").click(function () {
                window.location.href = "/wechat/member_info/verify";
            });
        });
    </script>
@endsection