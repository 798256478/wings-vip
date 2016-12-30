/**
 * Sync Controller
 */
'use strict';
angular.module('app')
    .controller('SyncController',  ['$scope', '$location', 'AuthService','SyncService','$uibModal',
        function($scope, $location, AuthService,SyncService,$uibModal) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('订单同步');
            $scope.pages = {
                'nowPage':1
            };
            $scope.screen = {
                'isRepair':false
            };
            $scope.search = {};

            //得到记录总数和当前页列表
            $scope.getSyncFailRecord=function(page=1,isRepair=false){
                SyncService.getSyncFailRecord(page,isRepair).then(function(res){
                    $scope.syncFailList=res.syncFailList;
                    $scope.pages.total=res.total;
                },function(res){
                    alert(res.message);
                });
            };
            $scope.getSyncFailRecord(1,false);

            //再次同步
            $scope.againSync = function(id){
                $scope.items=[{
                    'title':'确定重新发送?',
                    'type':'affirm',
                    'a':'返回',
                    'b':'确定'
                }];
                var modalInstance = $uibModal.open({
                    animation: false,
                    templateUrl: 'affirm.html',
                    controller: 'ModalInstanceCtrl',
                    size: 'sm',
                    'keyboard':false,
                    resolve: {
                        items: function () {
                            return $scope.items;
                        }
                    }

                });
                modalInstance.result.then(function(setItems) {
                    SyncService.againSync(id).then(function (res) {
                        if(res == 'FAIL'){
                            alert('同步失败');
                        }else{
                            $scope.getSyncFailRecord($scope.pages.nowPage, $scope.screen.isRepair);
                            alert('同步成功');
                        }
                    }, function (res) {
                        alert(res.message);
                    });
                },function(){

                });
            };

            //修改为同步成功
            $scope.syncSuccess = function(id){
                $scope.items=[{
                    'title':'该订单确定已同步?',
                    'type':'affirm',
                    'a':'返回',
                    'b':'确定'
                    }];
                var modalInstance = $uibModal.open({
                    animation: false,
                    templateUrl: 'affirm.html',
                    controller: 'ModalInstanceCtrl',
                    size: 'sm',
                    resolve: {
                        items: function () {
                            return $scope.items;
                        }
                    }

                });
                modalInstance.result.then(function(setItems) {
                    SyncService.syncSuccess(id).then(function(res){
                        $scope.getSyncFailRecord($scope.pages.nowPage,$scope.screen.isRepair);
                    },function(res){
                        alert(res.message);
                    });
                },function(){

                });

            };

            $scope.$watch('pages.nowPage',function(newVal,oldVal){
                if(newVal==oldVal){
                    return;
                }
                $scope.getSyncFailRecord($scope.pages.nowPage,$scope.screen.isRepair);
            });

            $scope.$watch('screen.isRepair',function(newVal,oldVal){
                if(newVal==oldVal){
                    return;
                }
                $scope.pages.nowPage=1;
                $scope.getSyncFailRecord($scope.pages.nowPage,$scope.screen.isRepair);
            });
            //
            //$scope.$watch('search.action',function(newVal,oldVal){
            //    if(newVal==oldVal){
            //        return;
            //    }
            //    $scope.pages.nowPage=1;
            //    $scope.getOperatingRecord($scope.pages.nowPage,$scope.search);
            //});

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
