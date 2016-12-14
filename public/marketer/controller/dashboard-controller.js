/**
 * Dashboard Controller
 */
'use strict';

angular.module('app')
.controller('DashboardController', ['$scope', '$state','$location', 'AuthService','DashboardService', '$q',
    function($scope,$state ,$location, AuthService,DashboardService, $q) {
        if(AuthService.current_user == null){
            $location.path("/login");
        }
        $(".header .meta .page").text('仪表盘');

        $scope.key='CARD';

        $scope.getInitData=function(){
            DashboardService.getInitData($scope.key).then(function(res){
                $scope.finishOrderList = res.orderData.deal;
                $scope.unfinishOrderList = res.orderData.undeal;
                $scope.eventruleList = res.eventruleList;
                $scope.saleTop=res.saleTop;
                $scope.totalData=res.totalData;
                $scope.actionJobList = res.actionJobList;
                $scope.levels = res.levels.levels;
                $scope.actionPropertyTemplateList = res.actionPropertyTemplateList;
                $scope.actionTicketList = res.actionTicketList;
                $scope.getSaleSort();
            },function(res){
                alert(res.message);
            })
        }

        $scope.getSaleSort=function(){
            var temp = 0 ;
            var data=$scope.saleTop;
            $scope.earnSort='';
            $scope.saleSort==''?$scope.saleSort='desc':($scope.saleSort=='desc'?$scope.saleSort='asc':$scope.saleSort='desc');
            var sort=$scope.saleSort; 
            sort=='asc'?sort='<':sort='>';            
            for (var i = 0 ; i < data.length - 1 ; i++)
            {
                for (var j = i + 1 ; j < data.length ; j++)
                {
                    if (eval((data[j].sum?data[j].sum:0)+sort+(data[i].sum?data[i].sum:0)))
                    {
                        temp = data[i] ;
                        data[i] = data[j] ;
                        data[j] = temp ;
                    }
                }
            }  
        }


        $scope.deliver=function(id){
            $state.go('deliver',{id:id});
        };

        $scope.recipient = {
            'SELF': '本人',
            'REFERRER': '推荐人',
            'ROOT_REFERRER': '二级推荐人'
        };

        $scope.getTicketName = function(id){
            var index = _.findIndex($scope.actionTicketList, function(n){
                return n.id == id;
            });
            if(index > -1){
                return $scope.actionTicketList[index].title;
            }else {
                return '';
            }
        }
        $scope.getPropertyName = function(id){
            var index = _.findIndex($scope.actionPropertyTemplateList, function(n){
                return n.id == id;
            });
            if(index > -1){
                return $scope.actionPropertyTemplateList[index].title;
            }else {
                return '';
            }
        }

        $scope.getLevelName = function(id){
            var index = _.findIndex($scope.levels, function(n){
                return n.id == id;
            });
            if(index > -1){
                return $scope.levels[index].name;
            }else {
                return '';
            }
        }

        $scope.getType = function(args){
            return typeof(args);
        }

        $scope.eventLink=function(rule){
        	$state.go('event',{rule:rule});
        }

        $scope.getInitData();
    }
]);
