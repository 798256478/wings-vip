(function () {
    'use strict';
    
    angular.module('app')
    .controller('ConsumeController', ['$http', 'urls','$scope','AuthService','SettingService',
    function($http,urls, $scope, AuthService,SettingService){
        $scope.paymentselects={};
        $scope.order={total_fee:'',bonus_present:'',remark:''};
        $scope.debt=0;
        
        $scope.$watch('order.total_fee',function (newVal,oldVal) {
            if(newVal===oldVal) return;
            if(isNaN(oldVal)||oldVal.length==0)
                oldVal=0;
            var total_fee=$scope.order.total_fee;
            if(isNaN(total_fee)||total_fee.length==0){
                  total_fee=0;
            }
            var rate= $scope.payments.consume.bonus;
            var bonus_present=(Number(total_fee)*Number(rate)).toFixed(0);
            $scope.order.bonus_present=bonus_present;
            $scope.debt=(Number($scope.debt)+Number(total_fee)-Number(oldVal)).toFixed(2);//未结清
            
             //计算最大可使用积分
            $scope.max=Number.MAX_VALUE;
            if(Number($scope.bonus_rule.limit)>0){
                 $scope.max=Number($scope.bonus_rule.limit);
            }
            var usemax=(Number($scope.bonus_rule.use)*newVal).toFixed(2);
            if(Number($scope.bonus_rule.use)>0 && $scope.max > usemax){
                 $scope.max=usemax;
            }
        });
        
        

        $scope.checkData=function()
        {
            if($scope.cardInfo==null){
                $scope.errorInfo.message="没有选择会员卡";
                return false;
            }
            if($scope.order.total_fee.length <= 0||isNaN($scope.order.total_fee)){
                $scope.errorInfo.message='消费金额必须大于0';
                return false;
            }
            if(typeof($scope.paymentselects['余额']) != "undefined"&&Number($scope.paymentselects['余额'].amount)>Number($scope.cardInfo.card.balance)){
                $scope.errorInfo.message='余额不足';
                $scope.paymentselects['余额'].amount=$scope.cardInfo.card.balance;
                return false;  
            }
            var bonus_payment=$scope.paymentselects['积分抵扣'];
            if(typeof(bonus_payment) != "undefined"){
                var use_bonus = (bonus_payment.amount*Number($scope.bonus_rule.exchange)).toFixed(2);
                if(Number(use_bonus)>Number($scope.cardInfo.card.bonus)) {
                    $scope.errorInfo.message='积分不足';
                    return false;
                }
                if(Number(use_bonus)>$scope.max){
                    $scope.errorInfo.message='可使用积分不能超过'+($scope.max/Number($scope.bonus_rule.exchange)).toFixed(0);
                    return false;
                }
            }
           return true;
        }
        
        $scope.submit=function(isValid) {
             if(!$scope.checkData()){
                 return;
             }
            swal({
                title: "确认消费吗？",
                type: "warning",
                showCancelButton: true,
                closeOnConfirm: true 
            },
            function()
            {
                $scope.do_submit();
            });
  
             
        };
        
        $scope.do_submit=function() {
            var payments=[];
            for(var key in $scope.paymentselects){
                var payment={};
                payment.type = $scope.paymentselects[key].type;
                payment.name = $scope.paymentselects[key].name;
                payment.amount = Number($scope.paymentselects[key].amount);
                if(payment.type == 'BONUS')
                {
                      payment.use_bonus = payment.amount;
                      payment.amount = payment.use_bonus * Number($scope.bonus_rule.exchange);
                }
                payments.push(payment);
            }
            var parms={
                channel:'SHOP',
                cardid:$scope.cardInfo.card.id,
                data:{
                        payments:payments,
                        total_fee: Number($scope.order.total_fee),
                        remark: $scope.order.remark,
                        cashier_id:AuthService.current_user.id,
                        bonus_present:$scope.order.bonus_present,
                    },
            }
            $http.post(urls.BASE_API + '/createConsumeOrder',parms).success(function(res){
                $scope.order={total_fee:'',bonus_present:'',remark:''};
                $scope.paymentselects={};
                $scope.refreshCard();
                $scope.consume_form.$setPristine();
            }).error(function(res){
                $scope.errorInfo.message=res.message;
            });
        }
        
        
        $scope.quality=function(paymentStr) {
            $scope.qualitydata={paymentStr:paymentStr,amount: $scope.order.total_fee};
        };
        
        
    }])
    
})();