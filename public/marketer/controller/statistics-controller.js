/**
 * property Controller
 */
'use strict';

angular.module('app')
    .controller('StatisticsController', ['$scope', '$location', 'AuthService', 'StatisticsService', '$uibModal',
        function($scope, $location, AuthService, StatisticsService, $uibModal) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('综合分析');
            $scope.statistics = {};
            $scope.typeList = {
                'CARD': '建卡及到店',
                'BALANCE': '储值及使用',
                'TICKET': '领券及用券',
                'PROPERTY': '服务及核销',
                'COST': '总消费及子项',
            };

            $scope.data=[];
            $scope.key = '';
            $scope.CostChart = {};
            $scope.costtype = '';
            var nowTime = new Date();

            $scope.now = {
                'year': nowTime.getFullYear().toString(),
                'month': nowTime.getMonth() >= 9 ? (nowTime.getMonth() + 1).toString() : '0' + (nowTime.getMonth() + 1),
            };
            $scope.selDate = $.extend(true, {}, $scope.now);
            $scope.dataState = false;
            $scope.dataText1 = '';
            $scope.dataText2 = '';

            StatisticsService.getDateList().then(function(res) {
                $scope.tmpDateList = res;
                $scope.dateList = $.extend(true, [], $scope.tmpDateList);
            }, function(res) {
                alert(res.message);
            });

            $scope.getDaysData=function(){
                StatisticsService.getDaysData().then(function(res) {
                    $scope.totalData=res;
                },function(res) {
                    alert(res.message);
                });
            }

            $scope.getDaysData();

            $scope.selYear = function(year) {
                $scope.selDate.year = year;
                $scope.selDate.month = 'all';
                if ($scope.selDate.year != '' && $scope.selDate.month != '') {
                    var value = $.extend(true, {}, $scope.selDate);
                    value.key = $scope.key;
                    $scope.getStatistics(value);
                }
            }

            $scope.selMonth = function(month) {
                $scope.selDate.month = month;
                if ($scope.selDate.year != '' && $scope.selDate.month != '') {
                    var value = $.extend(true, {}, $scope.selDate);
                    value.key = $scope.key;
                    $scope.getStatistics(value);
                }
            }

            $scope.getStatistics = function(value) {
                StatisticsService.getStatistics(value).then(function(res) {
                    $scope.dateList = $.extend(true, [], $scope.tmpDateList);
                    if(value.key != 'COST'){
                        $scope.statistics = res;
                        $scope.createChart();
                    }else {
                        $scope.CostChart = res;
                        $scope.getCostChat('costtype');
                    }
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.selType = function(key) {
                $scope.key = key;
                $scope.name = $scope.typeList[$scope.key];
                var value = $.extend(true, {}, $scope.selDate);
                value.key = $scope.key;
                $scope.getStatistics(value);
            };
            $scope.selType('CARD');

            $scope.getCostChat = function(type){
                $scope.statistics = $.extend(true, [], $scope.CostChart);
                if(type == 'costtype'){
                    $scope.costtype = 'costtype';
                    $scope.statistics.table = $.extend(true, [], $scope.CostChart.table.costtype);
                }else if(type == 'channel') {
                    $scope.costtype = 'channel';
                    $scope.statistics.table = $.extend(true, [], $scope.CostChart.table.channel);
                }
                $scope.createChart();
            }

            $scope.getTotalMoney = function (data, i){
                var total = 0;
                angular.forEach(data, function(value, key){
                    total += value.data[i];
                });
                return total;
            }

            $scope.createChart = function() {
                $scope.dataState = true;
                $scope.dataCount = [];
                for (var i = 0; i < $scope.statistics.catearr.length; i++) {
                    $scope.dataCount.push(i);
                }
                if ($scope.selDate.month == 'all') {
                    $scope.dataText1 = $scope.selDate.year + '年';
                    $scope.dataText2 = '';
                }else {
                    $scope.dataText1 = $scope.selDate.year + '年' + $scope.selDate.month + '月';
                    $scope.dataText2 = '日';
                }
                $("#chart").kendoChart({
                    title: {
                        text: $scope.name
                    },
                    legend: {
                        position: "bottom"
                    },
                    seriesDefaults: $scope.statistics.type,
                    series: $scope.statistics.table,
                    valueAxis: {
                        line: {
                            visible: false
                        }
                    },
                    categoryAxis: {
                        categories: $scope.statistics.catearr,
                        majorGridLines: {
                            visible: false
                        }
                    },
                    tooltip: {
                        visible: true,
                        format: "{0}%",
                        template: "#= series.name #: #= value #"
                    }
                });
            }
        }
    ]);
