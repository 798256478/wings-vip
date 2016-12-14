@extends('wechat.default.master')

@section('title', '会员信息')

@push('links')
<style type="text/css">
    body {
        background-color: #eee;
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
            <li><a href="/wechat/health/detections">我的检测</a></li>
            <li><a href="/wechat/health/detection/{{$code}}/info">检测中心</a></li>
            <li><a href="/wechat/health/detection/{{$code}}/userInfo">个人信息</a></li>
            <li class="active">绑定手机</li>
        </ol>

        <form id="form1" action="/wechat/health/detection/phone" method="POST">
            <div class="form-group">
                <label for="mobileInput">输入新手机</label>
                <input type="text" class="form-control" id="mobileInput" name="mobile" placeholder="手机号"
                    value="@if(Session::has('message')){{session('message.mobile')}}@endif">
                <div>
                @if (count($errors) > 0)
                <span class="error">
                    {{$errors->first('mobile')}}
                </span>
                @endif
                </div>

            </div>
            <div class="form-group">
                <div class="input-group">
                    <input type="text" class="form-control" id="keyInput" name="key" placeholder="验证码">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-info grey" id="keyButton" disabled style="width: 100%">获取验证码
                        </button>
                    </span>
                </div>
                @if (count($errors) > 0)
                <span class="error">
                    {{$errors->first('key')}}
                </span>
                @endif
            </div>
            <input type="hidden" name="id" value="{{$id}}">
            <input type="hidden" name="code" value="{{$code}}">
            <input type="hidden" name="card_code" value="{{$card_info->card_code}}">
            @if(Session::has('message'))
                <label>{{session('message.info')}}</label>
            @endif
            <button id="submitButton" type="submit" disabled class="btn btn-primary grey form-control">提交</button>
            {{ method_field('PUT') }}
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
        function onKeyChanged() {
            if ($("#keyInput").val() != "") {
                if ($("#mobileInput").val() != "") {
                    $('#submitButton').removeAttr("disabled"); 
                }
            } else {
                $('#submitButton').attr('disabled', 'true');
            }
        }

        function onMobileChanged() {
            if ($("#mobileInput").val() != "") {
                $('#keyButton').removeAttr("disabled"); 
            } else {
                $('#keyButton').attr('disabled', 'true');
            }
        }

        function validateMobile() {
            var mobile = $("#mobileInput").val();
            var reg = /^1\d{10}$/;
            if (reg.exec(mobile)) {
                return true;
            }else{
                alert("手机号格式不正确");
                return false;
            }
        }

        function validateKey() {
            var key = $("#keyInput").val();
            if (key.length==6) {
                return true;
            } else {
                alert("验证码为6位数字");
                return false;
            }
        }

        function onKeyButtonClick(){
            if (validateMobile()) {
                var data = {
                    mobile: $("#mobileInput").val(),
                    type: 'mobile',
                    id: {{$id}}
                }

                $.ajax({
                    url: '/wechat/health/detection/detectionSendSms',
                    type: 'POST',
                    data: data,
                    beforeSend:function(){
                        $('#keyButton').attr('disabled', 'true');
                    },
                    error: function (data) {
                        if (data.responseText){
                            alert(data.responseText);
                        }
                        $('#keyButton').removeAttr("disabled"); 
                    },
                    success: function (data) {
                        var count = 60;
                        var countdown = setInterval(CountDown, 1000);

                        function CountDown() {
                            $("#keyButton").attr("disabled", true);
                            $("#keyButton").html(count + "秒后重新获取验证码");
                            if (count == 0) {
                                $("#keyButton").html("获取验证码").removeAttr("disabled");
                                clearInterval(countdown);
                            }
                            count--;
                        }
                    }
                });
            }
        }

        $(function () {
            if($("#mobileInput").val().length==11){
                $('#keyButton').removeAttr("disabled"); 
            }

            $("#keyInput").keyup(onKeyChanged);
            $("#keyInput").on('input paste', onKeyChanged);
            $("#mobileInput").keyup(onMobileChanged);
            $("#mobileInput").on('input paste', onMobileChanged);
            $('#keyButton').click(onKeyButtonClick);

            $("#form1").submit(function () {
                if (!validateKey() || !validateMobile()){
                    return false;
                }
            });
        });
    </script>
@endsection