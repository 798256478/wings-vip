/**
 * property Controller
 */
'use strict';

angular.module('app')
    .controller('OrderController', ['$scope', '$location', 'AuthService', 'OrderService', '$q','$state',
        function($scope, $location, AuthService, OrderService, $q,$state) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('订单管理');
            //alert(document.documentElement.clientHeight-75);
            $scope.widget_height={height:''+document.documentElement.clientHeight-93+'px','overflow-y':'auto'};
            $scope.orderState = {
                'NOT_PAY': '待付款',
                'PAY_SUCCESS': '已支付',
                'FINISH': '完成',
                'CLOSED': '关闭'
            };
            $scope.order = {
                'data': {},
                'record':{}
            };
            $scope.pages = {
                'nowPage': 1
            };
            $scope.sort = {
                'state': {
                    'sel': 'ALL',
                    'list': {
                        'ALL': '全部',
                        'NOT_PAY': '待付款',
                        'PAY_SUCCESS': '已支付',
                        'DELIVERED': '已发货',
                        'FINISH': '完成',
                        'CLOSED': '关闭'
                    }
                },
                'type': {
                    'sel': 'ALL',
                    'list': {
                        'ALL': '全部',
                        'CONSUME': '消费',
                        'BALANCE': '储值',
                        'GOODS': '商品'
                    }
                },
                'channel': {
                    'sel': 'ALL',
                    'list': {
                        'ALL': '全部',
                        'SHOP': '店内',
                        'WECHAT': '微信',
                        'DELIVERY': '快递'
                    }
                },
                'number':{
                    'sel':''
                },
                'time':{
                    'start':'',
                    'finish':''
                },
                'isShow':{
                    'closeOrder':false
                }
            };
            $scope.isShow='list';
            $scope.option={
                'val':'',
                'options': {
                    'state': $scope.sort.state.sel,
                    'type': $scope.sort.type.sel,
                    'channel': $scope.sort.channel.sel,
                    'number': $scope.sort.number.sel,
                    'time': {
                        'start': $scope.sort.time.start,
                        'finish': $scope.sort.time.finish
                    },
                    'page':$scope.pages.nowPage,
                    'showClose':$scope.sort.isShow.closeOrder
                }
            };
            $scope.batch={
                'sel':{},
                'list':{},
                'isNull':true,
                'isAllBatch':false
            };
            $scope.refundState= {
                'APPLY': '申请退款',
                'REFUND': '卖家同意退款',
                'REFUSED': '卖家拒绝退款',
                'CLOSE': '退款申请已取消'
            };

            $scope.getOrderData = function(){
                OrderService.getOrderData($scope.option.val).then(function(res) {
                    $scope.orderList = res['order'];
                    $scope.pages.orderTotal=res['count'];
                    $scope.batch={
                        'sel':{},
                        'list':{},
                        'isNull':true
                    };
                    $scope.batch.isAllBatch=false;
                    angular.forEach($scope.orderList,function(val,key){
                        if(val.state=='PAY_SUCCESS'&&val.is_need_delivery){
                            $scope.batch.list[val.id]=val.id;
                            $scope.batch.isNull=false;
                        }
                        val.total = {};
                        val.total.bonus = val.bonus_pay_amount;
                        val.total.price = 0;
                        val.total.deduction = {};
                        angular.forEach(val.order_payments,function(a,b){
                            if(a.type =='BONUS'){
                                val.total.bonus += a.use_bonus;
                                val.total.deduction.bonus = a.use_bonus;
                                val.total.deduction.price = a.amount;
                            }else{
                                val.total.price += a.amount;
                            }
                        })
                    })
                    //console.log($scope.batch.list);

                }, function(res) {
                    alert(res.message);
                });
            }


            //重新加载列表,页数改为1
            $scope.getOrderTotal = function() {
                $scope.pages.nowPage=1;
                $scope.option={
                    'val':'',
                    'options': {
                        'state': $scope.sort.state.sel,
                        'type': $scope.sort.type.sel,
                        'channel': $scope.sort.channel.sel,
                        'number': $scope.sort.number.sel,
                        'time': {
                            'start': $scope.sort.time.start,
                            'finish': $scope.sort.time.finish
                        },
                        'page':$scope.pages.nowPage,
                        'showClose':$scope.sort.isShow.closeOrder
                    }
                };
                $scope.option.val=angular.toJson($scope.option.options,true);
                $scope.getOrderData();
            };

            //加载下一页列表
            $scope.getOrderList = function() {
                $scope.option.options.page=$scope.pages.nowPage
                $scope.option.val=angular.toJson($scope.option.options,true);
                $scope.getOrderData();
            };

            $scope.init=function() {
                $scope.getOrderTotal();
            };

            $scope.init();

            $scope.search=function(){
                $scope.getOrderTotal();
            };

            $scope.$watchCollection('pages.nowPage', function(newVal, oldVal) {
                if(newVal==oldVal){
                    return false;
                }
                $scope.getOrderList($scope.sort.sel,newVal );
            });

            $scope.notShowCloseOrder=function(){
                $scope.getOrderTotal();
            };


            //成交时间
            $scope.datePicker = {
                format: 'yyyy-MM-dd',
                minDate: new Date(),
                dateOptions: {
                    formatYear: 'yyyy',
                    startingDay: 1
                }
            };
            $scope.startTime={
                'opened':false
            };
            $scope.finishTime={
                'opened':false
            };
            $scope.selectStartTime=function(){
                $scope.startTime.opened=true;
            };

            $scope.selectFinishTime=function(){
                $scope.finishTime.opened=true;
            };

            //批量
            $scope.$watch('batch.isAllBatch',function(newVal,oldVal){
                if(newVal==oldVal){
                    return;
                }
                if(newVal) {
                    $scope.batch.sel = angular.copy($scope.batch.list);
                }else{
                    $scope.batch.sel={};
                }
            });

            $scope.batchDeliver=function(){
                var a=false;
                angular.forEach($scope.batch.sel,function(val){
                    if(!a){
                        if(val){
                            a=true;
                        }
                    }
                });
                if(!a){
                    alert('没有选择项');
                    return;
                }
                if($scope.batch.sel){
                    $state.go('deliver',{id:$scope.batch.sel});
                }
            };

            $scope.deliver=function(id){
                $state.go('deliver',{id:id});
            };

            $scope.detail=function(id){
                $state.go('order_details',{id:id});
            };

            //$scope.$watchCollection('sort.sel', function(newVal, oldVal) {
            //    $scope.getOrderTotal(newVal).then(function(res) {
            //        if ($scope.pages.nowPage != 1) {
            //            $scope.pages.nowPage = 1;
            //        } else {
            //            $scope.getOrderList(newVal,1);
            //        }
            //    }, function(res) {});
            //});

            $scope.getOrder = function(id) {
                $scope.order={};
                OrderService.getOrder(id).then(function(res) {
                    $scope.order.data = res.order;
                    $scope.order.record = res.record;
                    $scope.isShow='details';
                }, function(res) {
                    alert(res.message);
                });
            };

            $scope.submit = function(isValid) {
                if (isValid) {
                    if ($scope.order.id) {
                        OrderService.updateOrder($scope.order).then(function(res) {
                            $scope.getOrderList();
                            $scope.getOrder(res);
                        }, function(res) {
                            alert(res.message);
                            $scope.error = res.errors;
                        });
                    }
                }
            };

            $scope.clearData = function() {
                $scope.order.data = {};
                $scope.getOrderList();
            }

            $scope.clearData();
        }
    ]);
