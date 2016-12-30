/**
 * Login Controller
 */
'use strict';
angular.module('app')
    .controller('OperatingRecordsController',  ['$scope', '$location', 'AuthService','OperatingRecordsService',
        function($scope, $location, AuthService,OperatingRecordsService) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('会员操作');
            $scope.pages={
                'nowPage':1
            };
            $scope.search={};
            //得到记录总数和当前页列表
            $scope.getOperatingRecord=function(page=1,search=''){
                OperatingRecordsService.getOperatingRecord(page,angular.toJson(search)).then(function(res){
                    $scope.operatingList=res.operatingList;
                    $scope.pages.total=res.total;
                },function(res){
                    alert(res.message);
                });
            };
            $scope.getOperatingRecord();

            $scope.$watch('pages.nowPage',function(newVal,oldVal){
                if(newVal==oldVal){
                    return;
                }
                $scope.getOperatingRecord($scope.pages.nowPage,$scope.search);
            });

            $scope.$watch('search.cardCode',function(newVal,oldVal){
                if(newVal==oldVal){
                    return;
                }
                $scope.pages.nowPage=1;
                $scope.getOperatingRecord($scope.pages.nowPage,$scope.search);
            });

            $scope.$watch('search.action',function(newVal,oldVal){
                if(newVal==oldVal){
                    return;
                }
                $scope.pages.nowPage=1;
                $scope.getOperatingRecord($scope.pages.nowPage,$scope.search);
            });

            //$scope.changeAction=function(action){
            //    if(action) {
            //        $scope.search.action = action;
            //    }else{
            //        $scope.search.action = 0;
            //    }
            //}
        }
    ])
;
