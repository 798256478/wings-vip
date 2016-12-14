/**
 * Created by shenzhaoke on 2016/6/1.
 */
(function () {
	'use strict';

	angular.module('app')
		.service('BarcodeService', ['$http', 'urls','Upload', '$q', function ($http, urls,Upload,$q) {
			var self = this;

			//获取进度配置信息
			this.getProgressConfig=function(){
				var deferred = $q.defer();
				$http.get(urls.BASE_API + '/getProgresses').success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;

			};




			//修改实验信息
			this.changeBarcodeInfo=function(data){
				var deferred = $q.defer();
				$http.get(urls.BASE_API + '/changeBarcodeInfo/'+data.barcode_id+'/'+data.experiment_id+'/'+data.progress_id).success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;

			};




			//获得当前页条码列表
			this.getBarcodes=function(page,pageSize,code){
				var deferred = $q.defer();
                if(code.length == 0)
                    code='*';
				$http.get(urls.BASE_API + '/getBarcodes/'+page+'/'+pageSize+'/'+code).success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;

			};

			this.getBarcodeInfo=function(code){
				var deferred = $q.defer();
				$http.get(urls.BASE_API + '/getBarcodeInfo/'+ code).success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;
			};



            //添加编码
			this.addBarcode=function(code){
				var deferred = $q.defer();
				$http.get(urls.BASE_API + '/addBarcode/'+code).success(function(res) {
					deferred.resolve(res);
				}).error(function(res) {
					deferred.reject(res);
				});
				return deferred.promise;
			};
            
            //批量添加编码
			this.upload=function(file){
				var deferred = $q.defer();
				if(file){
					file.upload = Upload.upload({
						url: urls.BASE_API + '/addBarcodes',
						data: {
							'code': file
						},
						method: 'POST'
					}).success(function(res) {
						deferred.resolve(res);
					}).error(function(res) {
						deferred.reject(res);
					});
					//file.upload.then(function(res) {
					//		deferred.resolve(res);
					//}, function(res) {
					//	deferred.reject(res);
					//});
				}else {
					deferred.reject("格式不正确");
				}
				return deferred.promise;
			}


		}])
})();