/**
 * TicketTemplate Controller
 */
'use strict';

angular.module('app')
    .controller('TicketTemplateController', ['$scope', '$location', 'AuthService', 'TicketTemplateService', 'SettingService',
        function($scope, $location, AuthService, TicketTemplateService, SettingService) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('券管理');
            $scope.nowType = '';
            $scope.formState = '新增券';
            $scope.form = {};

            // 日期选择控件配置
            $scope.datePicker = {
                format: 'yyyy-MM-dd',
                minDate: new Date(),
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

            $scope.getTicketTemplateList = function() {
                TicketTemplateService.getTicketTemplateList().then(function(res) {
                    $scope.TicketTemplateList = res.ticket_templates;
                }, function(res) {
                    $scope.error = res.message;
                });
            }

            SettingService.getSetting('TICKET').then(function(res) {
                $scope.setting = {
                    'key': 'TICKET',
                    'value': res,
                };
                $scope.getTicketTemplateList();
            }, function(res) {
                $scope.error = res.message;
            });

            TicketTemplateService.getTicketTemplateTypeList().then(function(res) {
                $scope.TicketTemplateTypeList = res;
                $scope.TicketTemplateTypeList.All = ' 全部';
                var tmp = {};
                $scope.TicketTemplateTypeList =
                    Object.keys($scope.TicketTemplateTypeList).sort().forEach(function(k) {
                        tmp[k] = $scope.TicketTemplateTypeList[k]
                    });
                $scope.TicketTemplateTypeList = tmp;
            }, function(res) {
                $scope.error = res.message;
            });

            $scope.getTicketTemplate = function(id) {
                $scope.settype = '';
                $scope.form.couponForm.$setPristine();
                TicketTemplateService.getTicketTemplate(id).then(function(res) {
                    $scope.coupon = res.ticket_template;
                    $scope.coupon.begin_timestamp = new Date($scope.coupon.begin_timestamp);
                    $scope.coupon.end_timestamp = new Date($scope.coupon.end_timestamp);
                    $scope.nowType = $scope.TicketTemplateTypeList[$scope.coupon.card_type] + "：" + $scope.coupon.title;
                    $scope.formState = '修改';
                }, function(res) {
                    $scope.error = res.message;
                });
            }

            $scope.createNew = function(ticketType) {
                $scope.settype = ''; //如果有值则是修改基础设置
                $scope.form.couponForm.$setPristine();
                $scope.coupon = {
                    'card_type': ticketType,
                    'disable_online': false,
                    'disable_shop': false,
                };
                $scope.formState = "新增";
                $scope.nowType = $scope.TicketTemplateTypeList[ticketType];
                $scope.coupon.sub_title = $scope.setting.value.sub_title;
                $scope.coupon.notice = $scope.setting.value.notice;
                $scope.coupon.description = $scope.setting.value.description;
            }

            $scope.submit = function(isValid) {
                if (isValid) {
                    if ($scope.coupon.begin_timestamp > $scope.coupon.end_timestamp) {
                        alert("结束时间必须晚于开始时间");
                        return;
                    }
                    if ($scope.coupon.id) {
                        TicketTemplateService.updateTicketTemplate($scope.coupon).then(function(res) {
                            alert("修改成功！");
                            $scope.getTicketTemplateList();
                            $scope.getTicketTemplate(res);
                            $scope.form.couponForm.$setPristine();
                        }, function(res) {
                            alert(res.message);
                            $scope.error = res.errors;
                        });
                    } else {
                        TicketTemplateService.addTicketTemplate($scope.coupon).then(function(res) {
                            $scope.getTicketTemplateList();
                            $scope.getTicketTemplate(res);
                            $scope.form.couponForm.$setPristine();
                            alert("添加成功！");
                        }, function(res) {
                            alert(res.message);
                            $scope.error = res.errors;
                        });
                    }
                }
            }

            $scope.delete = function(id) {
                var del = confirm("是否要删除该券？");
                if (del) {
                    TicketTemplateService.deleteTicketTemplate(id).then(function(res) {
                        $scope.clearData();
                    }, function(res) {
                        alert(res.message);
                    });
                }
            }

            $scope.changeState= function(status){
                return !status;
            }

            $scope.clearData = function() {
                $scope.formState = '新增券';
                $scope.nowType = '';
                $scope.coupon = {
                    'disable_online': false,
                    'disable_shop': false,
                };
                $scope.getTicketTemplateList();
            }
            $scope.clearData();

            $scope.basic_set = function(data) {
                $scope.settype = data;
                $scope.clearData();
            }
        }
    ]);
