/**
 * property Controller
 */
'use strict';

angular.module('app')
    .controller('DeliverController', ['$scope','$stateParams', '$location', 'AuthService', 'OrderService', '$q','$uibModal',
        function($scope,$stateParams, $location, AuthService, OrderService, $q,$uibModal) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }

            $(".header .meta .page").text('发货');

            $scope.id=$stateParams.id;

            $scope.express = {
                'id':$scope.id,
                'type':'SELF',
                'company':null,
                'express_code':null
            }; 
            
            $scope.edit=false;

            $scope.getDeliverList=function(id){
            	OrderService.getDeliverList(id).then(function(res){
            		$scope.deliverList=res.deliverList;
                    $scope.companyList=res.order.express_company;
            	}, function(res) {
                    alert(res.message);
                });
            }

            $scope.submit = function(isValid) {
            	if(isValid){
            		OrderService.deliver($scope.express).then(function(res) {
                        swal('发货成功');
                        $location.path("/order");
                    }, function(res) {
                        alert(res.message);
                        $scope.error = res.errors;
                    });
            	}
            }

            $scope.showedit=function(){
                $scope.edit=true;
            }


            $scope.hideEdit=function(){
                $scope.edit=false;
            }

            $scope.editAddress = function(id,address){
                var index=_.findIndex($scope.deliverList, function(chr) {
                  return chr.order.id == id;
                });
                var addressInfo={'id':id,'address':address};
                OrderService.editAddress(addressInfo).then(function(res) {
                        $scope.edit=false;
                        ($scope.deliverList)[index].order.address=address;
                    }, function(res) {
                        alert(res.message);
                        $scope.error = res.errors;
                });
            }

            $scope.clear=function(){
                $scope.express.company=null;
                $scope.express_code=null;
            }

            $scope.initData=function(){
            	$scope.getDeliverList($scope.id);
            }
            $scope.initData();
        }
    ]);
