(function() {
    'use strict';

    angular.module('app')
        .service('OperatingRecordsService', ['$http', 'urls', '$q', function($http, urls, $q) {
            var self = this;

            this.getOperatingRecord=function(page,search){
                var deferred = $q.defer();
                var a;
                if(search){
                    a=urls.BASE_API + '/operatingRecords/'+ page +'/'+ search;
                }else{
                    a=urls.BASE_API + '/operatingRecords/' + page ;
                }
                $http.get(a).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

        }]);

})();
