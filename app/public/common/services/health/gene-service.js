(function() {
    'use strict';

    angular.module('app')
        .service('GeneService', ['$http', 'urls', '$q',
            function($http, urls, $q) {
                var self = this;

                this.get_genes = function(name,pageindex,pagesize) {
                    var deferred = $q.defer();
                    if(name.length==0)
                        name='*';
                    $http.get(urls.BASE_API + '/genes/'+name+'/'+pageindex+'/'+pagesize).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
                this.gene_save=function (gene) {
                    var deferred = $q.defer();
                    var oper='create_gene';
                    if(gene.id!=0){
                        oper='update_gene';
                    }
                    $http.post(urls.BASE_API + '/'+oper, gene).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
                
                this.site_save=function (site) {
                    var deferred = $q.defer();
                    $http.post(urls.BASE_API + '/save_site', site).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }


            }
        ]);

})();
