(function() {
    'use strict';
    angular.module('app')
        .service('RefundService', ['$http', 'urls', '$q', function($http, urls, $q) {
            var self = this;
            this.getRefundsData = function(option) {
                var deferred = $q.defer();
                var url = urls.BASE_API + '/refunds';
                $http.post(url,option).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }
            this.getRefundData = function(id) {
                var deferred = $q.defer();
                var url = urls.BASE_API + '/refund/'+id;
                $http.get(url).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }
            this.dealRefund = function(data) {
                var deferred = $q.defer();
                var url = urls.BASE_API + '/dealRefund';
                $http.post(url,data).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }
        }]);
})();
