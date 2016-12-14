(function () {
    'use strict';
    
    angular.module('app')
    .controller('BalanceExchangeController', ['$http', 'urls','$scope','AuthService',
    function($http,urls, $scope, AuthService,SettingService){
        $scope.checkData=function(bonus){
            if($scope.cardInfo==null) {
                $scope.errorInfo.message="没有选择会员卡";
               
                return false;
            }
            if(Number($scope.cardInfo.card.bonus) < Number(bonus)){
                $scope.errorInfo.message="积分不足";
                return false;
            }
            return true;
        }
        $scope.doSubmit=function(bonus,balance) {
             var parms={
                channel:'SHOP',
                cardid:$scope.cardInfo.card.id,
                data:{
                        balance_fee: balance,
                        balance_present:0,
                        bonus_require:bonus,
                        remark:'',
                        cashier_id:AuthService.current_user.id,
                    },
             }
             $http.post(urls.BASE_API + '/createBalanceOrder',parms).success(function(res){
                $scope.refreshCard();
            }).error(function(res){
                 $scope.errorInfo.message=res.message;
            });
        }
        
        $scope.submit=function (bonus,balance) {
            if(!$scope.checkData(bonus)){
                 return;
             }
            swal({
                title: "确认用"+bonus+"积分兑换"+balance+"余额吗？",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: true 
            },
            function()
            {
                $scope.doSubmit(bonus,balance);
                    $scope.$apply();
            });
        }
        
    }])
    
})();