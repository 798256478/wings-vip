(function() {
    'use strict';

    angular.module('app')
        .service('SyncService', ['$http', 'urls', '$q', function($http, urls, $q) {
            var self = this;

            this.getSyncFailRecord=function(page,isRepair){
                var deferred = $q.defer();
                var data = {
                    'page':page,
                    'isRepair':isRepair
                };
                $http.post(urls.BASE_API + '/syncFailRecords',data).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.syncSuccess=function(id){
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/syncSuccess/' + id).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.againSync=function(id){
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/againSync/' + id).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

        }]);

})();
