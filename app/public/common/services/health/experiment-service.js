(function() {
    'use strict';

    angular.module('app')
        .service('ExperimentService', ['$http', 'urls', '$q',
            function($http, urls, $q) {
                var self = this;

                this.get_experiments = function() {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/experiments').success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
                
                this.get_experiment = function(id) {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/experiment/'+id).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
                
                 this.get_sites_by_projectId= function(projectId) {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/get_sites_by_projectId/'+projectId).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
                this.save_experiment=function (experiment) {
                    var deferred = $q.defer();
                    $http.post(urls.BASE_API + '/save_experiment', experiment).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
                this.save_project=function (project) {
                    var deferred = $q.defer();
                    $http.post(urls.BASE_API + '/save_project', project).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
                this.save_sites=function (site) {
                    var deferred = $q.defer();
                    $http.post(urls.BASE_API + '/save_project_site', site).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
                
                this.get_risk_by_projectId= function(projectId) {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/get_risk_by_projectId/'+projectId).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
                this.save_risk=function (risks) {
                    var deferred = $q.defer();
                    $http.post(urls.BASE_API + '/save_risk', risks).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
                
                this.save_circumRisk=function (risks) {
                    var deferred = $q.defer();
                    $http.post(urls.BASE_API + '/save_circumRisk', risks).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

            }
        ]);

})();
