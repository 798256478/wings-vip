/**
 * HealthCustomer Controller
 */
'use strict';

angular.module('app')
	.controller('HealthCustomerController', ['$scope', '$location', 'AuthService', 'HealthCustomerService','BarcodeService', '$q',
		function ($scope, $location, AuthService, HealthCustomerService,BarcodeService, $q) {
			if (AuthService.current_user == null) {
				$location.path("/login");
				return;
			}
			$(".header .meta .page").text('健康客户');
			$scope.addCustomer={
				'sex':'0'
			};
			$scope.editCustomer={};
			$scope.pages = {
				'nowPage': 1
			};
			$scope.progressConfig={
				'0':'未使用'
			};
			$scope.search={};
			$scope.showOne=function(data){
				$scope.isShow=data;
			};
			$scope.sexConfig={
				2:'女',
				1:'男',
				0:'未知'
			};

			//初始化
			$scope.init=function(){
				HealthCustomerService.init().then(function(res){
					$scope.pages.codeTotal=parseInt(res.total);
					angular.forEach(res.progresses,function(val,key){
						$scope.progressConfig[val.id]=val.name;
					});
					$scope.customerList=res.customers;

				},function(res){
					alert(res.message);
				});
			};
			$scope.init();

			//新增客户
			$scope.addCustomer_save=function(){
				HealthCustomerService.editCustomer_save($scope.addCustomer).then(function(res){
					$scope.addCustomer={};
					$scope.addCustomer.sex='0';
					$scope.getCustomerList($scope.pages.nowPage,$scope.search.name);
					alert(res);
				},function(res){
					alert(res.message);
				});
			};

			//得到当前页列表和页面总数
			$scope.getCustomerList=function(page,name=0){
				HealthCustomerService.getCustomerList(page,name).then(function(res){
					$scope.customerList=res.customers;
					$scope.pages.codeTotal=parseInt(res.total);
					//console.log(res);
				},function(res){
					alert(res.message);
				});
			};

			////得到页面总数
			//$scope.getCustomerTotal=function(name=0){
			//	HealthCustomerService.getCustomerTotal(name).then(function(res){
			//		$scope.pages.codeTotal=parseInt(res);
			//		//console.log(res);
			//	},function(res){
			//		alert(res.message);
			//	});
			//};

			//得到进度配置信息
			$scope.getProgressConfig=function(){
				BarcodeService.getProgressConfig().then(function(res){
					angular.forEach(res.progresses,function(val,key){
						$scope.progressConfig[val.id]=val.name;
					});
					//console.log(res.progresses);
					//console.log($scope.sort.list);
				},function(res){
					alert(res.message);
				});
			};
			//$scope.getProgressConfig();

			$scope.Search=function(name){
				if(name.length>0) {
					$scope.search.name = name;
				}else{
					$scope.search.name=0;
				}
			};

			//显示修改页面
			$scope.showEditCustomer=function(customer){
				$scope.editCustomer.name=customer.name;
				$scope.editCustomer.mobile=customer.mobile;
				$scope.editCustomer.sex=customer.sex!=null?customer.sex:'0';
				$scope.editCustomer.age=customer.age;
				$scope.editCustomer.code=customer.code?customer.code.code:'';
				$scope.editCustomer.address=customer.address?customer.address:'';
				$scope.editCustomer.id=customer.id;
				$scope.isShow='editCustomer';
				//console.log($scope.editCustomer.sex);

			};

			//保存修改
			$scope.editCustomer_save= function () {
				HealthCustomerService.editCustomer_save($scope.editCustomer).then(function(res){
					$scope.isShow='';
					$scope.getCustomerList($scope.pages.nowPage,$scope.search.name);
					alert(res);
				}, function (res) {
					alert(res.message);
				});
			};



			$scope.$watchCollection('pages.nowPage',function(newVal,oldVal){
				if(newVal==oldVal){
					return;
				}
				$scope.getCustomerList(newVal,$scope.search.name);
			});

			$scope.$watchCollection('search.name',function(newVal,oldVal){
				if(newVal==oldVal){
					return;
				}
				$scope.pages.nowPage=1;
				$scope.getCustomerList(1,newVal);
			});



		}
	]);

