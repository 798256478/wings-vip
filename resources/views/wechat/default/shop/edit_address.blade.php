@extends('wechat.default.master')

@section('title', $title)

@push('links')
<link href="/components/mobiscroll/css/mobiscroll.animation.css" rel="stylesheet" type="text/css"/>
<link href="/components/mobiscroll/css/mobiscroll.frame.css" rel="stylesheet" type="text/css"/>
<link href="/components/mobiscroll/css/mobiscroll.scroller.css" rel="stylesheet" type="text/css"/>

<link rel="stylesheet" href="/common/css/wechat/default/address.css" type="text/css">
<style type="text/css">
	/*模态框*/
	.modal-dialog{
		width: 70%;
		margin-left: auto;
		margin-right: auto;
	}

	.modal-footer{
		border-top: none;
	}

	.modal-footer a:first-child{
		font-weight: bold;
		border: none;
	}

	.modal-footer a:last-child{
		font-weight: bold;
		color: red;
		border: none;
	}
</style>
@endpush

@section('content')
<div class="container">
	<form name="address">
		<div class="group">
			<div class="one ">
				<label for="name" class="title">收&nbsp;&nbsp;货&nbsp;&nbsp;人</label>
				<input type="text" name="name" id="name" class="list-input"
				       placeholder="收货人姓名" value="{{isset($address['name'])?$address['name'] : ''}}">
			</div>
			<div class="one ">
				<label for="tel" class="title">联系电话</label>
				<input type="text" name="tel" id="tel" class="list-input"
				       placeholder="收货人联系电话" value="{{isset($address['tel'])? $address['tel']: ''}}">
			</div>
			<div class="one ">
				<label for="area" class="title">所在地区</label>
				<input id="area" name="area" placeholder="请选择所在地区"
				       areaid="10060 10019 10019" readonly="" class="list-input" >
			</div>
			<div class="one clearfix">
				<label for="detail" class="title">详细地址</label>
				{{--<textarea name="" id="" placeholder="请输入详细地址"  class="list-input"></textarea>--}}
				<div contenteditable="true" class="detail-address" name="detail"
				     id="detail" placeholder="请输入详细地址">{{isset($address['detail'])?$address['detail']: ''}}</div>
			</div>
		</div>
		<div class="group">
			<div class="one">
				<input type="checkbox" id="is-default" value="true"
				@if((isset($address['isdefault']) && $address['isdefault']) || (isset($isNeedDefault) && $isNeedDefault))
					checked
				@endif
				>
				<label for="is-default" class="is-default">
					默认地址
				</label>
			</div>
		</div>
		@if(isset($address))
			<div class="add-delete">
				<button id="delete" class="btn btn-default edit-btn">删除</button>
				<button id="save" class="btn btn-default edit-btn">保存</button>
			</div>
		@else
			<div class="add-address">
				<button id="save" class="btn btn-default add-btn">保存</button>
			</div>
		@endif
	</form>

</div>
{{--模态框--}}
<div  class="modal fade" id="myModal" tabindex="-1"   aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<span>确认要删除地址</span>
			</div>
			<div class="modal-footer">
				<a type="button" class="btn btn-default" data-dismiss="modal">返回</a>
				<a type="button" id="del-btn" class="btn btn-default">删除</a>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
    @parent
    <script src="/common/js/area.js"></script>
    <script src="/components/mobiscroll/js/mobiscroll.appframework.js"></script>
    <script src="/components/mobiscroll/js/mobiscroll.core.js"></script>
    <script src="/components/mobiscroll/js/mobiscroll.frame.js"></script>
    <script src="/components/mobiscroll/js/mobiscroll.scroller.js"></script>
    <script src="/components/mobiscroll/js/i18n/mobiscroll.i18n.zh.js"></script>
    <script src="/common/js/address_area.js"></script>

    <script type="text/javascript">

		$(function() {
		    $(".group .detail-address").width(($(".group").width()-$(".group .title").width()-53)+'px');
		    $(".group .list-input").width(($(".group").width()-$(".group .title").width()-53)+'px');
			//默认地址
			@if(isset($address['area']) && $address['area'])
				var currentAddressId={
					'proviceid':'',
					'cityid':'',
					'districtid':''
				};
				for(var i = 0;i < AddressInfo.Provinces.length;i++){
					if (AddressInfo.Provinces[i]._provicename === '{{$address['province']}}') {
						currentAddressId.proviceid = AddressInfo.Provinces[i]._proviceid;
						break;
					}
				}
				if(currentAddressId.proviceid) {
					for (var i = 0; i < AddressInfo.Cities[currentAddressId.proviceid].length; i++) {
						if (AddressInfo.Cities[currentAddressId.proviceid][i]._cityname === '{{$address['city']}}') {
							currentAddressId.cityid = AddressInfo.Cities[currentAddressId.proviceid][i]._cityid;
							break;
						}
					}
					if(currentAddressId.cityid) {
						for (var i = 0; i < AddressInfo.Districts[currentAddressId.cityid].length; i++) {
							if (AddressInfo.Districts[currentAddressId.cityid][i]._districtname === '{{$address['area']}}') {
								currentAddressId.districtid = AddressInfo.Districts[currentAddressId.cityid][i]._districtid;
								break;
							}
						}
						$("#area").attr("areaid", currentAddressId.proviceid + ' ' + currentAddressId.cityid + ' ' + currentAddressId.districtid);
						$("#area").val('{{$address['province']}} {{$address['city']}} {{$address['area']}}');
					}
				}
			@endif

//			地址选择插件
			var valo = $("#area").attr("areaid");
			$('#area').mobiscroll().scroller(
				{
					preset: 'area',
					theme: 'mobiscroll',
//					theme: 'android-ics light',
					display: 'bottom',
					valueo: valo,
					showLabel: true,
					lang:'zh',
					rows:3
				});


//			保存或编辑
			$('#save').click(function(){
				var data=check();
				if(!data){
					return false;
				}
				data['isdefault']=$("#is-default").is(':checked');
				@if(isset($address))
				//保存编辑地址
					{{--data['id']="{{$address['id']}}";--}}
					$.ajax({
						type:'PUT',
						url:'/wechat/address/{{$address['id']}}',
						data:data,
						success:function(data){
							window.location.href='/wechat/address';
						},
						error:function(data){
							alert($.parseJSON(data.responseText).message);
						}
					});
				@else
//				保存新建地址
					$.ajax({
						type:'POST',
						url:'/wechat/address',
						data:data,
						success:function(data){
							window.location.href='/wechat/address';
						},
						error:function(data){
							alert($.parseJSON(data.responseText).message);
						}
					});
				@endif
				return false;
			});
//			验证
			function check(){
				var data={};
				data['name']=$("#name").val();
				data['tel']=$("#tel").val();
				data['detail']=$("#detail").html();
				var area = $("#area").val();
				if(!data['name']){
					alert('请输入收件人姓名');
					return false;
				}
				if(!data['tel']){
					alert('请输入收件人联系方式');
					return false;
				}
				if(!/^1[3|4|5|6|7|8]\d{9}$/.exec(data['tel'])){
					alert('收件人联系方式格式不正确');
					return false
				}
				if(!area){
					alert('请选择地区');
					return false;
				}else{
					data['area']=area.split(' ');
				}
				if(!data['detail']){
					alert('请输入详细地址');
					return false;
				}
				return data;
			}
//			删除
			@if(isset($address))
				$("#delete").click(function(){
					$("#myModal").modal('show');
					return false;
				});
				$("#del-btn").click(function(){
					$.ajax({
						type:'DELETE',
						url:'/wechat/address/'+{{$address['id']}},
						success:function(){
							window.location.href='/wechat/address';
						},
						error:function(data){
							$("#myModal").modal('hide');
							alert(data.message);
						}
					});
					return false;
				});
			@endif

//			模态框
			function centerModals() {
				$('.modal').each(function (i) {   //遍历每一个模态框
					var $clone = $(this).clone().css('display', 'block').appendTo('body');
					var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
					top = top > 0 ? top : 0;
					$clone.remove();
					$(this).find('.modal-content').css("margin-top", top - 30);  //修正原先已经有的30个像素
				});

			}

			$('.modal').on('show.bs.modal', centerModals);      //当模态框出现的时候
			$(window).on('resize', centerModals);               //当窗口大小变化的时候

		})
    </script>
@endsection