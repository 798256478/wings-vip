/**
 * GoodCode Controller
 */
'use strict';

angular.module('app')
.controller('BarcodeController', ['$scope', '$location', 'AuthService','BarcodeService','HealthCustomerService','ExperimentService','$q',
    function($scope, $location, AuthService,BarcodeService,HealthCustomerService,ExperimentService,$q) {
        if (AuthService.current_user == null) {
            $location.path("/login");
            return;
        }
        $(".header .meta .page").text('产品条码');
        $scope.searchValue = '';
        $scope.isShow = '';
        $scope.add = {};
        $scope.pages = {
            nowPage: 1,
            total: 0,
            pagesize:14
        };
        BarcodeService.getProgressConfig().then(function(res){
            $scope.progresses = res.progresses;
        },function(res){
            alert(res.message);
        });
        
        ExperimentService.get_experiments().then(function(res){
            $scope.experiments = res;
        },function(res){
            alert(res.message);
        });
        
        //获得当前页条码列表和总条码数
        $scope.getBarcodes=function(page=1,code=''){
            BarcodeService.getBarcodes(page,$scope.pages.pagesize,$scope.searchValue).then(function(res){
	            $scope.pages.total=parseInt(res.total);
                $scope.barcodes = res.barcodes;
            },function(res){
                alert(res.message);
            });
        };
        $scope.getBarcodes();

	    //监听页数改变
	    $scope.$watchCollection('pages.nowPage', function(newVal, oldVal) {
            if(newVal == oldVal) return;
            if(newVal <= 0)  return;
		    $scope.getBarcodes($scope.pages.nowPage);
	    });


        //按照搜索取列表
        $scope.Search = function() {
            if($scope.searchValue.length > 0 && $scope.searchValue.length < 3)
                return;
            $scope.getBarcodes($scope.pages.nowPage);
        };


        //点击显示客户修改页面
        $scope.show_edit_customer=function(data){
            $scope.editCustomer={};
            if(data.customer){
                $scope.editCustomer.id=data.customer.id;
            }
            $scope.editCustomer.name=data.customer?data.customer.name:null;
            $scope.editCustomer.mobile=data.customer?data.customer.mobile:null;
            $scope.editCustomer.sex=data.customer && data.customer.sex?data.customer.sex+'':'0';
            $scope.editCustomer.age=data.customer?data.customer.age:null;
            $scope.editCustomer.address=data.customer?data.customer.address:null;
            $scope.editCustomer.code=data.code;
                $scope.isShow='customer';
        };

        //保存客户资料
        $scope.editCustomer_save=function(){
            //编辑保存
            HealthCustomerService.editCustomer_save($scope.editCustomer).then(function(res){
                $scope.getBarcodes($scope.pages.nowPage);
                $scope.isShow='';
                alert(res);
            },function(res){
                alert(res.message);
            });
        
        };

        //添加条码
        $scope.add_code=function(){
            if(!$scope.add.code){
                alert('未输入条码');
                return;
            }
            if(!angular.isString($scope.add.code)){
                alert('条码不正确');
                return;
            }
            BarcodeService.addBarcode($scope.add.code).then(function(res){
                $scope.add.code='';
                $scope.getBarcodes($scope.pages.nowPage);
                alert(res);
            },function(res){
                alert(res.message);
            });
        };

        //上传条码文件
        $scope.fileUpload = function(file) {
            if(!file){
                alert('未选择文件');
                return;
            }
            BarcodeService.upload(file).then(function(res) {
                $scope.getBarcodes($scope.pages.nowPage);
                alert('共插入'+res+'个条码');
            }, function(res) {
                alert(res.message);
            });
        };
        
        $scope.changeShow=function(show){
            $scope.isShow = show;
        };
        
        $scope.show_edit_codeInfo = function(code){
             BarcodeService.getBarcodeInfo(code).then(function(res){
                 $scope.currentCodeInfo = res.barcode;
                 $scope.isShow = 'codeInfo';
                 $scope.currentexperiment = {barcode_id:res.barcode.id};
             },function(res){
                alert(res.message);
            });
        }

        $scope.edit_currentexperiment = function(data){
             $scope.currentexperiment = data;
        }
        $scope.edit_currentexperiment_save =function(){
             BarcodeService.changeBarcodeInfo( $scope.currentexperiment).then(function(res){
                 $scope.show_edit_codeInfo( $scope.currentCodeInfo.code);
             },function(res){
                alert(res.message);
            });
        }
        
         
    }
]);
