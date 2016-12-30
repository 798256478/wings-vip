/**
 * property Controller
 */
'use strict';

angular.module('app')
    .controller('CommodityStatisticDetailsController', ['$scope', '$stateParams','$location', 'AuthService', 'StatisticsService', '$uibModal','CommodityService',
        function($scope,$stateParams, $location, AuthService, StatisticsService, $uibModal,CommodityService) {
            $(".header .meta .page").text('销售详情');
            $scope.data=[];
            $scope.key = '';
            $scope.CostChart = {};
            $scope.saleSort='';
            $scope.earnSort='';
            $scope.dateSort='asc';
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
            $scope.commodity='';

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
                    $scope.getCommodityStatisticsData(value);
                }
            }

            $scope.selMonth = function(month) {
                $scope.selDate.month = month;
                if ($scope.selDate.year != '' && $scope.selDate.month != '') {
                    var value = $.extend(true, {}, $scope.selDate);
                    $scope.getCommodityStatisticsData(value);
                }
            }

            $scope.getCommodityStatisticsData = function(value) {
                $scope.saleSort='';
                $scope.earnSort='';
                $scope.dateSort='asc';
                StatisticsService.getCommodityStatisticsData(value,$stateParams.id).then(function(res) {
                    $scope.dateList = $.extend(true, [], $scope.tmpDateList);
                    $scope.commodityStatisticsData=res.result;
                    $scope.catearr=res.catearr;
                    $scope.table=res.table;
                    $scope.commodity=res.commodity;
                    $scope.createChart();
                    
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.getSaleSort=function(){
                var temp = 0 ;
                var data=$scope.commodityStatisticsData;
                $scope.earnSort='';
                $scope.dateSort='';
                $scope.saleSort==''?$scope.saleSort='desc':($scope.saleSort=='desc'?$scope.saleSort='asc':$scope.saleSort='desc');
                var sort=$scope.saleSort; 
                sort=='asc'?sort='<':sort='>';            
                for (var i = 0 ; i < data.length - 1 ; i++)
                {
                    for (var j = i + 1 ; j < data.length ; j++)
                    {
                        if (eval((data[j][0]?data[j][0].sum:0)+sort+(data[i][0]?data[i][0].sum:0)))
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
                var data=$scope.commodityStatisticsData;
                $scope.saleSort='';
                $scope.dateSort='';
                $scope.earnSort==''?$scope.earnSort='desc':($scope.earnSort=='desc'?$scope.earnSort='asc':$scope.earnSort='desc');
                var sort=$scope.earnSort;
                sort=='asc'?sort='<':sort='>';
                for (var i = 0 ; i < data.length - 1 ; i++)
                {
                    for (var j = i + 1 ; j < data.length ; j++)
                    {
                        if (eval((data[j][0]?data[j][0].total_price:0)+sort+(data[i][0]?data[i][0].total_price:0)))
                        {
                            temp = data[i] ;
                            data[i] = data[j] ;
                            data[j] = temp ;
                        }
                    }
                }
            }

            $scope.getDateSort=function(){
                var temp = 0 ;
                var data=$scope.commodityStatisticsData;
                $scope.earnSort='';
                $scope.saleSort='';
                $scope.dateSort==''?$scope.dateSort='asc':($scope.dateSort=='asc'?$scope.dateSort='desc':$scope.dateSort='asc');
                var sort=$scope.dateSort;
                sort=='asc'?sort='<':sort='>';
                for (var i = 0 ; i < data.length - 1 ; i++)
                {
                    for (var j = i + 1 ; j < data.length ; j++)
                    {
                        if (eval(parseInt(data[j].catearr.substr(-3,2))+sort+parseInt(data[i].catearr.substr(-3,2))))
                        {
                            temp = data[i] ;
                            data[i] = data[j] ;
                            data[j] = temp ;
                        }
                    }
                }
            }

            $scope.createChart = function() {
                $scope.dataState = true;
                $scope.dataCount = [];
                for (var i = 0; i < $scope.catearr.length; i++) {
                    $scope.dataCount.push(i);
                }
                $("#chart").kendoChart({
                    title: {
                        text: $scope.commodity.name
                    },
                    legend: {
                        position: "bottom"
                    },
                    seriesDefaults:{
                        'type':'line',
                        'stack':true
                    },
                    series: $scope.table,
                    valueAxis: {
                        line: {
                            visible: false
                        }
                    },
                    categoryAxis: {
                        categories: $scope.catearr,
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

            $scope.backList=function(){
                history.back();
            }

            $scope.setData=function(){
                $scope.selDate = $.extend(true, {}, $scope.now);
                if ($scope.selDate.year != '' && $scope.selDate.month != '') {
                    var value = $.extend(true, {}, $scope.selDate);
                    $scope.getCommodityStatisticsData(value);
                }
            }
            $scope.setData();
        }
    ]);
