@extends('wechat.default.master')

@section('title', '会员信息')

@push('links')
<style type="text/css">
    body {
        background-color: #eee;
    }

    form {
        text-align: center;
    }

    h4 {
        text-align: center;
        margin-top: 30px;
        margin-bottom: 5px;
    }

    .form-group {
        margin-top: 30px;
    }

    #keyButton,
    #submitButton {
        background-color: {{ $theme->colors['THEME'] or '#0092DB' }};
        border-color: {{ $theme->colors['THEME'] or '#0092DB' }};
    }

    #keyButton:disabled,
    #submitButton:disabled {
        background-color: #aaa;
        border-color: #aaa;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <ol class="breadcrumb row">
            <li><a href="/wechat">会员卡</a></li>
            <li><a href="/wechat/member_info">会员信息</a></li>
            <li class="active">身份验证</li>
        </ol>

        <h4>设置口令需验证身份</h4>

        <div class="row">
            <div class="col-xs-2"></div>
            <form action="/wechat/member_info/verifyWord" method="post" onSubmit="return check();" class="col-xs-8">
                @if(isset($card_info->mobile) && $card_info->mobile != null && $card_info->mobile != "")
                    @if(isset($card_info->pin) && $card_info->pin != null && $card_info->pin != "")
                        <div class="form-group">
                            <input type="text" class="form-control" id="word" name="word" placeholder="口令" value="">
                            <div>
                                @if (count($errors) > 0)
                                    {{$errors->first('word')}}
                                @endif
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:-7px;margin-bottom:-22px;">
                            <span style="color:#AAA;">或</span>
                        </div>
                    @endif
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" id="key" name="key" placeholder="验证码">
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-info theme-backcolor" id="keyButton">获取验证码</button>
                            </span>
                        </div>
                        <div>
                            @if (count($errors) > 0)
                                {{$errors->first('key')}}
                            @endif
                        </div>
                    </div>
                    <div class="form-group">
                        <button id="submitButton" type="submit" class="btn btn-primary theme-backcolor form-control" 
                                disabled>提交
                        </button>
                    </div>
                    @if(Session::has('message'))
                        <label>{{session('message')}}</label>
                    @endif
                @else
                    <div class="form-group">
                        <p>只有绑定手机后才能设置口令。</p><a href="/wechat/member_info/phone">去绑定手机</a>
                    </div>
                @endif
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
        function onKeyChange(){
            if ($("#key").val() != "") {
                $('#submitButton').removeAttr('disabled');
                $("#word").val('');
            } else {
                if ($("#word").val() == "") {
                    $('#submitButton').attr('disabled', 'true');
                }
            }
        }

        function onWordChange(){
            if ($("#word").val() != "") {
                $('#submitButton').removeAttr('disabled');
                $("#key").val('');
            } else {
                if ($("#key").val() == "") {
                    $('#submitButton').attr('disabled', 'true');
                }
            }

        }

        function check() {
            if (($("#word").val() == null || $("#word").val() == "") && ($("#key").val() == null || $("#key").val() == "")) {
                alert("至少选择一种验证方式");
                return false;
            }
        }

        $(function () {
            @if(isset($card_info->mobile) && $card_info->mobile != null && $card_info->mobile != "")
                @if(isset($card_info->pin) && $card_info->pin != null && $card_info->pin != "")
                    $("#word").keyup(onWordChange);
                    $("#word").on('input paste',onWordChange);
                @endif
                $("#key").keyup(onKeyChange);
                $("#key").on('input paste',onKeyChange);

                $('#keyButton').click(function () {
                    var data = {
                        mobile: {{$card_info->mobile}},
                        type: 'word'
                    };
                    $.ajax({
                        url: '/wechat/api/sendSms',
                        type: 'POST',
                        data: data,
                        error: function (data) {
                            alert('发送失败,请稍后再试');
                            $('#keyButton').removeAttr('disabled');
                        },
                        beforeSend:function(){
                            $('#keyButton').attr('disabled', 'true');
                        },
                        success: function (data) {
                            var count = 60;
                            var countdown = setInterval(CountDown, 1000);
    
                            function CountDown() {
                                $("#keyButton").attr("disabled", true);
                                $("#keyButton").html(count);
                                if (count == 0) {
                                    $("#keyButton").html("获取验证码").removeAttr("disabled");
                                    clearInterval(countdown);
                                }
                                count--;
                            }
                        }
                    });
                });
            @endif
        })
    </script>
@endsection