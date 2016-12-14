(function() {
    'use strict';

    angular.module('app')
        .service('CommodityService', ['$http', 'urls', '$q',
            function($http, urls, $q) {
                var self = this;

                this.getCommodityList = function() {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/commodity').success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.getCommodityListWithCategory = function(categoryId) {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/commodityCategory/' + categoryId).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.getCommoditySpecificationsWithoutSuit = function() {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/commoditywithoutsuit').success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.getCommodity = function(id) {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/commodity/' + id).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.getCommodity = function(id) {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/commodityMarketer/' + id).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.addCommodity = function(commodity) {
                    var deferred = $q.defer();
                    $http.post(urls.BASE_API + '/commodity', commodity).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.updateCommodity = function(commodity) {
                    var deferred = $q.defer();
                    $http.put(urls.BASE_API + '/commodity', commodity).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.deleteCommodity = function(id) {
                    var deferred = $q.defer();
                    $http.delete(urls.BASE_API + '/commodity/' + id).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.search = function(val) {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/commoditysearch/' + val).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.getCommoditiesByConditions = function($conditions) {
                    var deferred = $q.defer();

                    $http.get(urls.BASE_API + '/getCommoditiesByConditions/' + JSON.stringify($conditions)).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.getSellableList = function(val) {
                    var url = '';
                    var deferred = $q.defer();
                    if (val == "App\\Models\\TicketTemplate") {
                        url = "ticket_template_usable_list";
                    } else if (val == "App\\Models\\PropertyTemplate") {
                        url = "propertytemplate";
                    } else {
                        deferred.resolve([]);
                        return deferred.promise;
                    }
                    $http.get(urls.BASE_API + '/' + url).success(function(res) {
                        if (res.ticket_templates) {
                            deferred.resolve(res.ticket_templates);
                        } else if (res.property_templates) {
                            deferred.resolve(res.property_templates);
                        } else if (res.products) {
                            deferred.resolve(res.products);
                        }
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.getCommodityCategoryList = function() {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/commodity_category').success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.getCommodityCategory = function(id) {
                    var deferred = $q.defer();
                    $http.get(urls.BASE_API + '/commodity_category/' + id).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.addCommodityCategory = function(commodityCategory) {
                    var deferred = $q.defer();
                    $http.post(urls.BASE_API + '/commodity_category', commodityCategory).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.updateCommodityCategory = function(commodityCategory) {
                    var deferred = $q.defer();
                    $http.put(urls.BASE_API + '/commodity_category', commodityCategory).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }

                this.deleteCommodityCategory = function(id) {
                    var deferred = $q.defer();
                    $http.delete(urls.BASE_API + '/commodity_category/' + id).success(function(res) {
                        deferred.resolve(res);
                    }).error(function(res) {
                        deferred.reject(res);
                    });
                    return deferred.promise;
                }
            }
        ]);

})();
