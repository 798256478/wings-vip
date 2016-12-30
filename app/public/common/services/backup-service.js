(function() {
    'use strict';

    angular.module('app')
        .service('BackupService', ['$http', 'urls', '$q', function($http, urls, $q) {
            var self = this;
            // this.getBackup = function() {
            //     var deferred = $q.defer();
            //     $http({
            //         method: 'GET',
            //         url: urls.BASE_API + '/backup',
            //         responseType: 'blob'
            //     }).success(function(res, status, headers){
            //         var name = headers('Content-Disposition');
            //         var arr = name.split('"');
            //         if (arr.length > 2) {
            //             name = arr[1];
            //         }
            //         deferred.resolve({'file':res, 'filename': name});
            //     }).error(function(res){
            //         deferred.reject(res);
            //     });
            //     return deferred.promise;
            // }
            this.getBackup = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/backup').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.backupHistory = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/backupHistory').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }
        }]);
})();
