(function() {
    'use strict';

    angular.module('app')
        .service('TicketTemplateService', ['$http', 'urls', '$q', function($http, urls, $q) {
            var self = this;

            this.getTicketTemplateList = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/ticket_template').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getUsableTicketTemplateList = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/ticket_template_usable_list').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getTicketTemplateTypeList = function() {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/ticket_template_type_list').success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.getTicketTemplate = function(ticket_template_id) {
                var deferred = $q.defer();
                $http.get(urls.BASE_API + '/ticket_template/' + ticket_template_id).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.addTicketTemplate = function(ticket_template) {
                var deferred = $q.defer();
                $http.post(urls.BASE_API + '/ticket_template', ticket_template).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.updateTicketTemplate = function(ticket_template) {
                var deferred = $q.defer();
                $http.put(urls.BASE_API + '/ticket_template', ticket_template).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            this.deleteTicketTemplate = function(ticket_template_id) {
                var deferred = $q.defer();
                $http.delete(urls.BASE_API + '/ticket_template/' + ticket_template_id).success(function(res) {
                    deferred.resolve(res);
                }).error(function(res) {
                    deferred.reject(res);
                });
                return deferred.promise;
            }
        }]);

})();
