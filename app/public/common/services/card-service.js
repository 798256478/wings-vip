(function () {
    'use strict';

    angular.module('app')
    .service('CardService', ['$http', 'urls', '$q', function($http, urls, $q){
        var self = this;

        this.getCardSummaries = function() {
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/cardSummaries').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getNewCardSummaries = function(){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/cardNewSummaries').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getCardDetail = function(cardid){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/cardDetail/' + cardid).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.Search = function(val){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/cardSearch/' + val).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }
        this.getCard = function(cardid){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/card/' + cardid).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

        this.getTotal = function(){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/cardTotal').success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }

    }]);

})();
