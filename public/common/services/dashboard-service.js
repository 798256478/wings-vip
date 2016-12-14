(function() {
    'use strict';

    angular.module('app')
        .service('DashboardService', ['$http', 'urls', '$q',
            function($http, urls, $q) {
                var self = this;
                this.getInitData = function(key) {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/dashboard/'+key).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
            }
        ]);
})();
