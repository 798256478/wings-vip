(function () {
    'use strict';

    angular.module('app')
    .service('TicketService', ['$http', 'urls', '$q', function($http, urls, $q){
        var self = this;

        /**
         * 根据用户id获取该用户的有效优惠券列表
         * @method function
         * @param  {int} cardid 用户id
         * @return {array}
         */
        this.getCardTicketList = function(cardid){
            var deferred = $q.defer();
            $http.get(urls.BASE_API + '/cardTicketList/' + cardid).success(function(res){
                deferred.resolve(res);
            }).error(function(res){
                deferred.reject(res);
            });
            return deferred.promise;
        }
    }]);

})();
