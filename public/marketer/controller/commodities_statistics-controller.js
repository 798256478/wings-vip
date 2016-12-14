/**
 * property Controller
 */
'use strict';

angular.module('app')
    .controller('CommoditiesStaticticsController', ['$scope', '$location', 'AuthService', 'StatisticsService', '$uibModal',
        function($scope, $location, AuthService, StatisticsService, $uibModal) {
            $(".header .meta .page").text('商城');
            $scope.data=[];
            $scope.key = '';
            $scope.CostChart = {};
            var nowTime = new Date();

            $scope.now = {
                'year': nowTime.getFullYear().toString(),
                'month': nowTime.getMonth() >= 9 ? (nowTime.getMonth() + 1).toString() : '0' + (nowTime.getMonth() + 1),
            };
            $scope.selDate = {
                'year': '',
                'month': '',
            }
            $scope.dataState = false;
            $scope.dataText1 = '';
            $scope.dataText2 = '';

            $scope.saleSort='desc';
            $scope.earnSort='';

            StatisticsService.getDateList().then(function(res) {
                $scope.tmpDateList = res;
                $scope.selDate = $.extend(true, {}, $scope.now);
                $scope.dateList = $.extend(true, [], $scope.tmpDateList);
            }, function(res) {
                alert(res.message);
            })

            $scope.selYear = function(year) {
                $scope.selDate.year = year;
                $scope.selDate.month = 'all';
                if ($scope.selDate.year != '' && $scope.selDate.month != '') {
                    var value = $.extend(true, {}, $scope.selDate);
                    $scope.getCommodityStatistics(value);
                }
            }

            $scope.selMonth = function(month) {
                $scope.selDate.month = month;
                if ($scope.selDate.year != '' && $scope.selDate.month != '') {
                    var value = $.extend(true, {}, $scope.selDate);
                    $scope.getCommodityStatistics(value);
                }
            }

            $scope.getCommoditiesStatistics = function(value) {
                StatisticsService.getCommoditiesStatistics(value).then(function(res) {
                    $scope.dateList = $.extend(true, [], $scope.tmpDateList);
                    $scope.commoditiesStatistics=res.commodities;
                    $scope.saleSort='asc';
                    $scope.getSaleSort();
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.getSaleSort=function(){
                var temp = 0 ;
                var data=$scope.commoditiesStatistics;
                $scope.earnSort='';
                $scope.saleSort==''?$scope.saleSort='desc':($scope.saleSort=='desc'?$scope.saleSort='asc':$scope.saleSort='desc');
                var sort=$scope.saleSort; 
                sort=='asc'?sort='<':sort='>';            
                for (var i = 0 ; i < data.length - 1 ; i++)
                {
                    for (var j = i + 1 ; j < data.length ; j++)
                    {
                        if (eval((data[j].sum?data[j].sum:0)+sort+(data[i].sum?data[i].sum:0)))
                        {
                            temp = data[i] ;
                            data[i] = data[j] ;
                            data[j] = temp ;
                        }
                    }
                }  
            }

            $scope.getEarnSort=function(){
                var temp = 0 ;
                var data=$scope.commoditiesStatistics;
                $scope.saleSort='';
                $scope.earnSort==''?$scope.earnSort='desc':($scope.earnSort=='desc'?$scope.earnSort='asc':$scope.earnSort='desc');
                var sort=$scope.earnSort;
                sort=='asc'?sort='<':sort='>';
                for (var i = 0 ; i < data.length - 1 ; i++)
                {
                    for (var j = i + 1 ; j < data.length ; j++)
                    {
                        if (eval((data[j].total_price?data[j].total_price:0)+sort+(data[i].total_price?data[i].total_price:0)))
                        {
                            temp = data[i] ;
                            data[i] = data[j] ;
                            data[j] = temp ;
                        }
                    }
                }
            }

            $scope.setData=function(){
                $scope.selDate = $.extend(true, {}, $scope.now);
                if ($scope.selDate.year != '' && $scope.selDate.month != '') {
                    var value = $.extend(true, {}, $scope.selDate);
                    $scope.getCommoditiesStatistics(value);
                }
            }

            $scope.setData();
        }
    ]);
