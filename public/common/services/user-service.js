(function() {
    'use strict';

    angular.module('app')
        .service('UserService', ['$http', 'urls', '$q', function($http, urls, $q) {
            var self = this;

            this.getUsers = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/user').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getUser = function(userid) {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/user/' + userid).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.addUser = function(user) {
                var deferred = $q.defer();
                $http.post(urls.BASE_API + '/user', user).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.updateUser = function(user) {
                var deferred = $q.defer();
                $http.put(urls.BASE_API + '/user', user).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.deleteUser = function(id) {
                var deferred = $q.defer();
                $http.delete(urls.BASE_API + '/user/' + id).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getLoginRecords=function(option,page,search){
                var deferred = $q.defer();
                var a;
                if(search){
                    a=urls.BASE_API + '/loginRecords/' + option +'/'+ page +'/'+ search;
                }else{
                    a=urls.BASE_API + '/loginRecords/' + option +'/'+ page;
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
