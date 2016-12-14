(function () {
    'use strict';
    
    angular.module('app')
    .controller('CardController', ['$http','$scope','AuthService','CardService','SettingService',
    function($http, $scope, AuthService, CardService,SettingService){
        
        $scope.cardSummaries = [];
        $scope.selectcard=null;
        $scope.cardInfo=null;
        $scope.cardSettings={};
        
        SettingService.getSetting('CARD').then(function(res){
             $.each(res.levels,function(k,level){
                  $scope.cardSettings[level.id]=level.name;
             })
        },function(res){
            $scope.errorInfo.message=res.message;
        })
        
        
        $scope.getCardSummaries = function() {
            //$scope.error = null;
            $scope.cardSummaries = [];
            
            CardService.getCardSummaries().then(function(res){
                $scope.cardSummaries = res.cards;
            },function(res){
                 $scope.errorInfo.message = res.message;
            });
        }   
        $scope.getCard=function (id) {
            if (typeof(id) == "undefined") { 
                return;
            }  
            CardService.getCard(id).then(function(res) {
                $scope.cardInfo=res;
                $scope.selectcard=null;
                $scope.$broadcast('cardchange');  
                $scope.initfoucus();
            },function(res) {
                $scope.errorInfo.message=res.message;
            });
        }
        

        $scope.refreshCard=function() {
            $scope.getCard($scope.cardInfo.card.id);
        }
        $scope.$watch('selectcard',function(newVal, oldVal)
        {
            if($scope.selectcard!=null){
                //选中会员卡，更新卡面和历史记录
                 $scope.getCard($scope.selectcard.id);
            }
        })
        
        $scope.$watch('AuthService.current_user',function(newVal, oldVal)
        {
            if(oldVal==null&&newVal!=null)
            {
                 $scope.getCardSummaries();
            }
        })
    }])
    
})();