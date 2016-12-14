(function () {
    'use strict';
    
    angular.module('app')
    .controller('WriteOffController', ['$http', 'urls','$scope','AuthService',
    function($http,urls, $scope, AuthService,SettingService){
        
        $scope.do_write_off=function(type, objId) {
            $http.post(urls.BASE_API+'/write_off',{type:type,objid:objId}).success(function(res){
                $scope.refreshCard();
            }).error(function(res){
                $scope.errorInfo.message=res.message;
            });
            
        };
        
        $scope.write_off=function (type, objId,title) {
            swal({
                    title: "确认核销"+title+"吗？",
                    type: "warning",
                    showCancelButton: true,
                    closeOnConfirm: true 
                },
                function()
                {
                    $scope.do_write_off(type, objId);
                });
        }
    }])
    
})();