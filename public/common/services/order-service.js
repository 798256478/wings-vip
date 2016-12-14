(function() {
    'use strict';

    angular.module('app')
        .service('OrderService', ['$http', 'urls', '$q', function($http, urls, $q) {
            var self = this;

            this.getOrderData = function(option) {
                var deferred = $q.defer();
                var url = urls.BASE_API + '/order_data/' + option ;
                $http.get(url).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            /*this.getOrderTotal = function(opt) {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/order_total/' + opt).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }*/

            this.getSaleTop=function() {
                var deferred = $q.defer();
                var url = urls.BASE_API + '/saleTop';
                $http.get(url).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getOrder = function(id) {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/order/' + id).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.updateOrder = function(order) {
                var deferred = $q.defer();
                $http.put(urls.BASE_API + '/order', order).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.search = function(val) {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/ordersearch/' + val).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getDeliverList=function(id){
                var deferred=$q.defer();
                $http.post(urls.BASE_API+'/deliverList',id).success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.deliver=function(expressInfo){
                var deferred=$q.defer();
                $http.post(urls.BASE_API+'/deliver',expressInfo).success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.editAddress=function(addressInfo){
                var deferred=$q.defer();
                $http.post(urls.BASE_API+'/editAddress',addressInfo).success(function(res){
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

        }]);

})();
