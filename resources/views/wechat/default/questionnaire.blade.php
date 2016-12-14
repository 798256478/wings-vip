@extends('wechat.default.master')

@section('title', '我的检测')

@push('links')
<link href="/components/mobiscroll/css/mobiscroll.animation.css" rel="stylesheet" type="text/css"/>
<link href="/components/mobiscroll/css/mobiscroll.frame.css" rel="stylesheet" type="text/css"/>
<link href="/components/mobiscroll/css/mobiscroll.scroller.css" rel="stylesheet" type="text/css"/>
<link href="/common/css/wechat/default/questionnaire.css" rel="stylesheet" type="text/css"/>

<style type="text/css">
	.button-cont button {
		background-color: {{ $theme->colors['THEME'] or '#0092DB' }} !important;
		width: 40%;
		color: #fff;
		margin-bottom: 80px;
	}

	.checked {
		background-color: {{ $theme->colors['THEME'] or '#0092DB' }}!important;
		color: #fff;
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
				<form action="" name="">
					<div class="section-title"><h3>{{$section['section']}}</h3></div>
					<div class="section-cont">
						@foreach($section['questions'] as $questionsKey=>$topic)
							@if($topic['type']=='choose')
								<div class="one-topic">
									<div class="topic-title">
									<span>
										<span class="number">{{$questionsKey+1}}.</span>
										<pre>{{$topic['title']}}</pre>
										@if($topic['is_required']==1)
											<span class="required-icon">*</span>
										@endif
									</span>
									</div>
									<div class=" topic-option @if($topic['is_required']==1) required @endif ">
										@foreach($topic['options'] as $key => $val)
											<input type="radio" id="{{$topic['position']}}[{{$key}}]" value="{{$key}}"
											       name="{{$topic['position']}}"
													{{$questionnaireAnswer->$topic['position']==$key ? 'checked' :''}}>
											<label
											       class="input-cont  radio-label {{$questionnaireAnswer->$topic['position']==$key ? 'checked' :''}}">
												<span class="icon-cont"></span>
												<span class="writ-cont">{{$val}}</span>
											</label>
										@endforeach
									</div>
								</div>
							@elseif($topic['type']=='checkbox')
								<div class="one-topic">
									<div class="topic-title">
									<span>
										<span class="number">{{$questionsKey+1}}.</span>
										<pre>{{$topic['title']}}(多选)</pre>
										@if($topic['is_required']==1)
											<span class="required-icon">*</span>
										@endif
									</span>
									</div>
									<div class=" topic-option @if($topic['is_required']==1) required @endif ">
										@foreach($topic['options'] as $key => $val)
											<input type="checkbox" id="{{$topic['position']}}['{{$key}}']"
											       value="{{$key}}" name="{{$topic['position']}}[{{$key}}]"
											@if(is_array($questionnaireAnswer->$topic['position']))
												{{in_array($key,$questionnaireAnswer->$topic['position']) ? 'checked' :''}}
													@endif>
											<label for="{{$topic['position']}}['{{$key}}']" class="input-cont
											@if(is_array($questionnaireAnswer->$topic['position']))
											{{in_array($key,$questionnaireAnswer->$topic['position']) ? 'checked' :''}}
											@endif">
												<span class="icon-cont"></span>
												<span class="writ-cont">{{$val}}</span>
											</label>
										@endforeach
									</div>
								</div>
							@elseif($topic['type']=='judge')
								<div class="one-topic">
									<div class="topic-title">
									<span>
										<span class="number">{{$questionsKey+1}}.</span>
										<pre>{{$topic['title']}}</pre>
										@if($topic['is_required']==1)
											<span class="required-icon">*</span>
										@endif
									</span>
									</div>
									<div class=" topic-option @if($topic['is_required']==1) required @endif ">
										@foreach($topic['options'] as $key => $val)
											<input type="radio" id="{{$topic['position']}}[{{$key}}]" value="{{$key}}"
											       name="{{$topic['position']}}"
													{{$questionnaireAnswer->$topic['position']==$key ? 'checked' :''}}>
											<label
											       class="input-cont  radio-label {{$questionnaireAnswer->$topic['position']==$key ? 'checked' :''}}">
												<span class="icon-cont"></span>
												<span class="writ-cont">{{$val}}</span>
											</label>
										@endforeach
									</div>
								</div>
							@else
								<div class="one-topic-text clearfix">
									<div class="topic-title-text">
									<span>
										<span class="number">{{$questionsKey+1}}.</span>
										<pre>{{$topic['title']}}</pre>
										@if($topic['is_required']==1)
											<span class="required-icon">*</span>
										@endif
									</span>
									</div>
									<div class=" topic-option-text @if($topic['is_required']==1) required @endif ">
										<input type="text" id="{{isset($topic['unique'])?'birthday':''}}" class=" list-input color-show " name="{{$topic['position']}}"
										       value="{{$questionnaireAnswer->$topic['position'] ? $questionnaireAnswer->$topic['position']:''}}">
									</div>
								</div>
							@endif
						@endforeach
					</div>
					<div class="button-cont clearfix">
						<input type="hidden" name="nowSection" value="{{$sectionsKey}}">
						@if($sectionsKey!=0)
							<button type="button" class="btn  up"><span class="glyphicon glyphicon-menu-left"></span>
								上一页
							</button>
						@endif
						@if(isset($questionnaire->sections[$sectionsKey+1]) && $questionnaire->sections[$sectionsKey+1]!='' && $questionnaire->sections[$sectionsKey+1]!=null)
							<button type="button" class="btn  down">下一页 <span
										class="glyphicon glyphicon-menu-right"></span></button>
						@else
							<input type="hidden" name="is_complete" value="1">
							<button type="button" class="btn  submit-button">提交</button>
						@endif
					</div>

				</form>
			</div>
		@endforeach
	</div>

@endsection

@section('scripts')
	@parent
	<script src="/components/mobiscroll/js/mobiscroll.appframework.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.core.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.frame.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.scroller.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.util.datetime.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.select.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.datetimebase.js"></script>
	<script src="/components/mobiscroll/js/mobiscroll.datetime.js"></script>
	<script src="/components/mobiscroll/js/i18n/mobiscroll.i18n.zh.js"></script>

	{{--<script src="/components/jquery-validation/jquery.validate.js"></script>--}}
	<script type="application/javascript">
		$('.writ-cont').width($('.input-cont').width() - $('.icon-cont').width() - 12);
		$(function () {
			function text_width(){
				for(var i=0;i<$('.topic-option-text').length;i++) {
					$('.topic-option-text').eq(i).css('width', parseInt($('.one-topic-text').eq(i).css('width'))
							- parseInt($('.topic-title-text').eq(i).css('width')) - parseInt($('.topic-option-text').eq(i).css('margin-left'))-10);
				}
			}
			text_width();

			$('#birthday').mobiscroll().date({
				display: "bottom",
				lang: "zh"
			});
				//单选多选的外观切换
			$(".input-cont").click(function () {
				if ($(this).hasClass('radio-label')) {
					//单选
					$(this).siblings('label').removeClass('checked');
					$(this).siblings('input').removeAttr('checked');
					if($(this).hasClass('checked')){
						$(this).prev().removeAttr('checked');
						$(this).removeClass('checked');
					}else{
						$(this).prev().prop('checked','checked');
						$(this).addClass('checked');
					}
				} else {
					//多选
					if ($(this).hasClass('checked')) {
						$(this).removeClass('checked');
					} else {
						$(this).addClass('checked');
					}
				}
			})

			//数据验证

			function ver(_this) {
				var ver_data = _this.closest('form').find(".required");
//				alert(ver_data);
//				ver_data.each(function(index,onedata){
//					var one_topic=$(onedata).find('input');
//					alert($(one_topic[0]).attr('type'))
//				})
				var is_true = true;
				var anchor = false;
				for (var i = 0; i < ver_data.length; i++) {
//					alert($(ver_data[i]));
					var one_topic = $(ver_data[i]).find('input');
					var one_topic_type = $(one_topic[0]).attr('type');
					var one_topic_name = $(one_topic[0]).attr('name');
					var one_topic_id = $(one_topic[0]).attr('id');
					if (one_topic_type == 'text') {
						var one_topic_val = $("input[name=" + one_topic_name + "]").val();
						if (one_topic_val == '' || one_topic_val == null) {
							$(ver_data[i]).prev().append("<span class='error' >必填</span>");
							is_true = false;
							if (!anchor) {
								anchor = $(ver_data[i]).prev().offset().top;
							}
						}
					}
					else if (one_topic_type == 'radio') {
						if (!$(':radio[name=' + one_topic_name + ']').is(":checked")) {
							$(ver_data[i]).prev().append("<span class='error' >必选</span>");
							is_true = false;
							if (!anchor) {
								anchor = $(ver_data[i]).prev().offset().top;
							}
						}
					}
					else {
						if (!$(ver_data[i]).find("input").is(":checked")) {
//							alert($(ver_data[i] ).find("input").find(':checked').size());
							$(ver_data[i]).prev().append("<span class='error' >必选</span>");
							is_true = false;
							if (!anchor) {
								anchor = $(ver_data[i]).prev().offset().top;
							}
						}
					}
				}
				if (!is_true) {
					text_width();
					alert('请填写完整');
					$("body,html").animate({
						scrollTop: anchor
					}, 200);
					return false;
				}
				return true;
			}


//			章节的切换
			var section = 0;
			$('.section').hide();
			$('#0').show();
			$('.up').on('click', function () {
//				alert($(this).closest('form').validate(ver));
//				console.log(section);
				$('#' + section).hide();
				section--;
				$("body,html").animate({
					scrollTop: 0
				}, 500);
				$('#' + section).show();
			});
			$('.down').on('click', function () {
				$('.error').remove();
				if (!ver($(this))) {
					return false;
				}
				$.ajax({
					url: '/wechat/health/detection/{{$code}}/questionnaire',
					async: false,
					type: 'POST',
					data: $(this).closest('form').serialize(),
					error: function (data) {
//						alert(JSON.stringify(data));
						alert('网络连接错误');
					},
					success: function (data) {
//						alert(JSON.stringify(data));
//						console.log(section);
						$('#' + section).hide();
						section++;
						$("body,html").animate({
							scrollTop: 0
						}, 500);
						$('#' + section).show();
					}
				})
			})
			$('.submit-button').on('click', function () {
				$('.error').remove();
				if (!ver($(this))) {
					return false;
				}
				$.ajax({
					url: '/wechat/health/detection/{{$code}}/questionnaire',
					async: false,
					type: 'POST',
					data: $(this).closest('form').serialize(),
					error: function (data) {
//						alert(JSON.stringify(data));
						alert('网络连接错误');
					},
					success: function (data) {
//						alert(JSON.stringify(data));
//						console.log(section);
						window.location.href = '/wechat/health/detection/{{$code}}/questionnaire';
					}
				})
			})
		})


	</script>
@endsection