'use strict';

angular.module('app')
    .controller('DealRefundController', ['$scope', '$location', 'AuthService', 'RefundService', '$q','$state','$stateParams',
        function($scope, $location, AuthService, RefundService, $q,$state,$stateParams) {
        	if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('退款');

            $scope.id = $stateParams.id;

            $scope.orderState={
                'NOT_PAY':'等待买家付款',
                'PAY_SUCCESS':'已付款',
                'DELIVERED':'已发货',
                'FINISH':'交易成功',
                'CLOSED':'关闭'
            }

            $scope.refundState={
                'APPLY':'买家申请退款',
                'REFUND':'同意退款',
                'REFUSED':'拒绝退款',
                'CLOSE':'买家已取消退款申请'
            }

            $scope.orderState = {
                'NOT_PAY': '待付款',
                'PAY_SUCCESS': '已支付',
                'DELIVERED': '已发货',
                'FINISH': '完成',
                'CLOSED': '关闭'
            };

            $scope.refund={
                'id':$scope.id,
                'state':'REFUND',
                'money':null,
                'is_refund_other':false,
                'instructions':''
            }

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

            $scope.getData=function(id){
            	RefundService.getRefundData(id).then(function(res){
            		$scope.refundData=res.refundData;
                    $scope.commodityData = new Array();
                    $scope.suit = res.refundData.order.suit ? res.refundData.order.suit : false;
                    console.log(res);
                    if(res.refundData.order_detail){
                        ($scope.commodityData)[0] = res.refundData.order_detail;
                    } else {
                        $scope.commodityData = res.refundData.order.order_details;
                    }
                    $scope.refund_reason=res.refund_reason.refund_reason;
                    $scope.refundData.order.total = {};
                    $scope.refundData.order.total.price = 0;
                    $scope.refundData.order.total.bonus = $scope.refundData.order.bonus_pay_amount;
                    $scope.refundData.order.total.deduction = {};
                    angular.forEach($scope.refundData.order.order_payments,function(val,key){
                        if(val.type == 'BONUS'){
                            $scope.refundData.order.total.bonus += val.use_bonus;
                            $scope.refundData.order.total.deduction.bonus = val.use_bonus;
                            $scope.refundData.order.total.deduction.price = val.amount;
                        }else{
                            $scope.refundData.order.total.price += val.amount;
                        }
                    });
            	},function(res){
            		alert(res.message);
            	})
            }

            $scope.submit=function(isvalid){
                if(isvalid){
                    RefundService.dealRefund($scope.refund).then(function(res){
                        if(res){
                            swal('退款处理成功');
                            $location.path("/refund");
                        }
                    },function(res){
                        alert(res.message);
                    });
                }
            }

            $scope.initData=function(){
            	$scope.getData($scope.id);
            }

            $scope.initData();
           }
    ]);