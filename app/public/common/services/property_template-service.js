(function () {
    'use strict';

    angular.module('app')
    .service('PropertyTemplateService', ['$http', 'urls', '$q', function($http, urls, $q){
        var self = this;

        this.getPropertyTemplateList = function(){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/propertytemplate').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getPropertyTemplate = function(id){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/propertytemplate/' + id).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.addPropertyTemplate = function(propertyTemplate){
            var deferred = $q.defer();
            $http.post(urls.BASE_API + '/propertytemplate', propertyTemplate).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.updatePropertyTemplate = function(propertyTemplate){
            var deferred = $q.defer();
            $http.put(urls.BASE_API + '/propertytemplate', propertyTemplate).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.deletePropertyTemplate = function(id){
            var deferred = $q.defer();
            $http.delete(urls.BASE_API + '/propertytemplate/' + id).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getIcons = function(){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/icons').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }
    }]);

})();
