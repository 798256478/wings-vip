(function () {
    'use strict';
    
    angular.module('app')
    .controller('MallController', ['$http', 'urls','$scope','AuthService','SettingService','CommodityService',
    function($http,urls, $scope, AuthService,SettingService,CommodityService){
        $scope.paymentselects={};
        $scope.commodities=[];
        $scope.carts={};//键值对，不能用forearch
        $scope.cart_count=0;
        $scope.order={total_price:0,total_bonus:0,money_pay_amount:0,bonus_pay_amount:0,remark:''};
        $scope.conditions={code:''};
        $scope.show_content = 'commodity';
        $scope.debt=0;
        $scope.$watch('AuthService.current_user',function(newVal, oldVal)
         {
            if(newVal != null && $scope.commodities.length == 0)
            {
                $scope.search();
            }
         })
         
        $scope.search=function() {
            CommodityService.getCommoditiesByConditions($scope.conditions).then(function (res) {
                $scope.commodities=res.commodities;
            },function (res) {
                $scope.errorInfo.message=res.message;
            });
        }
        
        $scope.add=function (commodity,specification) {
            if(specification.quantity <= 0){
                $scope.errorInfo.message='商品售罄'; 
                return;
            }
            if( typeof($scope.carts[specification.id]) == "undefined"){
                $scope.carts[specification.id]={};
                $scope.carts[specification.id].specification=specification;
                $scope.carts[specification.id].commodity=commodity;
                $scope.carts[specification.id].num=1;
                $scope.cart_count++;
            }
            else{
                if($scope.carts[specification.id].num+1>specification.quantity){
                   $scope.errorInfo.message='商品不足'; 
                    return;
                }
                $scope.carts[specification.id].num=$scope.carts[specification.id].num+1;
                $scope.cart_count++;
            }
            angular.element('#money_pay_amount').focus();
            $scope.order.total_price=(Number($scope.order.total_price)+Number(specification.price)).toFixed(2);
            $scope.order.money_pay_amount=(Number($scope.order.money_pay_amount)+Number(specification.price)).toFixed(2);
            $scope.order.total_bonus=Number($scope.order.total_bonus)+Number(specification.bonus_require);
            $scope.order.bonus_pay_amount=Number($scope.order.bonus_pay_amount)+Number(specification.bonus_require);
          
        };
         $scope.$watch('order.money_pay_amount',function (newVal,oldVal) {
              if(newVal===oldVal) return;
              if(isNaN(newVal)||newVal.length==0){
                    newVal=0;
              }
              if(isNaN(oldVal)||oldVal.length==0){
                    oldVal=0;
              }
              $scope.debt=(Number($scope.debt)+Number(newVal)-Number(oldVal)).toFixed(2);
              
         })
        
        $scope.reduction=function (id) {
            if($scope.carts[id].num <= 0){
                $scope.errorInfo.message='数量不能小于0';
                return;
            }
             $scope.cart_count--;
             $scope.carts[id].num=$scope.carts[id].num-1;
             $scope.order.total_price=(Number($scope.order.total_price)-Number($scope.carts[id].specification.price)).toFixed(2);
             $scope.order.total_bonus=Number($scope.order.total_bonus)-Number($scope.carts[id].specification.bonus_require);
             $scope.order.money_pay_amount=Number($scope.order.money_pay_amount)-Number($scope.carts[id].specification.price);
             $scope.order.bonus_pay_amount=Number($scope.order.bonus_pay_amount)-Number($scope.carts[id].specification.bonus_require);
             if($scope.carts[id].num==0){
                 delete $scope.carts[id];
             }
             if($scope.cart_count==0){
                  $scope.show_content='commodity';
             }
        };
        
        
        $scope.$watch('money_pay_amount',function (newVal,oldVal) {
            if(newVal===oldVal) return;
            if(isNaN(oldVal)||oldVal.length==0)
                oldVal=0;
            if(isNaN(newVal)||newVal.length==0)
                newVal=0;
            $scope.debt=(Number($scope.debt)+Number(newVal)-Number(oldVal)).toFixed(2);//未结清
            //计算最大可使用积分
            $scope.max=Number.MAX_VALUE;
            if(Number($scope.bonus_rule.limit)>0){
                 $scope.max=Number($scope.bonus_rule.limit);
            }
            var usemax=(Number($scope.bonus_rule.use)*newVal).toFixed(2);;
            if(Number($scope.bonus_rule.use)>0 && $scope.max > usemax){
                 $scope.max=usemax;
            }
        });
        
        
        $scope.change_show=function(status) {
            $scope.show_content = status;
        };
        

        
        $scope.clear=function() {
            $scope.carts={};
            $scope.order={total_price:0,total_bonus:0,money_pay_amount:0,bonus_pay_amount:0};
            $scope.cart_count=0;
            $scope.show_detail=false;
            $scope.money_pay_amount=0;
            $scope.paymentselects={};
            $scope.show_content = 'commodity';
        };
        
        $scope.checkData=function(params) {
             if($scope.cardInfo==null) {
               $scope.errorInfo.message="没有选择会员卡";
                return false;
            }
            if( $scope.cart_count==0){
               $scope.errorInfo.message="购物车为空，快来添加商品吧";
                return false;
            }
            if(typeof($scope.paymentselects['余额']) != "undefined"&&Number($scope.paymentselects['余额'].amount)>Number($scope.cardInfo.card.balance)){
                $scope.errorInfo.message='余额不足';
                $scope.paymentselects['余额'].amount=$scope.cardInfo.card.balance;
                return false;  
            }
            var bonus_payment=$scope.paymentselects['积分抵扣'];
            if(typeof(bonus_payment) != "undefined"){
                var use_bonus =  (bonus_payment.amount*Number($scope.bonus_rule.exchange)).toFixed(2);
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
            var payments=[];
            for(var key in $scope.paymentselects){
                  var payment={};
                  payment.type = $scope.paymentselects[key].type;
                  payment.name = $scope.paymentselects[key].name;
                  payment.amount = $scope.paymentselects[key].amount;
                  if(payment.type == 'BONUS')
                  {
                      payment.use_bonus = payment.amount;
                      payment.amount = (payment.use_bonus * Number($scope.bonus_rule.exchange)).toFixed(2);
                  }
                  payments.push(payment);
            }

            //订单明细
            var items=[];
            for(var key in $scope.carts){
                var model={};
                model.specificationId=$scope.carts[key].specification.id;
                model.quantity=$scope.carts[key].num;
                items.push(model);
            }
            var parms={};
            var parms={
                channel:'SHOP',
                cardid:$scope.cardInfo.card.id,
                data:{
                        items:items,
                        payments:payments,
                        remark: $scope.order.remark,
                        cashier_id:AuthService.current_user.id,
                        bonus_require:$scope.order.total_bonus,
                        cashier_price_deductions:$scope.order.total_price-$scope.order.money_pay_amount,
                        cashier_bonus_deductions:$scope.order.total_bonus-$scope.order.bonus_pay_amount,
                    },
            }
            $http.post(urls.BASE_API + '/createGoodsOrder',parms).success(function(res){
                $scope.clear();
                $scope.refreshCard();
            }).error(function(res){
               $scope.errorInfo.message=res.message;
            });
        };
        
        $scope.quality=function(paymentStr) {
            $scope.qualitydata={paymentStr:paymentStr,amount: $scope.order.money_pay_amount};
        };
    }])
    
})();