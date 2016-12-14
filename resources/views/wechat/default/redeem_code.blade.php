@extends('wechat.default.master')

@section('title', '兑换码')

@push('links')
<style type="text/css">

	.form-control{
		margin-top: 15px;
	}

	.redeem-btn, .redeem-btn:focus, .redeem-btn:hover {
        margin-top: 15px;
        width: 100%;
		background-color: {{ $theme->getColor('BUTTON1') }};
        color:#fff;
        outline:none;
	}
    
    .redeem-btn[disabled], .redeem-btn[disabled]:focus, .redeem-btn[disabled]:hover{
		background-color: #aaa;
        color:#fff;
	}

	form{
		margin-top: 40px;
	}

	input[type=text]{
		background-image:url("{{ $theme->getWechatImage('redeem-tag.jpg') }}");
		background-repeat:no-repeat;
		background-size:30px 22px;
		background-position:10px 6px;
		padding-left: 45px;
	}
    
</style>
@endpush

@section('content')
<div class="container">
	<ol class="breadcrumb row">
		<li><a href="/wechat">会员中心</a> </li>
		<li class="active">兑换码</li>
	</ol>
	<div class="row">
		<div class="col-xs-10  col-xs-offset-1">
			
			<form>
				<input type="text" id="code" placeholder="请输入兑换码" class="form-control"></input>
				<button class="btn redeem-btn" disabled=true onclick="redeem();return false;">兑换</button>
				<span id="card_code" class="hidden">{{ $card_info->card_code}}</span>
			</form>
		</div>
	</div>
    
</div>
@endsection

@section('scripts')
    @parent
    
    <script type="text/javascript">
	    function redeem ()
        {
            $('.loading').show();
            
            $.post('/wechat/api/redeem_code' , {

                code: $("#code").val()
                
            }).success(function(data){
                
                if (data.status == 'SUCCEED')
                    swal(data.title, data.message, "success");
                else{
                    swal({   
                        title: data.title,   
                        text: data.message,   
                        imageUrl: "{{ $theme->getWechatImage('empty-box.jpg') }}" 
                    });
                }
                
                $('.loading').hide();
                $("#code").val('');
                refreshButton();
                
            }).error(function(){
                swal("异常", "系统出现异常，请与服务人员联系。", "warning");
                
                $('.loading').hide();
                $("#code").val('');
                refreshButton();
            });
        }
        
        function refreshButton()
        {
            var newkey = $("#code").val();
            if (newkey != ""){
                $(".btn").attr('disabled', false);
            }
            else{
                $(".btn").attr('disabled', true);
            }
        }


        $(function(){
            $("#code").keyup(function(){
                refreshButton();
            });
            $("#code").on('input paste',function(){
                refreshButton();
            });
            
        });
    </script>
@endsection