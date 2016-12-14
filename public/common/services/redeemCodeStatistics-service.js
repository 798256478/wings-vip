(function() {
    'use strict';

    angular.module('app')
        .service('RedeemCodeStatisticsService', ['$http', 'urls', '$q', function($http, urls, $q) {
            var self = this;

            this.getHistoryList = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/redeemcodeHistoryList').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getHistory = function(date, type) {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/redeemcodeHistory/'+ date + '/' + type).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }
        }]);

})();
