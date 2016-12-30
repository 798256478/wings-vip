(function () {
    'use strict';
    
    angular.module('app')
    .controller('InitializeController', ['$http', 'urls','$scope','AuthService','CommodityService',
    function($http,urls, $scope, AuthService,CommodityService){
         $scope.conditions={};
         $scope.carts={};
         $scope.select_count=0;
         $scope.commodities=[];
         $scope.$watch('AuthService.current_user',function(newVal, oldVal)
         {
            if(newVal != null &&  $scope.commodities.length == 0 )
            {
                $scope.search();
                $scope.init();
            };
         })
        $scope.search=function() {
            CommodityService.getCommoditiesByConditions($scope.conditions).then(function (res) {
                $scope.commodities=res.commodities;
            },function (res) {
                $scope.errorInfo.message=res.message;
            });
        }
        $scope.init=function (params) {
             if($scope.cardInfo==null) {
                  $scope.initData={bonus:'',balance:'',level:''};
             }
             else{
                $scope.initData={};
                $scope.initData.bonus=parseInt($scope.cardInfo.card.bonus.split(",").join(""));
                $scope.initData.balance=(Number($scope.cardInfo.card.balance.split(",").join(""))).toFixed(2);
                $scope.initData.level=$scope.cardInfo.card.level+"";//int类型和string不匹配的话不会选中，
             }
        }
       
        $scope.$on('cardchange', function(event, data) {  
            $scope.init();
         }); 
         
         
        $scope.getDateStr=function(addDayCount) { 
            var dd = new Date(); 
            dd.setDate(dd.getDate()+addDayCount);//获取AddDayCount天后的日期 
            return dd;
        }   

        $scope.add=function (commodity,specification) {
        if(specification.quantity <= 0){
            $scope.errorInfo.message='商品售罄'; 
            return;
        }
        if(typeof($scope.carts[specification.id]) == "undefined"){
            $scope.carts[specification.id]={};
            $scope.carts[specification.id].specification=specification;
            $scope.carts[specification.id].commodity=commodity;
            $scope.carts[specification.id].sellable={quantity:specification.sellable_quantity, expiry_date: $scope.getDateStr(specification.sellable_validity_days)};
            $scope.select_count++;
        }
    };
        
        
        $scope.reduction=function (id) {
            if($scope.carts[id].num <= 0){
                $scope.errorInfo.message='数量不能小于0';
                return;
            }
            delete $scope.carts[id];
            $scope.select_count--;
        };
        
        $scope.checkData=function(){
            if($scope.cardInfo==null) {
                $scope.errorInfo.message="没有选择会员卡";
                return false;
            }
            return true;
        }
        
        
        $scope.submit=function(isvaild) {
            if(!$scope.checkData()){
                    return;
                }
                var properties=[];
                $scope.initData.card_id=$scope.cardInfo.card.id;
                for(var key in $scope.carts){
                var model={};
                model.card_id=$scope.cardInfo.card.id;
                model.property_template_id=$scope.carts[key].specification.sellable_id;
                model.expiry_date=$scope.carts[key].sellable.expiry_date;
                model.quantity=$scope.carts[key].sellable.quantity;
                if(isNaN(model.quantity)||model.quantity==0){
                    $scope.errorInfo.message="数量验证错误，请重新填写";
                    return;
                }
                properties.push(model);
            }
            $scope.initData.properties=properties;
            $http.post(urls.BASE_API + '/initCardData',{data: $scope.initData}).success(function(res){
                $scope.refreshCard();
                $scope.carts={};
                $scope.select_count=0;
                // $scope.init();
            }).error(function(res){
                $scope.errorInfo.message=res.message;
            });
        }
    }])

})();