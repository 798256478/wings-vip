@extends('wechat.default.master')

@section('title', '哎呀出错了')

@push('links')
<style type="text/css">
	.img-cont{

	}
	.img-cont img{
		width: 100%;
		height: auto;
	}

	.operation{
		margin-top: 100px;
	}

	.operation span{
		text-align: center;
	}
</style>
@endpush

@section('content')
<div class="container">
	<div class="img-cont clearfix">
		<img class="col-xs-12 " src="/common/imgs/shopping_error.png" alt="">
	</div>
	<div class="operation clearfix">
		<span class="col-xs-4 col-xs-offset-2 back-one">再试一次</span>
		<span class="col-xs-4 back-all">稍后重试</span>
	</div>
</div>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
		$(function(){
			$(".back-one").click(function(){
				window.location.href = "/wechat";
//				history.back();
			});
			$(".back-all").click(function(){
				WeixinJSBridge.invoke('closeWindow',{},function(res){

					//alert(res.err_msg);

				});
//				window.close();
//				window.location.href="/wechat/shopping";
			});
		})
    </script>
@endsection