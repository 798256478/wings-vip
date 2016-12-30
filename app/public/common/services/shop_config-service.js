(function() {
    'use strict';

    angular.module('app')
        .service('ShopConfigService', ['$http', 'urls', '$q', function($http, urls, $q) {
            var self = this;

            this.getAllShop=function(){
                var deferred=$q.defer();
                $http.get(urls.BASE_API+'/shopConfig').success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            //根据数组ID获得商品列表
            this.getCommoditiesByArray= function (items) {
                var deferred=$q.defer();
                $http.post(urls.BASE_API+'/getCommoditiesByArray',items).success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getCommoditiesList=function(data){
                var deferred=$q.defer();
                $http.post(urls.BASE_API+'/getCommoditiesListByPage',data).success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;

            }

            //保存店铺标题部分
            this.saveShopTitle=function(shopId,shopTitle){
                var data={
                    'shopId':shopId,
                    'shopTitle':shopTitle
                };
                var deferred=$q.defer();
                $http.post(urls.BASE_API+'/saveShopTitle',data).success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            };

            //保存分类信息
            this.saveShopCategory=function(shopId,categoryId,category){
                var data={
                    'shopId':shopId,
                    'categoryId':categoryId,
                    'category':category
                };
                var deferred=$q.defer();
                $http.post(urls.BASE_API+'/saveShopCategory',data).success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            };

            this.saveLayout=function(shopId,categoryId,layout){
                var deferred=$q.defer();
                $http.get(urls.BASE_API+'/saveCategoryLayout/'+shopId+'/'+categoryId+'/'+layout).success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;

            }

            this.deleteShop=function(id){
                var deferred=$q.defer();
                $http.get(urls.BASE_API+'/deleteShop/'+id).success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.deleteCategory=function(shopId,categoryId){
                var deferred=$q.defer();
                $http.get(urls.BASE_API+'/deleteCategory/'+shopId+'/'+categoryId).success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.saveShopPage = function(shopPage){
                var deferred=$q.defer();
                $http.post(urls.BASE_API+'/saveShopPage',shopPage).success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getShopPage = function(){
                var deferred=$q.defer();
                $http.get(urls.BASE_API+'/getShopPage').success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

        }]);

})();
