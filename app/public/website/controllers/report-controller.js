/**
 * Report Controller
 */
'use strict';

angular.module('app')
.controller('ReportController', ['$scope','$location','ReportService','$q',"$cookieStore",
    function($scope,$location,ReportService, q,$cookieStore) {
        $scope.range={
          '一般风险':['success',3],
          '中度风险':['warning',2],
          '高度风险':['danger',1]
        };

        $scope.code=$cookieStore.get("code");

        $scope.first=true;

        $scope.getReportData=function(){
            $cookieStore.put("code",$scope.code);
            $scope.first=false;
            ReportService.getReportData($scope.code).then(function(res){
              if($scope.code){
                  $scope.reportData=res.reportData;
                  $scope.customer=res.customer;
                  $scope.progress=res.progress;
                  $scope.dataCode=res.code;
                }else{
                  alert("条码不能为空");
                }
            }, function(res) {
                alert(res.message);
            });
        }

        $scope.init=function(){
            if($cookieStore.get('code')){
              $scope.getReportData();
              $scope.first=false;
            }
        }

        $scope.init();
    }
]);