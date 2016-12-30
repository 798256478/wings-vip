/**
 * Login Controller
 */
'use strict';
angular.module('app')
    .controller('LoginRecordsController',  ['$scope', '$location', 'AuthService','UserService',
        function($scope, $location, AuthService,UserService) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('用户登录');
            $scope.sort={
                'roles':{
                    'sel':'ALL',
                    'list':{
                        'admin':'管理员',
                        'cashier':'收银',
                        'captain':'领班',
                        'marketer':'营销',
                        'ALL':'全部'
                    }
                }
            };
            $scope.pages = {
                'nowPage': 1
            };
            $scope.loginList={};
            $scope.search={};

            //得到所有记录总数和当前页列表
            $scope.getLoginRecords=function(option,page=1,search){
                UserService.getLoginRecords(option,page,search).then(function(res){
                    $scope.loginList=res.loginList;
                    $scope.pages.total=res.total;
                },function(res){
                    alert(res.message);
                });
            };

            $scope.$watch('pages.nowPage',function(newVal,oldVal){
                $scope.getLoginRecords($scope.sort.roles.sel,$scope.pages.nowPage,$scope.search.value);
            });

            $scope.$watchGroup(['sort.roles.sel','search.value'],function(newVal,oldVal){
                $scope.pages.nowPage=1;
                $scope.getLoginRecords($scope.sort.roles.sel,$scope.pages.nowPage,$scope.search.value);
            });

            //$scope.search=function(val){
            //    if(val){
            //        $scope.searchVal=val;
            //        $scope.getLoginRecords($scope.sort.roles.sel,$scope.pages.nowPage,$scope.searchVal);
            //    }else{
            //        $scope.searchVal=0;
            //        $scope.getLoginRecords($scope.sort.roles.sel,$scope.pages.nowPage,$scope.searchVal);
            //    }
            //}

        }
]);
