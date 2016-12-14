/**
 * Created by shenzhaoke on 2016/5/31.
 */
'use strict';

angular.module('app')
	.controller('QuestionnaireController', ['$scope', 'QuestionnaireService','$location','AuthService','$q','$anchorScroll',
		function ($scope, QuestionnaireService,$location,AuthService,$q,$anchorScroll) {
		if (AuthService.current_user == null) {
		    $location.path("/login");
		    return;
		}

		$(".header .meta .page").text('问卷调查');
		$scope.form_data={};
		$scope.page=0;
		$scope.init=function(){
			QuestionnaireService.init().then(function(res){
				$scope.questionnaire=res;
				//console.log(res);
				//console.log(new Date().getTime());
			},function(res){
				alert(res.message)
			})
		};
		$scope.init();

		$scope.last=function(){
			$scope.page--;
			QuestionnaireService.getQuestionnaireByPage($scope.page).then(function(res){
				//$scope.form_data={};
				$scope.questionnaire=res;
				//$scope.answer=res.answer;
				//angular.forEach($scope.answer,function(val,key){
				//	if(key.indexOf('topic')==0){
				//		$scope.form_data[key]=val;
				//	}
				//});
				$location.hash('position');
				$anchorScroll();
			},function(res){
				alert(res.message);
			});
		};

		$scope.next=function(){
			if(!angular.isDefined($scope.form_data.code)){
				alert ('未填写条码');
				return ;
			}

			$scope.submit().then(function(){
				$scope.page++;
				QuestionnaireService.getQuestionnaireByPage($scope.page).then(function(res){
					//$scope.form_data={};
					$scope.questionnaire=res;
					//$scope.answer=res.answer;
					//angular.forEach($scope.answer,function(val,key){
					//	if(key.indexOf('topic')==0){
					//		$scope.form_data[key]=val;
					//	}
					//});
					$location.hash('position');
					$anchorScroll();

				},function(res){
					alert(res.message);
				});

			},function(){});
		};

		$scope.complete=function(){
			if(!angular.isDefined($scope.form_data.code)){
				alert ('未填写条码');
				return ;
			}

			$scope.form_data.time=new Date().getTime();
			$scope.submit().then(function(){
				alert('已完成');
				$location.hash('position');
				$anchorScroll();

			},function(){});
		};

		$scope.searchCode = function () {
			$scope.init();

			QuestionnaireService.searchAnswerByCode($scope.input.code).then(function(res){
				$scope.form_data={};
				$scope.page=0;
				//$scope.questionnaire=res.questionnaire;
				//$scope.detection=res.detection;
				$scope.answer=res.questionnaire_answer;
				angular.forEach($scope.answer,function(val,key){
					if(key.indexOf('topic')==0){
					$scope.form_data[key]=val;
					}
				});
				$scope.form_data.code=$scope.input.code;
				alert('条码已找到');
			},function(res){
				alert(res.message)
			})
		};
		$scope.submit=function() {
			var deferred = $q.defer();
			angular.forEach($scope.questionnaire.questions,function(val,key){
				if(!$scope.form_data[val.position]){
					$scope.form_data[val.position]='';
				}
			});

			QuestionnaireService.submitData($scope.form_data).then(function(res){
				//alert('添加成功');
				deferred.resolve(res);

			},function(res){
				alert(res.message);
				deferred.reject(res);

			});
			return deferred.promise;

		};

		$scope.radioClick=function(val,key,position){
			//console.log(val+key+position);
			if(val==key){
				$scope.form_data[position]=undefined;
				//console.log(val+key+$scope.form_data[position]);

			}else{
				$scope.form_data[position]=key;
			}
		}


	}]);
