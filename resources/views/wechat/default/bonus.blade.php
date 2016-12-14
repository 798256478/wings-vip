@extends('wechat.default.master')

@section('title', '会员中心')

@push('links')

<style type="text/css">
	.header-property {
		background-color: {{ $theme->colors['THEME'] or '#0092DB' }};
	}

	.header-buttons .glyphicon {
		color: {{ $theme->colors['THEME'] or '#0092DB' }};
	}
</style>
@endpush

@section('content')
	<div class="container">
		<ol class="breadcrumb row">
			<li><a href="/wechat">会员卡</a></li>
			<li class="active">我的积分</li>
		</ol>

		<div>
			<button type="button" class="header-explain" data-container="body" data-toggle="popover"
			        data-placement="left" data-content="1元消费积1分">
				积分规则
			</button>
		</div>

		<div class="header-property">
			<label>积分</label>
			<br/>
			<span> {{$card_info->bonus}}</span>
		</div>

		<div class='header-buttons'>
			<a href="/wechat/mall">
			<span>
				&nbsp<span class="glyphicon glyphicon-shopping-cart"></span>
				&nbsp积分商城&nbsp
			</span>
			</a>
			&nbsp&nbsp&nbsp
			<a href="#redemption" role="tab" data-toggle="tab">
			<span>
				&nbsp<span class="glyphicon glyphicon-retweet"></span> 
				&nbsp使用明细&nbsp
			</span>
			</a>
		</div>
		<div class="content-title" href="#gain">
			<div class="line"></div>
		<span>
			&nbsp<span class="glyphicon glyphicon-bitcoin"></span>
			&nbsp积分明细&nbsp
		</span>
		</div>

		<div class="tab-content">
			{{--积分明细--}}
			<div id="gain" role="tabpanel" class="tab-pane active">
				@if (!empty($gain_records) && count($gain_records) > 0)
				<div class="record-group">
					@foreach($gain_records as $record)
					<div class="record value-change">
						<span class="record-time">{{date('Y-m-d H:i:s',$record['create_time'])}}:</span>
						<span class="record-value add">+{{$record['changes']['bonus']}} </span>
						<p>
							<span class="record-summary">{{isset($record['action']) ? $record['action'] : $record['minimal']}}</span>
						</p>
					</div>
					@endforeach
				</div>
				@else
				<div class="content-none">
					<div class="icon">
						<span class="glyphicon glyphicon-bitcoin"></span>
					</div>

					看起来你还没有获得积分~
				</div>
				@endif
			</div>

			{{--兑换记录--}}
			<div id="redemption" role="tabpanel" class="tab-pane">
				@if(!empty($redemption_records) && count($redemption_records) > 0)
				<div class="record-group">
					@foreach($redemption_records as $record)
					<div class="record value-change">
						<span class="record-time">{{date('Y-m-d H:i:s',$record['create_time'])}}:</span>
						<span class="record-value minus">{{$record['changes']['bonus']}}</span>

						<p>
							<span class="record-summary">{{isset($record['action']) ? $record['action'] : $record['minimal']}}</span>
						</p>
					</div>
					@endforeach
				</div>
				@else
				<div class="content-none">
					<div class="icon">
						<span class="glyphicon glyphicon-retweet"></span>
					</div>

					看起来你还没有使用积分~
				</div>
				@endif
			</div>
		</div>
	</div>
@endsection

@section('scripts')
	@parent
@endsection