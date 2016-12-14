(function () {
    'use strict';

    angular.module('app')
    .service('RedeemCodeService', ['$http', 'urls', '$q', function($http, urls, $q){
        var self = this;

        this.getRedeemCodeList = function(){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/redeemcode').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getRedeemCode = function(id){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/redeemcode/' + id).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.addRedeemCode = function(redeemCode){
            var deferred = $q.defer();
            $http.post(urls.BASE_API + '/redeemcode', redeemCode).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.updateRedeemCode = function(redeemCode){
            var deferred = $q.defer();
            $http.put(urls.BASE_API + '/redeemcode', redeemCode).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.generationCode = function(id, amount){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/generationCode/' + id + '/' + amount).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.deleteRedeemCode = function(id){
            var deferred = $q.defer();
            $http.delete(urls.BASE_API + '/redeemcode/' + id).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }
    }]);

})();
