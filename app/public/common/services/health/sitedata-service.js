(function() {
    'use strict';

    angular.module('app')
        .service('SiteDataService', ['$http', 'urls', '$q', 'Upload', '$timeout',
            function($http, urls, $q, Upload, $timeout) {
            var self = this;

            this.uploadSiteData = function(file, errFiles, form) {
                var deferred = $q.defer();
                if(file){
                    var file = file;
                    var error = errFiles && errFiles[0];
                    file.upload = Upload.upload({
                        url: urls.BASE_API + '/siteData',
                        data: {
                            'data': file,
                            'form': form
                        },
                        method: 'POST'
                    });
                    file.upload.then(function(response) {
                        $timeout(function() {
                            deferred.resolve(response.data);
                        });
                    }, function(response) {
                        if (response.status > 0)
                            deferred.reject(response.data);
                    });
                }else {
                    deferred.reject("文件格式不正确");
                }
                return deferred.promise;
            }

            this.editSiteData = function(data){
                var deferred = $q.defer();
                $http.put(urls.BASE_API + '/siteData', data).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }
        }]);

})();
