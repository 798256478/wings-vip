(function () {
    'use strict';
    
    angular.module('app')
    .controller('AuthController', ['$http','$scope', 'urls','AuthService','DesktopManager',
    function($http, $scope,urls, AuthService, DesktopManager){
        
        $scope.AuthService = AuthService;
        $scope.DesktopManager = DesktopManager;
        $scope.statistical_data=''; 
        $scope.login_step = 'LOGIN';
        var timer;
        $scope.login_fields = {
            login_name : '',
            password : '',
            pin : '',
            pin1 : '',
            pin2 : ''
        };
        
        $scope.password_status=0;
        
        $scope.edit_passsword_fields={
            oldpassword:'',
            newpassword:'',
            againpassword:''
        }
        
        $scope.startTimer=function()
        {
            clearTimeout(timer);
            timer=setTimeout(function(){
                $scope.lockup();
                $scope.$apply();
            },3*60*1000);
        }
        
        function successAuth(result){
            $scope.login_fields.login_name = '';
            $scope.login_fields.password = '';
            $scope.login_step = 'PIN1';
            document.onmousemove=document.onmousedown=document.onkeyup=document.onkeydown=$scope.startTimer;
        }
        
        function trySetPin(){
            //TODO:错误提醒
            if ($scope.login_fields.pin1 == $scope.login_fields.pin2){
                if (AuthService.setPin($scope.login_fields.pin1)){
                    DesktopManager.unlockDesktop();
                    $scope.login_fields.pin1 = $scope.login_fields.pin2 = '';
                    $scope.login_step = 'OK';
                    return;
                }
                else
                {
                    $scope.errorInfo.message ='不能设置该pin码，请更换';
                    $scope.login_fields.pin1 = $scope.login_fields.pin2 = '';
                    $scope.login_step = 'PIN1';
                }
            }
            else
            {
                $scope.errorInfo.message ='两次pin码不同，请重新设置';
                $scope.login_fields.pin1 = $scope.login_fields.pin2 = '';
                $scope.login_step = 'PIN1';
            }
        }
        
        $scope.setFocus = function(select){
            angular.element(select).focus();
        }
        
        $scope.login = function () {
            var formData = {
                login_name: $scope.login_fields.login_name,
                password: $scope.login_fields.password
            };
            AuthService.login(formData).then(successAuth,function(result){
                $scope.errorInfo.message =result.message;
            });
        };
        
        $scope.update_password_status=function () {
             $scope.password_status=!$scope.password_status;
        }
        
        $scope.editPassword=function () {
            if($scope.edit_passsword_fields.newpassword!=$scope.edit_passsword_fields.againpassword){
                 $scope.errorInfo.message ='两次输入的密码不一致';
                 return;
            }
            else{
                var formData = {
                    password: $scope.edit_passsword_fields.oldpassword,
                    newpassword: $scope.edit_passsword_fields.newpassword,
                    login_name: $scope.AuthService.current_user.login_name
                };
                AuthService.editPassword(formData).then(function(result) {
                    update_password_status();
                    successAuth(result);
                },function(result){
                    $scope.errorInfo.message =result.message;
                });
            }
        }
        $scope.toLogin = function(){
             $scope.login_step = 'LOGIN';
        }
        
        $scope.toPin = function(){
             $scope.login_step = 'PIN';
        }
        
        $scope.logout = function(id){
            AuthService.logout(id);
            if (!AuthService.current_user)
                $scope.lockup();
        }
        
        
        
        $scope.lockup = function(){
            AuthService.clearCurrent();
            DesktopManager.lockupDesktop();
            
            if (AuthService.logined_users.length > 0)
                $scope.login_step = 'PIN';
            else
                $scope.toLogin();
        }
        
        $scope.$watch('login_fields.pin1',function(newVal, oldVal)
        {
            if(newVal && newVal.length == 4){
                $scope.login_step = 'PIN2';
            }
        });
        
        $scope.$watch('login_fields.pin2',function(newVal, oldVal)
        {
            if(newVal && newVal.length == 4){
                trySetPin();
            }
        });
        
        $scope.$watch('login_fields.pin',function(newVal, oldVal)
        {
            if(newVal && newVal.length == 4){
                $scope.login_fields.pin = '';
                if (AuthService.switchAccount(newVal)){
                    $scope.login_step = 'OK';
                    DesktopManager.unlockDesktop();
                    return;
                }
                $scope.errorInfo.message ='pin码错误，请重试';
            }
        });
        
        $scope.statistical=function()
        {
            if($scope.statistical_data=='')
            {
                $http.get(urls.BASE_API + '/statistical').success(function(res) {
                    $scope.statistical_data=res;
                }).error(function(res) {
                    $scope.error=res.message;
                });
            }
            else
                $scope.statistical_data=''; 
        }
        
        $scope.$on('switch', function(event, data) {  
            if(data==true){
                 $scope.lockup();
            }
         });   

        
        
    }])
    
})();