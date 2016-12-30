(function() {
    'use strict';

    angular.module('app')
        .service('ProjectDataService', ['$http', 'urls', '$q', 'Upload', '$timeout',
            function($http, urls, $q, Upload, $timeout) {
            var self = this;

            this.getExperimentDataList = function(progress,code,experimentId,page,pageSize){
                var deferred = $q.defer();
                var searchDTO = {
                    progress: progress,
                    code:code,
                    experimentId:experimentId,
                    pageSize:pageSize,
                    page: page,
                };
                $http.get(urls.BASE_API + '/experimentData/'+JSON.stringify(searchDTO)).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getDetailById = function(id){
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/experimentData/getDetailById/'+id).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }
            
            this.getRiskById = function(id){
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/experimentData/getRiskById/'+id).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.editProjectData= function(recordList){
                var deferred = $q.defer();
                $http.post(urls.BASE_API + '/projectData/save', recordList).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.editRiskData= function(recordList){
                var deferred = $q.defer();
                $http.post(urls.BASE_API + '/riskData/save', recordList).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.search = function(progress, searchValue){
                var deferred = $q.defer();
                var data = {
                    'progress': progress,
                    'value': searchValue
                };
                $http.post(urls.BASE_API + '/projectData/search',data).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }
        }]);

})();
