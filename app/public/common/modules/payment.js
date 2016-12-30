 angular.module('angu-payment',[])
    .directive('payment',['$parse','$http','$sce','SettingService',paymentDirective]);   
    function paymentDirective($parse,$http, $sce,SettingService)
    {
        return {
            restrict:'E',
            scope:{
                "amount":"=amount",
                "debt":"=debt",
                "payment":"=payment",
                "paymentselects":"=paymentselects",
                "qualitydata":"=qualitydata",
                'bonus':'=bonus',
                "operation":"&operation",
                "bonus_rule":"=bonusrule",
            },
            template:'<div class="payment-wrapper" >'+
                        '<div class="input-group" ng-repeat=" (key,item) in payment.methods">'+
                            ' <div class="input-group-addon {{paymentselects[key]?\'select-addon\':\'\'}}" ng-click="select_payment(key,item)"> '+
                                '<input  type="checkbox" ng-model="paymentselects[key]?true:false ">{{key}}'+
                            '</div>'+
                            ' <input mnk-input  type="text" class="form-control"   ng-disabled="paymentselects[key]?false:true"   ng-model="paymentselects[key].amount">'+
                        '</div>'+
                    '</div>',
            replace: true,
            transclude: true,
            link:function ($scope,element,attrs) {
                $scope.$watch('paymentselects',function (newVal,oldVal) {
                    if(newVal==oldVal) return;
                    var bonus=0;
                    var payment_all=0;
                    $.each(newVal, function(key, item){   
                        var newfloat=item.amount;
                        if(isNaN(newfloat)||newfloat.length==0)
                            newfloat=0;
                        if(item.type != 'BONUS')
                            payment_all = (Number(payment_all)+Number(newfloat)).toFixed(2);
                        else
                            payment_all = (Number(payment_all)+Number(newfloat)*$scope.bonus_rule.exchange).toFixed(2);
                        bonus=bonus+Number(newfloat)*$scope.payment.bonus*item.rate;
                    }); 
                    $scope.debt=(Number($scope.amount)-Number(payment_all)).toFixed(2); 
                    if(!isNaN($scope.bonus)&&bonus>0)
                        $scope.bonus=bonus;
                },true);
                
                $scope.select_payment=function (key,model) {
                    if($scope.paymentselects[key]){
                        delete $scope.paymentselects[key];
                    }
                    else{
                        $scope.paymentselects[key]={};
                        if(model.type == 'BONUS')
                        {
                             $scope.paymentselects[key].amount = Number($scope.debt/$scope.bonus_rule.exchange).toFixed(0);
                        }
                        else
                            $scope.paymentselects[key].amount=$scope.debt;
                        $scope.paymentselects[key].type=model.type;
                        $scope.paymentselects[key].name=model.name;
                    }  
                }
                $scope.$watch('qualitydata',function (newVal,oldVal) {
                    if(newVal!=null){
                        var key=$scope.qualitydata.paymentStr;
                        $scope.paymentselects[key]={};
                        $scope.paymentselects[key].amount=$scope.qualitydata.amount;
                        $scope.paymentselects[key].type='CASHIER';
                        $scope.paymentselects[key].name=key;
                        $scope.operation();
                        $scope.qualitydata=null;
                    }
                });
                
            }
        }
    }