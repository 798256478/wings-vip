/**
 * Master Controller
 */
'use strict';

angular.module('app')
.controller('MasterController', ['$scope', '$cookieStore', '$location', 'AuthService', 'SettingService',
function($scope, $cookieStore, $location, AuthService, SettingService) {
    /**
     * Sidebar Toggle & Cookie Control
     */
    var mobileView = 1370;

    $scope.getWidth = function() {
        return window.innerWidth;
    };

    $scope.$watch($scope.getWidth, function(newValue, oldValue) {
        if (newValue >= mobileView) {
            if (angular.isDefined($cookieStore.get('toggle'))) {
                $scope.toggle = ! $cookieStore.get('toggle') ? false : true;
            } else {
                $scope.toggle = true;
            }
        } else {
            $scope.toggle = false;
        }

    });

    $scope.toggleSidebar = function() {
        $scope.toggle = !$scope.toggle;
        $cookieStore.put('toggle', $scope.toggle);
    };

    window.onresize = function() {
        $scope.$apply();
    };

    /*********************************************************************/
    $scope.AuthService = AuthService;

    $scope.logout = function(){
        if(AuthService.current_user != null){
            AuthService.logout(AuthService.current_user.id);
        }
        $location.path("/login");
    }

    SettingService.getModule().then(function(res) {
        $scope.moduleList = res;
    }, function(res) {
    });
}]);
