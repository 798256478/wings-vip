(function () {
    'use strict';

    angular.module('app')
    .service('MassService', ['$http', 'urls', '$q', function($http, urls, $q){
        var self = this;

        this.getDefaultMass = function(){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/mass').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getSendTop = function(){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/mass/sendtop').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getQueryList = function(advancedQuery){
            var deferred = $q.defer();
            $http.post(urls.BASE_API + '/mass', advancedQuery).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getMassTemplateList = function(){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/mass/template').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getMassTemplate = function($id){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/mass/template/'+ $id).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.saveMassTemplate = function(massTemplate){
            var deferred = $q.defer();
            $http.post(urls.BASE_API + '/mass/template', massTemplate).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.delMassTemplate = function($id){
            var deferred = $q.defer();
            $http.delete(urls.BASE_API + '/mass/template/'+ $id).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.sendMessage = function(message){
            var deferred = $q.defer();
            $http.post(urls.BASE_API + '/mass/send', message).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getMassHistory = function(){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/mass/history').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }
    }]);

})();
