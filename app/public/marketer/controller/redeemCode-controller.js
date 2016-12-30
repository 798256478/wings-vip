/**
 * property Controller
 */
'use strict';

angular.module('app')
    .controller('RedeemCodeController', ['$scope', '$location', 'AuthService', 'RedeemCodeService', '$uibModal',
        function($scope, $location, AuthService, RedeemCodeService, $uibModal) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('兑换码');
            $scope.redeemCode = null;
            $scope.form = {};
            $scope.rule = {
                'jobs' : []
            };
            // 日期选择控件配置
            $scope.datePicker = {
                format: 'yyyy-MM-dd',
                dateOptions: {
                    formatYear: 'yyyy',
                    startingDay: 1
                }
            }
            $scope.beginDate = {
                opened: false
            };
            $scope.endDate = {
                opened: false
            };
            $scope.selectBeginDate = function() {
                $scope.beginDate.opened = true;
            };
            $scope.selectEndDate = function() {
                $scope.endDate.opened = true;
            };
            $scope.text = "新增兑换码";

            $scope.getRedeemCodeList = function() {
                RedeemCodeService.getRedeemCodeList().then(function(res) {
                    $scope.redeemCodeList = res.redeem_codes;
                }, function(res) {
                    alert(res.message);
                });
            }
            $scope.getRedeemCodeList();

            $scope.addRedeemCode = function(){
                $scope.clearData();
                $scope.redeemCode = {
                    'is_many': false,
                    'jobs': [],
                };
            }

            $scope.getRedeemCode = function(id) {
                RedeemCodeService.getRedeemCode(id).then(function(res) {
                    $scope.redeemCode = res.redeem_code;
                    $scope.rule.jobs = $.extend(true, [], $scope.redeemCode.jobs);
                    if (!$scope.redeemCode.is_many) {
                        $scope.redeemCode.begin_timestamp = new Date($scope.redeemCode.begin_timestamp);
                        $scope.redeemCode.end_timestamp = new Date($scope.redeemCode.end_timestamp);
                    }
                    $scope.text = "修改：" + $scope.redeemCode.title;
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.changeState = function(state){
                $scope.redeemCode.is_many = state;
            }

            $scope.getDate = function(unixTime){
                var time = new Date(unixTime * 1000 + 8 * 3600 * 1000);
                var ymdhis = "";
                ymdhis += time.getUTCFullYear() + "-";
                ymdhis += (time.getUTCMonth()+1) + "-";
                ymdhis += time.getUTCDate();
                ymdhis += " " + time.getUTCHours() + ":";
                ymdhis += time.getUTCMinutes() + ":";
                ymdhis += time.getUTCSeconds();
                return ymdhis;
            }

            $scope.submit = function(isValid) {
                if (isValid) {
                    $scope.redeemCode.jobs = $scope.rule.jobs;
                    if ($scope.redeemCode._id) {
                        RedeemCodeService.updateRedeemCode($scope.redeemCode).then(function(res) {
                            alert("修改成功");
                            $scope.clearData();
                        }, function(res) {
                            alert(res.message);
                            $scope.error = res.errors;
                        });
                    } else {
                        RedeemCodeService.addRedeemCode($scope.redeemCode).then(function(res) {
                            alert("添加成功");
                            $scope.clearData();
                        }, function(res) {
                            alert(res.message);
                            $scope.error = res.errors;
                        });
                    }
                }
            }

            $scope.getCodes = function(count) {
                if($scope.redeemCode._id){
                    RedeemCodeService.generationCode($scope.redeemCode._id, count).then(function(res) {
                        $scope.getRedeemCode($scope.redeemCode._id);
                        alert("添加成功");
                        return undefined;
                    }, function(res) {
                        alert(res.message);
                        $scope.error = res.errors;
                    });
                }
            }

            $scope.delete = function(id) {
                var del = confirm("是否要删除该兑换码？有可能造成系统异常");
                if (del) {
                    RedeemCodeService.deleteRedeemCode(id).then(function(res) {
                        $scope.clearData();
                    }, function(res) {
                        alert(res.message);
                    });
                }
            }

            $scope.clearData = function() {
                $scope.redeemCode = null;
                $scope.rule.jobs = []
                $scope.getRedeemCodeList();
                if($scope.form.redeemCodeForm){
                    $scope.form.redeemCodeForm.$setPristine();
                }
                $scope.text = "新增兑换码";
            }
        }
    ]);
