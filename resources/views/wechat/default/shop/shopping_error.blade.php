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
		<span class="col-xs-4 col-xs-offset-2 back-one">返回上一级</span>
		<span class="col-xs-4 back-all">商城首页</span>
	</div>
</div>
@endsection

@section('scripts')
    @parent
    <script type="text/javascript">
		$(function(){
			$(".back-one").click(function(){
				history.back();
			});
			$(".back-all").click(function(){
				window.location.href="/wechat/shopping";
			});
		})
    </script>
@endsection