/**
 * property Controller
 */
'use strict';

angular.module('app')
    .controller('OrderDetailsController', ['$scope','$stateParams', '$location', 'AuthService', 'OrderService', '$q',
        function($scope,$stateParams, $location, AuthService, OrderService, $q) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('订单详情');
            $scope.orderState = {
                'NOT_PAY': '买家下单',
                'PAY_SUCCESS': '买家付款',
                'DELIVERED':'卖家发货',
                'FINISH': '订单完成',
                'CLOSED': '关闭'
            };
            $scope.orderType={
                'CONSUME': '消费',
                'BALANCE': '储值',
                'GOODS': '商品'
                };
            $scope.orderChannel={
                'SHOP': '店内',
                'WECHAT': '微信',
                'DELIVERY': '快递'
                };
            $scope.refundState= {
                'APPLY': '申请退款',
                'REFUND': '卖家同意退款',
                'REFUSED': '卖家拒绝退款',
                'CLOSE': '退款申请已取消'
            };

            $scope.order = {};
            $scope.id=$stateParams.id;
            $scope.getOrder = function(id) {
                $scope.order={};
                OrderService.getOrder(id).then(function(res) {
                    $scope.order = res.order;
                    $scope.suit = res.suit ? res.suit : false;
                    $scope.order.total = {};
                    $scope.order.total.price = 0;
                    $scope.order.total.bonus = $scope.order.bonus_pay_amount;
                    $scope.order.total.deduction = {};
                    angular.forEach($scope.order.order_payments,function(val,key){
                        if(val.type == 'BONUS'){
                            $scope.order.total.bonus += val.use_bonus;
                            $scope.order.total.deduction.bonus = val.use_bonus;
                            $scope.order.total.deduction.price = val.amount;
                        }else{
                            $scope.order.total.price += val.amount;
                        }
                    });
                }, function(res) {
                    alert(res.message);
                });
            };
            $scope.getOrder($scope.id);
            $scope.backList=function(){
                history.back();
            }

        }
    ]);
