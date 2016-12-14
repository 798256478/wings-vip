/**
 * RedeemCodeStatistics Controller
 */
'use strict';

angular.module('app')
    .controller('RedeemCodeStatisticsController', ['$scope', '$location', 'AuthService', 'RedeemCodeStatisticsService', '$uibModal',
        function($scope, $location, AuthService, RedeemCodeStatisticsService, $uibModal) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('兑换码统计');

            var nowTime = new Date();
            var month = nowTime.getMonth() + 1
            $scope.now = nowTime.getFullYear().toString() + '-' + month;
            $scope.selData = {
                'date': $scope.now,
                'typeName': '按兑换码',
            };

            RedeemCodeStatisticsService.getHistoryList().then(function(res) {
                $scope.list = res;
            }, function(res) {
                alert(res.message);
            });

            $scope.getStatistics = function() {
                RedeemCodeStatisticsService.getHistory($scope.selData.date, $scope.selData.typeName).then(function(res) {
                    $scope.statistics = res;
                    $scope.createChart();
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.selDate = function(key) {
                $scope.selData.date = key;
                $scope.getStatistics();
            }

            $scope.selType = function(key) {
                $scope.selData.typeName = key;
                $scope.getStatistics();
            };
            $scope.selType($scope.selData.typeName);

            $scope.createChart = function() {
                $("#chart").kendoChart({
                    title: {
                        text: $scope.selDate.typeName
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
