/**
 * Created by shenzhaoke on 2016/6/15.
 */
(function () {
	'use strict';

	angular.module('app')
		.service('HealthCustomerService', ['$http', 'urls', 'Upload', '$q', function ($http, urls, Upload, $q) {
			var self = this;

			this.init=function(name){
				var deferred = $q.defer();
				$http.get(urls.BASE_API + '/customer/init').success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;

			};

			this.editCustomer_save= function (data) {
				var deferred = $q.defer();
                var name='addCustomer';
                if(data.id){
                    name='editCustomer';
                }
				$http.post(urls.BASE_API +"/"+ name,data).success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;
			};

			this.getCustomerTotal=function(name){
				var deferred = $q.defer();
				$http.get(urls.BASE_API + '/getCustomerTotal/'+name).success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;

			};

			this.getCustomerList=function(page,name){
				var deferred = $q.defer();
				$http.get(urls.BASE_API + '/getCustomerList/'+page+'/'+name).success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;

			}

		}
		])
})();