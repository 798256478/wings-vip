(function() {
    'use strict';

    angular.module('app')
        .service('EventService', ['$http', 'urls', '$q', function($http, urls, $q) {
            var self = this;

            this.getEventList = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/event').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getEventRuleList = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/eventRulelist').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getJobList = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/event/jobs').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getEventRules = function(id) {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/event/rules').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.addEventRules = function(rule) {
                var deferred = $q.defer();
                $http.post(urls.BASE_API + '/event', rule).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.updateEventRules = function(rule) {
                var deferred = $q.defer();
                $http.put(urls.BASE_API + '/event', rule).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.delEventRules = function(ruleId) {
                var deferred = $q.defer();
                $http.delete(urls.BASE_API + '/event/' + ruleId).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }
        }]);

})();
