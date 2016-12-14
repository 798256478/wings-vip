(function () {
    'use strict';

    angular.module('app')
        .controller('MasterController', ['$rootScope', '$scope', 'AuthService', 'DesktopManager','SettingService',
            function ($rootScope, $scope, AuthService, DesktopManager,SettingService) {
                
                $scope.AuthService = AuthService;
                $scope.DesktopManager = DesktopManager;
                $scope.errorInfo = {message:'',code:''};
                $scope.currentPage='';
               
                SettingService.getSetting('CASHIER_CLIENT').then(function (res) {
                    $scope.cashier_client = res;
                    if($scope.cashier_client.menus.length > 0 )
                    {
                          $scope.currentPage = $scope.cashier_client.menus[0]['page'];
                    }
                    for(var i=0; i < $scope.cashier_client.menus.length; i++)
                    {
                        if($scope.cashier_client.menus[i]['page'] == 'CONSUME'&&
                        $scope.cashier_client.menus[i]['disable'] === false)
                        {
                             $scope.currentPage = $scope.cashier_client.menus[i]['page'];
                        }
                    }
                },function (res) {
                    $scope.error=res.message;
                })
                
                $scope.convertpayment = function(itemkey, model, methods){
                    $scope.payments[itemkey] = {};
                    $scope.payments[itemkey].bonus = model.bonus;
                    $scope.payments[itemkey].methods = {};
                    $.each(model.methods, function(key, rate){ 
                        $.each(methods, function(i, item){ 
                            if(item.name == key && item.disabled == false) 
                            {
                                $scope.payments[itemkey].methods[key] = {};  
                                $scope.payments[itemkey].methods[key].type = item.type;
                                $scope.payments[itemkey].methods[key].amount = '';
                                $scope.payments[itemkey].methods[key].rate = rate;
                                $scope.payments[itemkey].methods[key].name = key;
                            } 
                               
                        })
                    }); 
                }
                
                SettingService.getSetting('PAYMENT').then(function (res) {
                    $scope.payments = [];
                    $scope.bonus_rule = res.bonus_rule;
                    $scope.convertpayment('balance',res.balance,res.methods);
                    $scope.convertpayment('consume',res.consume,res.methods);
                    $scope.convertpayment('goods',res.goods,res.methods);
                 
                    
                },function (res) {
                    $scope.error=res.message;
                })
        
                
                $scope.switchPage = function (str) {
                    if(AuthService.current_user.roles != 'captain'&&str == 'INITIALIZE'){
                         $scope.errorInfo.message = '您没有该权限，请联系领班';
                         return;
                    }
                    $scope.currentPage=str;
                }
                
                $scope.initfoucus = function()
                {
                    var  $focus;
                    if($scope.currentPage == "BALANCE")
                        $focus='input[name="balance_fee"]';
                    if($scope.currentPage == "CONSUME")
                        $focus='input[name="total_fee"]';
                    if($scope.currentPage == "CONSUME")
                        $focus='input[name="total_fee"]';
                    $scope.DesktopManager.setFocus($focus);
                }
                
                $scope.switchUser = function() {
                     $scope.$broadcast('switch', true);  
                }
                $scope.$watch('errorInfo.message',function(newVal,oldVal) {
                     if(newVal||newVal.length > 0){
                         swal(newVal);
                         $scope.errorInfo.message = '';
                     }
                })
            }])
        
})();