(function() {
    'use strict';

    angular.module('app')
        .service('ReportService', ['$http', 'urls', '$q',
            function($http, urls, $q) {
                var self = this;

                this.getReportData = function(code) {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/getReportData/'+code).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.getReportDetail = function(code,project_id) {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/getReportDetail/'+code+'/'+project_id).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
            }
        ]);

})();