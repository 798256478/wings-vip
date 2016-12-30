(function () {
    'use strict';
    
    angular.module('app')
    .controller('BalanceBuyController', ['$http', 'urls','$scope','AuthService',
    function($http,urls, $scope, AuthService){
        $scope.paymentselects={};
        $scope.order={balance_fee:'',balance_present:'',total:'',remark:'',money_pay_amount:''};
        $scope.debt=0;

        $scope.$watch('order.balance_fee',function (newVal,oldVal) {
            if(newVal===oldVal) return;
            if(isNaN(oldVal)||oldVal.length==0)
                oldVal=0;
            var balance_fee=$scope.order.balance_fee;
            if(isNaN(balance_fee)||balance_fee.length==0){
                  balance_fee=0;
            }
            var rate=0;var max=0;
            for(var k in $scope.balance_buys) {
                if(Number(balance_fee) >= Number(k) && Number(max) <= Number(k)){
                    max=k;
                    rate=$scope.balance_buys[k];
                }
            }
            var balance_present=(Number(balance_fee)*Number(rate)).toFixed(2);
            $scope.order.balance_present=balance_present;
            $scope.order.total=(Number(balance_fee)+Number(balance_present)).toFixed(2);
            $scope.debt=(Number($scope.debt)+Number(balance_fee)-Number(oldVal)).toFixed(2);//未结清
        });
        
        $scope.$watch('order.balance_present',function(newVal,oldVal) {
            if(newVal===oldVal) return;
            var balance_present=$scope.order.balance_present;
            if(isNaN(balance_present)||balance_present.length==0){
                  balance_present=0;
                  $scope.order.balance_present='';
            }
            var balance_fee=$scope.order.balance_fee;
            if(isNaN(balance_fee)||balance_fee.length==0){
                  balance_fee=0;
            }
             $scope.order.total=(Number(balance_fee)+Number(balance_present)).toFixed(2);
			  
        })
        
        $scope.checkData=function(){
            if($scope.cardInfo==null) {
                $scope.errorInfo.message="没有选择会员卡";
                return false;
            }
            if($scope.order.balance_fee.length <= 0||isNaN($scope.order.balance_fee)){
                $scope.errorInfo.message='储值金额必须大于0';
                return false;
            }
            return true;
        }
        $scope.submit=function(isValid) {
            if(!$scope.checkData()){
                return;
            }
            swal({
                title: "确认储值吗？",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: true 
            },
            function()
            {
                $scope.do_submit();
            });
        }
        
        $scope.do_submit=function() {
            var payments=[];
            for(var key in $scope.paymentselects){
                var payment={};
                payment.type = $scope.paymentselects[key].type;
                payment.name = $scope.paymentselects[key].name;
                payment.amount = $scope.paymentselects[key].amount;
                payments.push(payment);
            }
            var parms={
                channel:'SHOP',
                cardid:$scope.cardInfo.card.id,
                data:{
                        payments:payments,
                        balance_fee: $scope.order.balance_fee,
                        balance_present:$scope.order.balance_present,
                        remark: $scope.order.remark,
                        cashier_id:AuthService.current_user.id,
                    },
            }
            $http.post(urls.BASE_API + '/createBalanceOrder',parms).success(function(res){
                $scope.order={balance_fee:'',balance_present:'',total:'',remark:''};
                $scope.paymentselects={};
                $scope.refreshCard();
                $scope.balance_buy_form.$setPristine();
            }).error(function(res){
                 $scope.errorInfo.message=res.message;
            });
        }
        
        $scope.quality=function(paymentStr) {
            $scope.qualitydata={paymentStr:paymentStr,amount: $scope.order.balance_fee}; 
        };
    }])
    
})();