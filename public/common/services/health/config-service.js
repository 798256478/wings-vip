(function() {
    'use strict';

    angular.module('app')
        .service('ConfigService', ['$http', 'urls', '$q', 'Upload', '$timeout',
            function($http, urls, $q, Upload, $timeout) {
            var self = this;

            this.upload = function(file, errFiles, experiment) {
                var deferred = $q.defer();
                if(file){
                    var file = file;
                    var error = errFiles && errFiles[0];
                    file.upload = Upload.upload({
                        url: urls.BASE_API + '/upload',
                        data: {
                            'setting': file,
                            'experiment': experiment,
                        },
                        method: 'POST'
                    });
                    file.upload.then(function(response) {
                        $timeout(function() {
                            deferred.resolve(response.data);
                        });
                    }, function(response) {
                        if (response.status > 0)
                            deferred.reject(response.status + ': ' + response.data);
                    });
                }else {
                    deferred.reject("文件格式不正确");
                }
                return deferred.promise;
            }
        }]);
})();
