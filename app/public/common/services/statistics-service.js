(function() {
    'use strict';

    angular.module('app')
        .service('StatisticsService', ['$http', 'urls', '$q', function($http, urls, $q) {
            var self = this;

            this.getDaysData = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/statisticsDaysData').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getStatistics = function(value) {
                var deferred = $q.defer();
                $http.post(urls.BASE_API + '/statistics', value).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getDateList = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/statistics').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getCommoditiesStatistics= function(value) {
                var deferred = $q.defer();
                $http.post(urls.BASE_API + '/commoditiesStatistics',value).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getCommodityStatisticsData= function(value,id) {
                var deferred = $q.defer();
                $http.post(urls.BASE_API + '/commodityStatistics/'+id,value).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }
        }]);

})();
