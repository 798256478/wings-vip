/**
 * Customer Controller
 */
'use strict';

angular.module('app')
.controller('BackupController', ['$scope', '$location', 'AuthService', 'BackupService',
    function($scope, $location, AuthService, BackupService) {
        if(AuthService.current_user == null){
            $location.path("/login");
            return;
        }
        $(".header .meta .page").text('数据备份');
        $scope.aa = {
            'time': ''
        };

        $scope.getHistory = function(){
            BackupService.backupHistory().then(function(res){
                $scope.aa.time = res;
            },function(res){
                $scope.aa.time = '';
            });
        }
        $scope.getHistory();

        $scope.getBackup = function(){
            BackupService.getBackup().then(function(res){
                $scope.getHistory();
                alert(res.message);
                // var a = document.createElement("a");
                // a.download = res.filename;
                // a.href = (window.URL || window.webkitURL).createObjectURL(res.file);
                // a.click();
            },function(res){
                alert(res.message);
            });
        }
    }
]);
