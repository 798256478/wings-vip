@extends('wechat.default.master')

@section('title', '健康问卷')

@push('links')

<style type="text/css">
	body {
		background-color: #eee;
	}

	.breadcrumb{
		margin-bottom: 0px;
	}

	.section {
		background-color: #fff;
	}

	.table-title {
		text-align: center;
	}

	.table-cont {
		background-color: #fff;
		padding: 15px 15px;
	}

	.table-cont .width{
		width:25%;
	}

	.submit-div {
		margin-top: 20px;
		margin-bottom: 100px;
	}

	.submit {
		background-color: #0092DB;
		color: white;
		width: 100%;
	}

</style>
@endpush

@section('content')
	<div class="container">
		<ol class="breadcrumb row">
			<li><a href="/wechat">会员卡</a></li>
			<li><a href="/wechat/health/detections">我的检测</a></li>
			<li><a href="/wechat/health/detection/{{$code}}/info">检测中心</a></li>
			<li class="active">健康问卷</li>
		</ol>
		@foreach($questionnaire->sections as $sectionsKey=>$section)
			<div class="section row " id="{{$sectionsKey}}">
				<div class="table-title"><h3>{{$section['section']}}</h3></div>
				<div class="table-cont">
					<table class="table table-condensed table-bordered table-striped">
						@foreach($section['questions'] as $questionsKey=>$topic)
							<tr>
								<td class="col-xs-1 col-sm-1">{{$questionsKey+1}}</td>
								<td class="col-xs-7 col-sm-7 ">{{$topic['title']}}</td>
								<td class="col-xs-4 col-sm-4">
									@if($topic['type']=='text')
										{{$questionnaireAnswer->$topic['position'] ? $questionnaireAnswer->$topic['position'] : ''}}
									@else
										@if(isset($questionnaireAnswer->$topic['position'])
											&&$questionnaireAnswer->$topic['position']!=''
											&&$questionnaireAnswer->$topic['position'] != null )
											@if(is_array($questionnaireAnswer->$topic['position']))
												@foreach($questionnaireAnswer->$topic['position'] as $oneAnswer)
													{{$topic['options'][$oneAnswer]}}&nbsp&nbsp
												@endforeach
											@else
												{{$topic['options'][$questionnaireAnswer->$topic['position']]}}
											@endif
										@else

										@endif
									@endif
								</td>
							</tr>
						@endforeach
					</table>
				</div>
			</div>
		@endforeach
		<div class="submit-div">
			<input type="submit" class="btn submit" value="查看进度">
		</div>
	</div>
@endsection

@section('scripts')
	@parent
	<script>
		$(function () {
			$('.btn').click(function () {
				window.location.href = '/wechat/health/detection/{{$code}}/progress';
			});

		})
	</script>
@endsection
