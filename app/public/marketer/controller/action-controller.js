/**
 * action Controller
 */
'use strict';

angular.module('app')
.controller('ActionController', ['$scope', 'EventService', 'TicketTemplateService', 'PropertyTemplateService','SettingService','$uibModal',
    function($scope, EventService, TicketTemplateService, PropertyTemplateService,SettingService, $uibModal) {
        $scope.actionTmpJob = {};
        $scope.recipient = {
            'SELF': '本人',
            'REFERRER': '推荐人',
            'ROOT_REFERRER': '二级推荐人'
        };
        SettingService.getSetting('CARD').then(function(res) {
            $scope.levels = res.levels
        }, function(res) {
            alert(res.message);
        });
        EventService.getJobList().then(function(res) {
            if($scope.massOpen){
                _.unset(res, 'App\\Jobs\\PayCommission');
            }
            $scope.actionJobList = res;
        }, function(res) {
            alert(res.message);
        });
        PropertyTemplateService.getPropertyTemplateList().then(function(res) {
            $scope.actionPropertyTemplateList = res.property_templates;
        }, function(res) {
            alert(res.message);
        });
        TicketTemplateService.getUsableTicketTemplateList().then(function(res) {
            $scope.actionTicketList = res.ticket_templates;
        }, function(res) {
            alert(res.message);
        });
        $scope.getTicketName = function(id){
            var index = _.findIndex($scope.actionTicketList, function(n){
                return n.id == id;
            });
            if(index > -1){
                return $scope.actionTicketList[index].title;
            }else {
                return '';
            }
        }
        $scope.getPropertyName = function(id){
            var index = _.findIndex($scope.actionPropertyTemplateList, function(n){
                return n.id == id;
            });
            if(index > -1){
                return $scope.actionPropertyTemplateList[index].title;
            }else {
                return '';
            }
        }
        $scope.getLevelName = function(id){
            var index = _.findIndex($scope.levels, function(n){
                return n.id == id;
            });
            if(index > -1){
                return $scope.levels[index].name;
            }else {
                return '';
            }
        }
        $scope.actionSaveJob = function(){
            if(!$scope.actionTmpJob.recipient){
                alert('对象不能为空');
                return;
            }
            var tmpJob = {
                'class': $scope.actionTmpJob.class,
                'recipient': $scope.actionTmpJob.recipient,
            };
            if(typeof($scope.actionTmpJob.term_type) == 'object'){
                tmpJob.args = {};
                $.each($scope.actionTmpJob.term_type, function(key, value){
                    if (value.value != undefined && value.value != null && value.value != '') {
                        tmpJob.args[key] = value.value;
                    }else {
                        alert('格式不正确！');
                        return false;
                    }
                });
            }else {
                if (value.value != undefined && value.value != null && value.value != '') {
                    tmpJob.args = $scope.actionTmpJob.value;
                }else {
                    alert('格式不正确！');
                    return false;
                }
            }
            if(typeof(tmpJob.args) != 'object' && typeof(tmpJob.args) != 'number'){
                alert('动作参数不能为空');
                return;
            }
            if($scope.actionTmpJob.$$hashKey){
                var index = _.findIndex($scope.rule.jobs, function(n){
                    return n.$$hashKey == $scope.actionTmpJob.$$hashKey;
                });
                if(index > -1){
                    $scope.rule.jobs[index].args = tmpJob.args;
                    $scope.rule.jobs[index].recipient = tmpJob.recipient;
                }
            }else {
                $scope.rule.jobs.push(tmpJob);
            }
            $scope.actionTmpJob = {};
        }
        $scope.actionDeleteJob = function(job){
            _.remove($scope.rule.jobs, function(o) {
                return o == job;
            });
        }
        $scope.getType = function(args){
            return typeof(args);
        }
        $scope.alertAdd = function(job){
            var tmpJob = {};
            if(job.recipient){
                tmpJob = $.extend(true, [], $scope.actionJobList[job.class]);
            }else {
                tmpJob = $.extend(true, [], job);
            }
            var alertItems = [
                {
                    'title': tmpJob.label + '给',
                    'name': 'recipient',
                    'type': 'recipient',
                    'recipient': $scope.recipient,
                    'recipient_options': tmpJob.recipient_options,
                    'value': job.recipient ? job.recipient : '',
                    'hashKey': job.recipient ? job.$$hashKey : '',
                    'class': tmpJob.class,
                },
            ];
            switch (tmpJob.class) {
                case "App\\Jobs\\GiveBalance":
                    alertItems.push({
                        'title': '余额',
                        'name': 'bonus',
                        'type': 'number',
                        'value': job.args ? job.args : '',
                    });
                    break;
                case "App\\Jobs\\GiveBonus":
                    alertItems.push({
                        'title': '积分',
                        'name': 'balance',
                        'type': 'number',
                        'value': job.args ? job.args : '',
                    });
                    break;
                case "App\\Jobs\\GiveProperty":
                    alertItems.push({
                        'title': tmpJob.term_type.propertyTemplateId.label,
                        'name': 'propertyTemplateId',
                        'type': 'property',
                        'options': $scope.actionPropertyTemplateList,
                        'value': job.args ? job.args.propertyTemplateId : '',
                    });
                    alertItems.push({
                        'title': tmpJob.term_type.count.label,
                        'name': 'count',
                        'type': 'number',
                        'value': job.args ? job.args.count : '',
                    });
                    alertItems.push({
                        'title': tmpJob.term_type.validity_days.label,
                        'name': 'validity_days',
                        'type': 'number',
                        'value': job.args ? job.args.validity_days : '',
                    });
                    break;
                case "App\\Jobs\\GiveTicket":
                    alertItems.push({
                        'title': tmpJob.term_type.ticketTemplateId.label,
                        'name': 'ticketTemplateId',
                        'type': 'property',
                        'options': $scope.actionTicketList,
                        'value': job.args ? job.args.ticketTemplateId : '',
                    });
                    alertItems.push({
                        'title': tmpJob.term_type.count.label,
                        'name': 'count',
                        'type': 'number',
                        'value': job.args ? job.args.count : '',
                    });
                    break;
                case "App\\Jobs\\LevelUp":
                    alertItems.push({
                        'title': '等级',
                        'name': 'level',
                        'type': 'level',
                        'options': $scope.levels,
                        'value': job.args ? job.args : '',
                    });
                    break;
                case "App\\Jobs\\PayCommission":
                    alertItems.push({
                        'title': '返佣',
                        'name': 'commission',
                        'type': 'commission',
                        'commission': job.args > 1 ? 2 : 1,
                        'value': job.args ? job.args : '',
                    });
                    break;
                default:
            }
            var modalInstance = $uibModal.open({
                animation: false,
                templateUrl: 'modal.html',
                controller: 'ModalInstanceCtrl',
                size: 'md',
                resolve: {
                    items: function () {
                        return alertItems;
                    }
                }
            });
            modalInstance.result.then(function (setItems) {
                var tmp = {
                    'class': setItems[0].class,
                    'recipient': setItems[0].value,
                };
                var hashKey = setItems[0].hashKey ? setItems[0].hashKey : '';
                var flag = true;
                switch (tmp.class) {
                    case "App\\Jobs\\GiveBalance":
                        if (setItems[1].value > 0) {
                            tmp.args = setItems[1].value;
                        }else {
                            flag = false;
                        }
                        break;
                    case "App\\Jobs\\GiveBonus":
                        if (setItems[1].value > 0) {
                            tmp.args = setItems[1].value;
                        }else {
                            flag = false;
                        }
                        break;
                    case "App\\Jobs\\GiveProperty":
                        var property = {};
                        $.each(setItems, function(key, value){
                            if(value.type != 'recipient'){
                                if (value.value != undefined && value.value != null && value.value != '') {
                                    property[value.name] = value.value;
                                }
                            }
                        });
                        if (property != {}) {
                            tmp.args = property;
                        }else {
                            flag = false;
                        }
                        break;
                    case "App\\Jobs\\GiveTicket":
                        var ticket = {};
                        $.each(setItems, function(key, value){
                            if(value.type != 'recipient'){
                                if (value.value != undefined && value.value != null && value.value != '') {
                                    ticket[value.name] = value.value;
                                }
                            }
                        });
                        if (ticket != {}) {
                            tmp.args = ticket;
                        }else {
                            flag = false;
                        }
                        break;
                    case "App\\Jobs\\LevelUp":
                        if (setItems[1].value > 0) {
                            tmp.args = setItems[1].value;
                        }else {
                            flag = false;
                        }
                        break;
                    case "App\\Jobs\\PayCommission":
                        if (setItems[1].value != 0) {
                            if (setItems[1].commission == 2) {
                                tmp.args = setItems[1].value;
                            } else if (setItems[1].commission == 1) {
                                tmp.args = setItems[1].value / 100;
                            }
                        }else {
                            flag = false;
                        }
                        break;
                    default:
                }
                if(tmp.recipient == '' || tmp.recipient == null || tmp.recipient == undefined){
                    flag = false;
                }
                if(typeof(tmp.args) == 'object'){
                    $.each(tmp.args, function(key, value){
                        if(value == 0 || value == null || value == undefined){
                            flag = false;
                        }
                    });
                }else {
                    if(tmp.args == 0 || tmp.args == null || tmp.args == undefined){
                        flag = false;
                    }
                }
                if (flag) {
                    if(hashKey != ''){
                        var index = _.findIndex($scope.rule.jobs, function(n){
                            return n.$$hashKey == hashKey;
                        });
                        if(index > -1){
                            $scope.rule.jobs[index] = tmp
                        }
                    }else {
                        $scope.rule.jobs.push(tmp);
                    }
                }else {
                    alert('格式错误');
                }
            }, function () {
            });
        }
    }
]);
