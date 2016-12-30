/**
 * ReportDetail Controller
 */
'use strict';

angular.module('app')
.controller('ReportDetailController', ['$scope','$location','$stateParams','ReportService','$q',
    function($scope,$location,$stateParams,ReportService, q) {
        $scope.code=$stateParams.code;
        $scope.projectId=$stateParams.projectId;

        $scope.range={
          '一般风险':['success',3],
          '中度风险':['warning',2],
          '高度风险':['danger',1]
        };

        $scope.getReportDetail=function(){
            ReportService.getReportDetail($scope.code,$scope.projectId).then(function(res){
                  $scope.project=res.project;
                  $scope.projectsDetail=res.projectsDetail;
            }, function(res) {
                alert(res.message);
            });
        }

        $scope.open=function(id){
            $('#'+id).display('block');
        }

        $scope.getReportDetail();
    }
]);