(function () {
    'use strict';
    
    angular.module('app')
    .controller('BalanceController', ['$http', 'urls','$scope','SettingService',
    function($http,urls, $scope,SettingService){
        $scope.type=0;
        SettingService.getSetting('BALANCE').then(function (res) {
              $scope.balance_buys=res.buy;
              $scope.balance_exchanges=res.exchange;
        },function (res) {
             $scope.errorInfo.message=res.message;
        })
       $scope.changeType=function(type) {
            $scope.type=type;
       }
    }])
    
})();