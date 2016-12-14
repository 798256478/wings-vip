@extends('wechat.default.master')

@section('title', '会员信息')

@push('links')
<style type="text/css">
    body{
        background-color: #eee;
    }

    #submitButton {
        background-color: {{ $theme->colors['THEME'] or '#0092DB' }};
        border-color: {{ $theme->colors['THEME'] or '#0092DB' }};
        margin-top: 20px;
    }

    #submitButton:disabled {
        background-color: #aaa;
        border-color: #aaa;
    }

    input[type="checkbox"] {
        display: none;
    }

    input[type="checkbox"] + label {
        cursor: pointer;
        font-size: 1em;
    }

    #word-div span{
        float: right;
        position: relative;
        left: 28px;
    }

    #wordState + label {
        background-color: #fafbfa;
        padding: 8px;
        border-radius: 50px;
        display: inline-block;
        position: relative;
        margin-right: 30px;
        -webkit-transition: all 0.1s ease-in;
        transition: all 0.1s ease-in;
        width: 50px;
        height: 20px;
    }

    #wordState + label:after {
        content: ' ';
        position: absolute;
        top: 0;
        -webkit-transition: box-shadow 0.1s ease-in;
        transition: box-shadow 0.1s ease-in;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 100px;
        box-shadow: inset 0 0 0 0 #eee, 0 0 1px rgba(0,0,0,0.4);
    }

    #wordState + label:before {
        content: ' ';
        position: absolute;
        background: white;
        top: 1px;
        left: 1px;
        z-index: 100;
        width: 20px;
        -webkit-transition: all 0.1s ease-in;
        transition: all 0.1s ease-in;
        height: 18px;
        border-radius: 100px;
        box-shadow: 0 3px 1px rgba(0,0,0,0.05), 0 0px 1px rgba(0,0,0,0.3);
    }

    #wordState:active + label:after {
        box-shadow: inset 0 0 0 20px #eee, 0 0 1px #eee;
    }

    #wordState:active + label:before {
        width: 25px;
    }

    #wordState:checked:active + label:before {
        width: 25px;
        left: 24px;
    }

    #wordState + label:active {
        box-shadow: 0 1px 2px rgba(0,0,0,0.05), inset 0px 1px 3px rgba(0,0,0,0.1);
    }

    #wordState:checked + label:before {
        content: ' ';
        position: absolute;
        left: 29px;
        border-radius: 100px;
    }

    #wordState:checked + label:after {
        content: ' ';
        font-size: 1.5em;
        position: absolute;
        background: #4cda60;
        box-shadow: 0 0 1px #4cda60;
    }
</style>
@endpush

@section('content')
    <div class="container">
        <ol class="breadcrumb row">
            <li><a href="/wechat">会员卡</a> </li>
            <li><a href="/wechat/member_info">会员信息</a> </li>
            <li class="active">设置口令</li>
        </ol>

        <form action="/wechat/member_info/word"  method="POST" onSubmit="return check();">
            <div class="form-group" id="word-div">
                <label for="mobile">口令状态</label>
                <span>
                    <input type="checkbox" id="wordState" name="wordState" onChange="ckchange();" value="true"
                    @if(isset($card_info) && $card_info->pin != null && $card_info->pin != '' ||count($errors)>0)
                        checked
                    @endif
                    /><label for="wordState"></label>
                </span>
            </div>
            <div class="form-group word-div">
                <label for="mobile">口令</label>
                <input type="text" class="form-control" id="word" name="word" placeholder="口令"
                       value="@if(isset($card_info) && $card_info->pin != null && $card_info->pin != ''){{$card_info->pin}}@endif">
                <div>
                    @if(count($errors)>0)
                        {{$errors->first('word')}}
                    @endif
                </div>
                <input type="hidden" name="_method" value="PUT">
                @if(Session::has('message'))
                    <label>{{session('message')}}</label>
                @endif
            </div>
            <button id="submitButton" type="submit" class="btn btn-primary theme-backcolor form-control">提交</button>



        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
        function onWordChange(){
            if($("#word").val() != ""){
                $('#submitButton').removeAttr('disabled');
            }else{
                $('#submitButton').attr('disabled', 'true');
            }
        }

        function ckchange(){
            if(!$("#wordState").is(":checked")){
                $(".word-div").hide();
                $('#submitButton').removeAttr('disabled');
            }else{
                $(".word-div").show();
                onWordChange();
            }
        }

        function check(){
            if($("#wordState").is(":checked")){
                if($("#word").val() == null || $("#word").val() == ""){
                    alert("启用口令时，口令不能为空");
                    return false;
                }
            }
        }

        $(function () {
            ckchange();
            $("#word").keyup(onWordChange);
            $("#word").on('input paste',onWordChange);
            $("#wordState").on('change',ckchange);
        });
    </script>
@endsection