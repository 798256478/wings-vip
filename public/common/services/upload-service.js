(function() {
    'use strict';

    angular.module('app')
        .service('UploadService', ['$http', 'urls', '$q', 'Upload', '$timeout',
            function($http, urls, $q, Upload, $timeout) {
                var self = this;

                this.addImage = function(image, errFiles) {
                    var deferred = $q.defer();
                    if(image){
                        var file = image;
                        var error = errFiles && errFiles[0];
                        file.upload = Upload.upload({
                            url: urls.BASE_API + '/image',
                            data: {
                                'image': image
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
                        deferred.reject("图片格式不正确");
                    }
                    return deferred.promise;
                }

                this.addIcon = function(image, errFiles) {
                    var deferred = $q.defer();
                    if(image){
                        var file = image;
                        var error = errFiles && errFiles[0];
                        file.upload = Upload.upload({
                            url: urls.BASE_API + '/image',
                            data: {
                                'image': image,
                                'size': 200
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
                        deferred.reject("图片格式不正确");
                    }
                    return deferred.promise;
                }

                this.addStaticImage = function(image, errFiles, imgType) {
                    var deferred = $q.defer();
                    if(image){
                        var file = image;
                        var error = errFiles && errFiles[0];
                        file.upload = Upload.upload({
                            url: urls.BASE_API + '/image',
                            data: {
                                'image': image,
                                'size': 300,
                                'type': imgType,
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
                        deferred.reject("图片格式不正确");
                    }
                    return deferred.promise;
                }

                this.delImage = function(image) {
                    var deferred = $q.defer();
                    $http.put(urls.BASE_API + '/image', {
                        'image': image
                    }).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
            }
        ]);

})();
