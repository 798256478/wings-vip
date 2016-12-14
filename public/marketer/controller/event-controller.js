/**
 * product Controller
 */
'use strict';

angular.module('app')
    .controller('EventController', ['$scope','$stateParams','$location', 'AuthService', 'EventService', 'TicketTemplateService',
        function($scope,$stateParams ,$location, AuthService, EventService, TicketTemplateService) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }

            $(".header .meta .page").text('事件');
            $scope.text = "";

            var rule=$stateParams.rule;

            $scope.getEventList = function() {
                EventService.getEventList().then(function(res) {
                    $scope.eventList = res;
                    if(rule){
                        $scope.editRules(rule.event_class,rule);
                    }
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.getEventRuleList = function() {
                EventService.getEventRuleList().then(function(res) {
                    $scope.eventruleList = res.event_rules;
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.clearData = function() {
                $scope.rule = {};
                $scope.text = "事件详情";
                $scope.conditions = {};
                $scope.getEventList();
                $scope.getEventRuleList();
            }
            $scope.clearData();

            $scope.addRule = function(selClass) {
                $scope.rule = {
                    'title': '',
                    'event_class': selClass,
                    'jobs': [],
                    'conditions': {},
                };
                var index = _.findIndex($scope.eventList, function(n) {
                    return n.class == selClass;
                });
                $scope.text = "新增" + $scope.eventList[index].label + "事件";
                $scope.conditions = $.extend(true, [], $scope.eventList[index].conditions);
            }

            $scope.delRule = function(rule) {
                $scope.rule = {};
                $scope.conditions = {};
                var del = confirm("是否要删除该事件？");
                if (del) {
                    EventService.delEventRules(rule).then(function(res) {
                        $scope.clearData();
                    }, function(res) {
                        alert(res.message);
                    });
                }
            }

            $scope.submit = function() {
                $scope.rule.conditions = $scope.getConditions();
                if (!$scope.rule.title) {
                    alert('名称不能为空！');
                    return;
                }
                if ($scope.rule.jobs.length <= 0) {
                    alert('动作不能为空！');
                    return;
                }
                if ($scope.rule._id) {
                    EventService.updateEventRules($scope.rule).then(function(res) {
                        $scope.clearData();
                    }, function(res) {
                        alert(res.message);
                    });
                } else {
                    EventService.addEventRules($scope.rule).then(function(res) {
                        $scope.clearData();
                    }, function(res) {
                        alert(res.message);
                    });
                }
            }

            $scope.editRules = function(eventClass, eventRule) {
                var index = _.findIndex($scope.eventList, function(n) {
                    return n.class == eventClass;
                });
                $scope.text = "修改" + $scope.eventList[index].label + "事件：" + eventRule.title;
                $scope.setConditions($scope.eventList[index].conditions, eventRule);
                $scope.rule = eventRule;
            }

            $scope.getConditions = function() {
                var conditionsList = {};
                $($scope.conditions).each(function(e) {
                    if ($scope.conditions[e].state) {
                        if ($scope.conditions[e].key == null) {
                            conditionsList = {};
                            return conditionsList;
                        } else {
                            if ($scope.conditions[e].term_type == null) {
                                conditionsList[$scope.conditions[e].key] = null;
                            } else if ($scope.conditions[e].term_type == 'int') {
                                conditionsList[$scope.conditions[e].key] = $scope.conditions[e][$scope.conditions[e].key];
                            } else if ($scope.conditions[e].term_type.min) {
                                conditionsList[$scope.conditions[e].key] = {
                                    'min': $scope.conditions[e][$scope.conditions[e].key].min,
                                    'max': $scope.conditions[e][$scope.conditions[e].key].max,
                                };
                            } else {
                                conditionsList = {};
                                return conditionsList;
                            }
                        }
                    }
                });
                return conditionsList;
            }

            $scope.setConditions = function(conditions, eventRule) {
                $scope.conditions = {}
                $scope.conditions = $.extend(true, [], conditions);
                if (eventRule.conditions != undefined && !angular.isArray(eventRule.conditions)) {
                    $.each(eventRule.conditions, function(key, value) {
                        var index = _.findIndex($scope.conditions, function(n){
                            return n.key == key;
                        });
                        $scope.conditions[index][key] = value;
                        $scope.conditions[index].state = true;
                    });
                } else {
                    var index = _.findIndex($scope.conditions, function(n){
                        return n.key == null;
                    });
                    $scope.conditions[index].state = true;
                }
            }
        }
    ]);
