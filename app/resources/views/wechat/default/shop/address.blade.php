@extends('wechat.default.master')

@section('title', '选择收货地址')

@push('links')
<link rel="stylesheet" href="/common/css/wechat/default/address.css" type="text/css">
<style type="text/css">

</style>
@endpush

@section('content')
<div class="container">
	<div class="address-list">
		@if(isset($address[0]))
			@foreach($address as $one)
				<div class="one-address clearfix" onclick="choose({{$one['id']}})">
					<div class="name-tel clearfix">
						<span class="name">
							{{$one['name']}}
						</span>
						<span class="tel">
							{{$one['tel']}}
						</span>
						<span class="edit-icon" onclick="edit({{$one['id']}})">
						<i class="glyphicon glyphicon-edit"></i>
						</span>
					</div>
					<div class="address-text">
						@if($one['isdefault'])
							<span class="default-address">[默认地址]</span>
						@endif
						<span class="address">
							{{$one['province'].$one['city'].$one['area'].$one['detail']}}
						</span>
{{--						{{$one['province']}}{{$one['city']}}{{$one['area']}}{{$one['detail']}}--}}
					</div>
				</div>
			@endforeach
		@else
			<span class="no-address">还没有收货地址?点击下方马上添加一个。</span>
		@endif
	</div>
	<div class="add-address">
		<a href="/wechat/address/new" class="btn btn-default">添加新地址</a>
	</div>
</div>
@endsection

@section('scripts')
    @parent
    {{--<script src="/common/js/area.js"></script>--}}
    <script type="text/javascript">
	    //	    跳转编辑
	    function edit(id){
		    window.location.href="/wechat/address/"+id;
		    event.stopPropagation();
	    }
	    //选中地址
	    function choose(id){
		    var data={
			    'address':id
		    };
//		    window.location.href='/wechat/order/new/?addressId='+JSON.stringify(data);
		    window.location.href='/wechat/order/new/?addressId='+id;
	    }

	    {{--//显示地址--}}
	    {{--var data=[];--}}
	    {{--@foreach($address as $key=>$one)--}}
	    {{--data[{{$key}}]=[];--}}
		{{--for(var i=0;i<AddressInfo.Provinces.length;i++){--}}
			{{--if (AddressInfo.Provinces[i]._proviceid == {{$one['province']}}) {--}}
				{{--data[{{$key}}]['province']=AddressInfo.Provinces[i]._provicename;--}}
				{{--break;--}}
			{{--}--}}
        {{--}--}}
	    {{--for(var i=0;i<AddressInfo.Cities[{{$one['province']}}].length;i++){--}}
		    {{--if(AddressInfo.Cities[{{$one['province']}}][i]._cityid=={{$one['city']}}){--}}
			    {{--data[{{$key}}]['city']=AddressInfo.Cities[{{$one['province']}}][i]._cityname;--}}
			    {{--break;--}}
		    {{--}--}}
	    {{--}--}}
	    {{--for(var i=0;i<AddressInfo.Districts[{{$one['city']}}].length;i++){--}}
		    {{--if(AddressInfo.Districts[{{$one['city']}}][i]._districtid=={{$one['area']}}){--}}
			    {{--data[{{$key}}]['area']=AddressInfo.Districts[{{$one['city']}}][i]._districtname;--}}
			    {{--break;--}}
		    {{--}--}}
	    {{--}--}}
	    {{--data[{{$key}}]['detail']='{{$one['detail']}}';--}}
		{{--@endforeach--}}
        {{--console.log(data);--}}
	    {{--var address=$(".address");--}}
	    {{--for(var i=0;i<data.length;i++){--}}
		    {{--address.eq(i).text(data[i]['province']+data[i]['city']+data[i]['area']+data[i]['detail']);--}}
	    {{--}--}}

    </script>
@endsection