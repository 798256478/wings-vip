'use strict';

angular.module('app')
    .controller('RefundController', ['$scope', '$location', 'AuthService', 'RefundService', '$q','$state',
        function($scope, $location, AuthService, RefundService, $q,$state) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('退款管理');
            $scope.refundState = {
                'ALL':'全部',
                'APPLY': '买家申请退款',
                'REFUND': '退款成功',
                'REFUSED': '拒绝退款',
                'CLOSE': '买家已取消退款申请'
            };

            $scope.option = {
                'state':'ALL',
                'page': 1,
                'page_size':10
            };

            $scope.pages={
                'refundTotal':0
            }

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

            $scope.getRefundsData = function(option) {
                RefundService.getRefundsData(option).then(function(res) {
                    $scope.refundData=res.data;
                    $scope.pages.refundTotal=res.total;
                }, function(res) {
                    alert(res.message);
                });
            };

            $scope.$watchCollection('option.page', function(newVal, oldVal) {
                if(newVal==oldVal){
                    return false;
                }
                $scope.getRefundsData($scope.option);
            });

            $scope.$watch('$viewContentloaded',function(){
                $(".widget .widget-body.auto-nohead").css({
                        "height": $("html").height() - 95,
                        "overflow-y": "auto"
                });
            })

            $scope.search=function(){
                $scope.option.page=1;
                $scope.getRefundsData($scope.option);
            }

            $scope.init=function() {
                $scope.getRefundsData($scope.option);
            };
            $scope.init();     
        }
    ]);
