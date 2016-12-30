/**
 * Created by shenzhaoke on 2016/6/1.
 */
(function () {
	'use strict';

	angular.module('app')
		.service('QuestionnaireService', ['$http', 'urls', '$q', function ($http, urls, $q) {
			var self = this;

			this.init=function(){
				var deferred = $q.defer();
				$http.get(urls.BASE_API + '/initQuestionnaire').success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;

			};

			this.getQuestionnaireByPage=function(page){
				var deferred = $q.defer();
				$http.get(urls.BASE_API + '/getQuestionnaireByPage/'+ page).success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;

			};

			this.searchAnswerByCode=function(code){
				var deferred = $q.defer();
				$http.get(urls.BASE_API + '/searchAnswerByCode/'+ code).success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;
			};

			this.submitData=function(data){
				var deferred = $q.defer();
				$http.post(urls.BASE_API + '/addAnswer',data).success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;
			}

		}])
})();