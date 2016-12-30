(function () {
    'use strict';

    angular.module('app')
    .service('SettingService', ['$http', 'urls', '$q', function($http, urls, $q){
        var self = this;
        this.getSetting = function(key){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/setting/' +key).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.setSetting = function(setting){
            var deferred = $q.defer();
            $http.put(urls.BASE_API + '/setting', setting).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getTheme = function(){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/settingTheme/').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getModule = function(){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/settingModule/').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

    }]);

})();
