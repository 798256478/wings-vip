@extends('wechat.default.master')

@section('title', '选择优惠券')

@push('links')
<style type="text/css">
	body{
		background-color: #eeeeee;
	}

	.button-div{
		margin-top: 20px;
	}

	.button-div button{
		margin: 0 5%;
		width: 90%;
	}

	.no-have-ticket span{
		height: 200px;
		display: block;
		width: 100%;
		line-height: 200px;
		text-align: center;
		font-size: 22px;
		color: #999999;
	}

</style>
@endpush

@section('content')
	<div class="container">
		<div class="row" style="margin-top:10px;">
			<div class="tickets-pack" >
				{{--可以使用--}}
				@foreach ($tickets['usable'] as $ticket)
					<details class="ticket">
						<summary onclick="chooseTicket({{$ticket->id}})">
							<div class="left">
								<small class="type">{{ $ticket->ticketTemplate['card_type'] }}</small>
								<span class="title {{ $ticket->ticketTemplate['color'] }}Front">
									{{ $ticket->ticketTemplate['title'] }}
								</span>
							</div>
							<div class="right {{ $ticket->ticketTemplate['color'] }}">
								<span class="sub-title">{{ $ticket->ticketTemplate['sub_title'] or '无限制' }}</span>
								<small class="date-info">{{ $ticket->ticketTemplate['end_display_time'] }}</small>
								<small class="date-info">到期</small>
								@if ( strtotime($ticket->ticketTemplate['end_timestamp']) - time() < 3600 * 24 * 4 )
									<div class="seal"> 即将到期</div>
								@endif
								<div class="point p1"></div>
								<div class="point p2"></div>
								<div class="point p3"></div>
								<div class="point p4"></div>
								<div class="point p5"></div>
								<div class="point p6"></div>
							</div>
						</summary>
						{{--<p class="description">{{ $ticket->ticketTemplate['description'] }}</p>--}}
					</details>
				@endforeach
				{{--未达到条件--}}
				@foreach ($tickets['disable'] as $ticket)
					<details class="ticket">
						<summary>
							<div class="left">
								<small class="type">{{ $ticket->ticketTemplate['card_type'] }}</small>
								<span class="title {{ $ticket->ticketTemplate['color'] }}Front">
									{{ $ticket->ticketTemplate['title'] }}
								</span>
							</div>
							<div class="right {{ $ticket->ticketTemplate['color'] }}">
								<span class="sub-title">{{ $ticket->ticketTemplate['sub_title'] or '无限制' }}</span>
								<small class="date-info">{{ $ticket->ticketTemplate['end_display_time'] }}</small>
								<small class="date-info">到期</small>
								<div class="seal grey">暂不可用</div>
								<div class="point p1"></div>
								<div class="point p2"></div>
								<div class="point p3"></div>
								<div class="point p4"></div>
								<div class="point p5"></div>
								<div class="point p6"></div>
							</div>
						</summary>
						{{--<p class="description">{{ $ticket->ticketTemplate['description'] }}</p>--}}
					</details>
				@endforeach
				{{--尚未开始--}}
				@foreach ($tickets['notStart'] as $ticket)
					<details class="ticket">
						<summary>
							<div class="left">
								<small class="type">{{ $ticket->ticketTemplate['card_type'] }}</small>
								<span class="title {{ $ticket->ticketTemplate['color'] }}Front">
									{{ $ticket->ticketTemplate['title'] }}
								</span>
							</div>
							<div class="right {{ $ticket->ticketTemplate['color'] }}">
								<span class="sub-title">{{ $ticket->ticketTemplate['sub_title'] or '无限制' }}</span>
								<small class="date-info">{{ $ticket->ticketTemplate['begin_display_time'] }}</small>
								<small class="date-info">/</small>
								<small class="date-info">{{ $ticket->ticketTemplate['end_display_time'] }}</small>
								<div class="seal grey">尚未开始</div>
								<div class="point p1"></div>
								<div class="point p2"></div>
								<div class="point p3"></div>
								<div class="point p4"></div>
								<div class="point p5"></div>
								<div class="point p6"></div>
							</div>
						</summary>
						{{--<p class="description">{{ $ticket->ticketTemplate['description'] }}</p>--}}
					</details>
				@endforeach
			</div>
		</div>
		@if(count($tickets['usable'])===0 &&count($tickets['notStart'])===0 &&count($tickets['disable'])===0)
			<div class="no-have-ticket">
				<span>您还没有此类优惠券</span>
			</div>
		@endif
		<div class="button-div">
			<button type="button" class="btn btn-info" id="not-ticket">不使用优惠券</button>
		</div>
	</div>
@endsection

@section('scripts')
	@parent
	<script type="text/javascript">
		function chooseTicket(id){
			var data={
				'ticket':id
			};
			window.location.href='/wechat/order/new/'+JSON.stringify(data);
		}

		$(function(){
			$("#not-ticket").click(function(){
				var data={
					'ticket':''
				};
				window.location.href='/wechat/order/new/'+JSON.stringify(data);
			});
		});
	</script>
@endsection