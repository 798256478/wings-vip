/**
 * property Controller
 */
'use strict';

angular.module('app')
    .controller('ShopConfigController', ['$scope','$stateParams', '$location', 'AuthService','ShopConfigService','PropertyTemplateService','$uibModal', '$q','UploadService',
        function($scope,$stateParams, $location, AuthService,ShopConfigService,PropertyTemplateService,$uibModal, $q,UploadService) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('商城配置');
            $scope.show = {
                'shop':'',
                'category':'',
                'shopId':'',
                'isCategory':false,
	            'page':'a',//a:店铺列表;b:店铺详情;d:商城主页配置
	            'list':''//a:推荐商品列表;b:分类下的商品列表;c:商城主页下的头部商品列表;d:商城主页下的主推商品列表
            };
            $scope.pages = {
                'category':{
                    'nowPage':1
                },
                'commodity':{
                    'nowPage':1
                }
            };
            $scope.sort = {
                'commodity':{
                    'title':{
                        'sel':''
                    }
                }
            };
            $scope.batch={};
            $scope.noShopHeight={height:''+document.body.offsetHeight-145+'px'};

            //得到所有店铺
            $scope.getAllShop=function(){
                ShopConfigService.getAllShop().then(function(res){
                    $scope.shop=res.shop;
                },function(res){
                    alert(res.message);
                });
                $scope.show.shop='';
            };

            $scope.init=function(){
                $scope.getAllShop();
                PropertyTemplateService.getIcons().then(function(res) {
                    $scope.icons = res;
                }, function(res) {
                    alert(res.message);
                });
            };

            $scope.init();

            //显示店铺详情
            $scope.showDetail=function(id,force=false){
                if(!force) {
                    if ($scope.show.shop === id) {
                        return;
                    }
                }
                $scope.show.shop=id;
                $scope.show.shopId=$scope.shop[id]._id;
                $scope.show.category='';
                $scope.show.isCategory=true;
	            $scope.currentCommondity='';
	            $scope.recommendedCommondity=[];
	            if($scope.shop[$scope.show.shop].categories.length>0){
		            $scope.showCategory(0);
	            }
                $scope.getRecommendedCommondity();
	            $scope.show.page='b';
                $scope.show.list='';
            };

            //获得分类下的商品信息
            $scope.getCategoryCommondity=function(){
                ShopConfigService.getCommoditiesByArray($scope.shop[$scope.show.shop].categories[$scope.show.category].items).then(function(res){
                    $scope.currentCommondity=res.commodity;
                },function(res){
                    alert(res.message);
                });
            };

            //显示分类商品列表
            $scope.showCategory=function(id,force=false){
                if(!force) {
                    if ($scope.show.category === id) {
                        return;
                    }
                }
                $scope.show.category=id;
	            $scope.show.page='b';
                //$scope.pages.category.nowPage=1;
	            //$scope.currentCommondity='';
	            $scope.getCategoryCommondity();
            };

            //店铺图标弹出框
            $scope.getShopIcons = function() {
                $scope.items = [{
                    'title': "选择一个图标",
                    'type': 'list',
                    'options': $scope.icons,
                    'value': ''
                }, {
                    'title': "或上传一张图片",
                    'type': "image",
                    'value': ''
                }];
                var modalInstance = $uibModal.open({
                    animation: false,
                    templateUrl: 'modal.html',
                    controller: 'ModalInstanceCtrl',
                    size: 'lg',
                    resolve: {
                        items: function() {
                            return $scope.items;
                        }
                    }
                });

                modalInstance.result.then(function(setItems) {
                    var index1 = _.findIndex(setItems, function(n){
                        return n.type == "list";
                    });
                    var index2 = _.findIndex(setItems, function(n){
                        return n.type == "image";
                    });
                    $scope.shop[$scope.show.shop].icon = setItems[index1].value;
                    $scope.shop[$scope.show.shop].image_icon = setItems[index2].value;
                }, function() {
                    if ($scope.shop[$scope.show.shop].icon == undefined) {
                        $scope.shop[$scope.show.shop].icon =  '';
                    }
                    if ($scope.shop[$scope.show.shop].image_icon == undefined) {
                        $scope.shop[$scope.show.shop].image_icon = '';
                    }
                });
            }

            //添加店铺
            $scope.addShop=function(){
                $scope.shop.add={
                    'title':'',
                    'icon':'',
                    'image_icon':'',
                    'recommended_items':[],
                    'categories':[],
                    'sort':0
                };
                //$scope.copyShow=angular.copy($scope.show);
                $scope.show.shop='add';
                $scope.show.category='';
                $scope.show.isCategory=false;
                $scope.show.page='b';
                $scope.recommendedCommondity=[];
                $scope.show.shopId='add';
                //$scope.getShopTitle();
            }

            //保存店铺标题部分
            $scope.saveShopTitle=function(){
                ShopConfigService.saveShopTitle($scope.show.shopId,$scope.shop[$scope.show.shop]).then(function(res){
                    if($scope.show.shopId==='add') {//新增加店铺,获得新增加的店铺的ID,显示店铺下的分类页面,新增店铺的本地数据置空
                        $scope.show.isCategory = true;
                        $scope.shop.add = '';
                        $scope.show.shopId=res.shop[res.index]._id;
                    }
                    //判断当前店铺顺序是否改变
                    if(res.isIndex){//改变,重新获得店铺列表,获得改变后的店铺位置
                        $scope.shop=res.shop;
                        $scope.show.shop=res.index;
                    }
                    $scope.show.page='b';
                    $scope.batch={};
                    alert('保存成功');
                },function(res){
                    $scope.show.page = 'b';
                    alert(res.message);
                });
            };

            //保存分类信息
            $scope.saveShopCategory=function(){
                ShopConfigService.saveShopCategory($scope.show.shopId,$scope.show.category,
                    $scope.shop[$scope.show.shop].categories[$scope.show.category])
                    .then(function(res){
                        //TODO    保存信息是否新添加的区分
                        if($scope.show.category==='add') {//新加分类,本地新加分类数据置空
                            $scope.shop[$scope.show.shop].categories.add = '';
                        }
                        //判断当前分类顺序是否改变
                        if(res.isIndex){//改变,重新获得分类列表,获得改变后的分类位置
                            $scope.shop[$scope.show.shop].categories=res.category;
                            $scope.show.category=res.index;
                        }
                        $scope.show.page='b';
                        $scope.batch={};
                        alert('保存成功');
                    }, function (res) {
                        alert(res.message);
                    });
            };

            //添加分类
            $scope.addCategory=function(){
                $scope.shop[$scope.show.shop].categories.add={
                    'layout':'once',
                    'title':'',
                    'icon':'',
                    'image_icon':'',
                    'items':[],
                    'sort':0
                };
                //$scope.copyCategory=$scope.show.category;
                $scope.show.category='add';
                $scope.currentCommondity=[];
                $scope.show.page='b';
                $scope.batch={};
                //$scope.getCategoryTitle();
            };

            //分类图标弹出框
            $scope.getCategoryIcons=function(){
                $scope.items = [{
                    'title': "选择一个图标",
                    'type': 'list',
                    'options': $scope.icons,
                    'value': ''
                }, {
                    'title': "或上传一张图片",
                    'type': "image",
                    'value': ''
                }];
                var modalInstance = $uibModal.open({
                    animation: false,
                    templateUrl: 'modal.html',
                    controller: 'ModalInstanceCtrl',
                    size: 'lg',
                    resolve: {
                        items: function() {
                            return $scope.items;
                        }
                    }
                });

                modalInstance.result.then(function(setItems) {
                    var index1 = _.findIndex(setItems, function(n){
                        return n.type == "list";
                    });
                    var index2 = _.findIndex(setItems, function(n){
                        return n.type == "image";
                    });
                        $scope.shop[$scope.show.shop].categories[$scope.show.category].icon = setItems[index1].value;
                        $scope.shop[$scope.show.shop].categories[$scope.show.category].image_icon = setItems[index2].value;
                });

            };

            //得到当前页商品列表
            $scope.getCommoditiesList=function(){
                var a ={
                    'title':$scope.sort.commodity.title.sel,
                    'page':$scope.pages.commodity.nowPage
                }
                ShopConfigService.getCommoditiesList(a).then(function(res){
                    $scope.commodityList=res.commodity;
                    $scope.pages.commodity.commodityTotal=res.count;
                    $scope.batch={};
                    console.log($scope.commodityList);
                },function(res){
                    alert(res.message);
                });
            }

	        //依据名字得到第一页商品列表
	        $scope.getCommoditiesListFirstPage=function(){
		        $scope.pages.commodity.nowPage=1;
		        $scope.getCommoditiesList();
	        }

	        //显示商品列表,重置搜索条件,得到第一页商品列表
            $scope.showCommondityList=function(val){
	            $scope.show.list=val;
	            //if(val=='a'){
		         //   $scope.copyCommondity = $scope.shop[$scope.show.shop].recommended_items;_
	            //}else {
		         //   $scope.copyCommondity = $scope.shop[$scope.show.shop].categories[$scope.show.category].items;
	            //}
                $scope.pages.commodity.nowPage=1;
                $scope.sort.commodity.title.sel='';
                //if(val == 'd'){
                //    $scope.show.page = 'd';
                //}else {
                //    $scope.show.page = 'c';
                //}
                $scope.getCommoditiesList();
            };

            $scope.$watch('pages.commodity.nowPage',function(newVal,oldVal){
	            if(newVal==oldVal){
		            return false;
	            }
                $scope.getCommoditiesList();
            });

            //添加到分类商品列表
            $scope.addToCategory=function(commodity){
		        var a=$scope.shop[$scope.show.shop].categories[$scope.show.category].items.indexOf(commodity.id);
		        if(a>=0){
                    angular.forEach($scope.currentCommondity,function(val,key){
                        if(val.id==commodity.id){
                            $scope.currentCommondity.splice(key,1);
                        }
                    });
                    $scope.shop[$scope.show.shop].categories[$scope.show.category].items.splice(a,1);
		        }else{
                    $scope.currentCommondity.unshift(commodity);
                    $scope.shop[$scope.show.shop].categories[$scope.show.category].items.unshift(commodity.id);
		        }
	        };

            //分类的全选
            $scope.batchCategory=function(){
                if($scope.batch.batchCategory){//全选
                    angular.forEach($scope.commodityList,function(val,key){
                        var a=$scope.shop[$scope.show.shop].categories[$scope.show.category].items.indexOf(val.id);
                        if(a<0){//不在列表的添加进去
                            $scope.currentCommondity.unshift(val);
                            $scope.shop[$scope.show.shop].categories[$scope.show.category].items.unshift(val.id);
                        }
                    })
                }else{//反向全选
                    angular.forEach($scope.commodityList,function(val,key){
                        var a=$scope.shop[$scope.show.shop].categories[$scope.show.category].items.indexOf(val.id);
                        if(a>=0){//在列表的删除
                            $scope.shop[$scope.show.shop].categories[$scope.show.category].items.splice(a,1);
                            angular.forEach($scope.currentCommondity,function(b,c){
                                if(b.id==val.id){
                                    $scope.currentCommondity.splice(c,1);
                                }
                            });
                        }
                    })
                }
            };

            //分类的反选
            $scope.noBatchCategory=function(){
                angular.forEach($scope.commodityList,function(val,key){
                    var a=$scope.shop[$scope.show.shop].categories[$scope.show.category].items.indexOf(val.id);
                    if(a<0){//不在列表的添加进去
                        $scope.currentCommondity.unshift(val);
                        $scope.shop[$scope.show.shop].categories[$scope.show.category].items.unshift(val.id);
                    }else{//在列表的删除
                        $scope.shop[$scope.show.shop].categories[$scope.show.category].items.splice(a,1);
                        angular.forEach($scope.currentCommondity,function(b,c){
                            if(b.id==val.id){
                                $scope.currentCommondity.splice(c,1);
                            }
                        });
                    }
                })
            };
            //显示主推编辑页面
            //如果是商品,$scope.recommendedCommondity[a]存储的为商品信息,图片地址在$scope.shop[$scope.show.shop].recommended_items[a]
            //如果是链接,$scope.recommendedCommondity[a]存储的为image和链接地址,
            $scope.editRecommended=function(a){
                $scope.recommended={};
                if(a==='add'){
                    $scope.recommended.type='commodity';
                    $scope.recommended.image='';
                    $scope.recommended.url='';
                    $scope.recommended.commodity={};
                }else{
                    if($scope.shop[$scope.show.shop].recommended_items[a].id){//是商品类型
                        $scope.recommended.type='commodity';
                        $scope.recommended.image=$scope.shop[$scope.show.shop].recommended_items[a].image;
                        $scope.recommended.url='';
                        $scope.recommended.commodity= $scope.recommendedCommondity[a];
                        //重置搜索条件
                        $scope.pages.commodity.nowPage=1;
                        $scope.sort.commodity.title.sel='';
                    }else{
                        $scope.recommended.type='url';
                        $scope.recommended.image= $scope.recommendedCommondity[a].image;
                        $scope.recommended.url= $scope.recommendedCommondity[a].url;
                        $scope.recommended.commodity={};
                    }
                }
                $scope.getCommoditiesList();
                $scope.recommended.id=a;
                //$scope.show.page='c';
                $scope.show.list='a';
            };

            //点击商品添加至主推
            $scope.checkRecommended=function(commodity){
                $scope.recommended.commodity=commodity;
            };
            //上传图片
            $scope.uplodeImage = function(type,file, errFiles) {
                UploadService.addImage(file, errFiles).then(function(res) {
                    if (res != undefined && res != '') {
                        if(type == 'recommended') {
                            $scope.recommended.image = res;
                        }else if(type == 'carousel'){
                            $scope.shopPage.carousel.image= res;
                        }else if(type == 'introduce'){
                            $scope.shopPage.content.introduce.url = res;
                        }
                    }
                }, function(res) {
                    if (errFiles.length > 0)
                        alert(res);
                });
            };

            //添加到主推商品列表
            $scope.addToRecommended=function(commodity){
                if(!$scope.recommended.image){
                    alert('请上传一张图片');
                    return;
                }
                if($scope.recommended.type==='commodity'){
                    if(!$scope.recommended.commodity.id){
                        alert('请选择一种商品');
                        return;
                    }
                    var a={
                        'id':$scope.recommended.commodity.id,
                        'image':$scope.recommended.image
                    };
                    if($scope.recommended.id==='add') {
                        $scope.shop[$scope.show.shop].recommended_items.push(a);
                        $scope.recommendedCommondity.push($scope.recommended.commodity);
                    }else{
                        $scope.shop[$scope.show.shop].recommended_items[$scope.recommended.id]=$scope.recommended.commodity.id;
                        $scope.recommendedCommondity[$scope.recommended.id]=$scope.recommended.commodity;
                    }
                }
                if($scope.recommended.type==='url'){
                    if(!$scope.recommended.url){
                        alert('请输入链接地址');
                        return;
                    }
                    var a={
                        'url':$scope.recommended.url,
                        'image':$scope.recommended.image
                    };
                    if($scope.recommended.id==='add') {
                        $scope.shop[$scope.show.shop].recommended_items.push(a);
                        $scope.recommendedCommondity.push(a);
                    }else{
                        $scope.shop[$scope.show.shop].recommended_items[$scope.recommended.id]=a;
                        $scope.recommendedCommondity[$scope.recommended.id]=a;
                    }
                }
                $scope.recommended={};
                $scope.show.list='';
            };

            //删除店铺的主推商品
            $scope.deleteRecommended=function(id,$event){
                $scope.shop[$scope.show.shop].recommended_items.splice(id,1);
                $scope.recommendedCommondity.splice(id,1);
                $scope.show.list='';
                $event.stopPropagation();
            };
	        //得到主推商品列表信息
	        $scope.getRecommendedCommondity=function(){
                var a=[];
                var b={};
                angular.forEach($scope.shop[$scope.show.shop].recommended_items,function(val,key){
                    if(val.id){
                        a[key] = val.id;
                    }else{
                        b[key] = val;
                    }
                });

		        ShopConfigService.getCommoditiesByArray(a).then(function(res){
                    angular.forEach(a,function(val,key){
                        angular.forEach(res.commodity,function(commodityVal,commodityKey){
                            if(val == commodityVal.id){
                                $scope.recommendedCommondity[key] = commodityVal;
                            }
                        });
                    });
                    
                    angular.forEach(b,function(val,key){
                        if(val){
                            $scope.recommendedCommondity[key] = val;
                        }
                    });
                },function(res){
			        alert(res.message);
		        });
	        };

	        //选择模版
	        $scope.saveLayout=function(val){
		        //ShopConfigService.saveLayout($scope.show.shopId,$scope.show.category,val).then(function(res){
			        $scope.shop[$scope.show.shop].categories[$scope.show.category].layout=val;
		        //},function(res){
			     //   alert(res.message);
		        //});

	        };

            //删除店铺
            $scope.deleteShop=function(index){
                event.stopPropagation();
                $scope.items=[{
                    'title':'删除操作不能恢复,确认要删除'+$scope.shop[index].title+'么?',
                    'type':'affirm',
                }];
                var modalInstance = $uibModal.open({
                    animation: false,
                    templateUrl: 'affirm.html',
                    controller: 'ModalInstanceCtrl',
                    size: 'sm',
                    resolve: {
                        items: function () {
                            return $scope.items;
                        }
                    }

                });
                modalInstance.result.then(function(setItems) {
                    ShopConfigService.deleteShop($scope.shop[index]._id).then(function(res){
                        $scope.shop.splice(index,1);
                        if($scope.shop.length==0){
                            $scope.show.page='a';
                            $scope.show.list='';
                            $scope.show.shop='';
                        }else{
                            if($scope.shop.length-1<$scope.show.shop){
                                $scope.show.shop=$scope.shop.length-1;
                            }
                            $scope.showDetail($scope.show.shop,true);
                        }
                    },function(res){
                        alert(res.message);
                    });
                },function(){

                });
            }

            //删除分类
            $scope.deleteCategory=function(id){
                event.stopPropagation();
                $scope.items=[{
                    'title':'删除操作不能恢复,确认要删除'+$scope.shop[$scope.show.shop].categories[id].title+'么?',
                    'type':'affirm',
                }];
                var modalInstance = $uibModal.open({
                    animation: false,
                    templateUrl: 'affirm.html',
                    controller: 'ModalInstanceCtrl',
                    size: 'sm',
                    resolve: {
                        items: function () {
                            return $scope.items;
                        }
                    }

                });
                modalInstance.result.then(function(setItems) {
                    ShopConfigService.deleteCategory($scope.show.shopId,id).then(function(res){
                        $scope.shop[$scope.show.shop].categories.splice($scope.show.category,1);
                        if($scope.shop[$scope.show.shop].categories.length==0){
                            $scope.show.page='b';
                            $scope.show.list='';
                            $scope.show.category='';
                        }else{
                            if($scope.shop[$scope.show.shop].categories.length-1<$scope.show.category){
                                $scope.show.category=$scope.shop[$scope.show.shop].categories.length-1;
                            }
                            $scope.showCategory($scope.show.category,true);
                        }
                    },function(res){
                        alert(res.message);
                    });
                },function(){

                });
            }

            //************************************商城主页**********************************//
            $scope.shopPage ={};
            $scope.shopPage.commodities = [];//主页主推商品列表
            //$scope.shopPage.carousel={};//主页被选中的轮播图
            //$scope.shopPage.carouselCommondity=[]//主页要显示在页面中的轮播图列表
            $scope.showHomePage = function(){
                if($scope.show.page != 'd'){
                    $scope.show.page = 'd';
                    $scope.show.shop = '';
                    ShopConfigService.getShopPage().then(function(res){
                        $scope.shopPage.content = res.mall;
                        if(!$scope.shopPage.content){
                            $scope.shopPage.content = {
                                'carousel': {
                                    'name': '头部广告',
                                    'type': 'carousel',
                                    'disabled': false,
                                    'is_show_name': false,
                                    'is_show_logo': true,
                                    'items': [
                                        //{
                                        //    type:'commodity',
                                        //    commodityId:1,
                                        //    image:'assfwsef'
                                        //},
                                        //{
                                        //    type:'image',
                                        //    image:'assfwsef'
                                        //},
                                        //{
                                        //    type:'url',
                                        //    'url':'asdasfaf',
                                        //    image:'assfwsef'
                                        //}
                                    ],
                                    'order': 1
                                },
                                'shop': {
                                    'name': '分类',
                                    'type': 'shop',
                                    'disabled': false,
                                    'is_show_name': false,
                                    'order': 2
                                },
                                'commodities': {
                                    'name': '主推商品',
                                    'type': 'commodities',
                                    'disabled': false,
                                    'is_show_name': false,
                                    'items': [],
                                    'order': 3
                                },
                                'introduce': {
                                    'name': '底部介绍',
                                    'type': 'introduce',
                                    'disabled': false,
                                    'is_show_name': false,
                                    'url': '',
                                    'order': 4
                                }
                            };
                        }
                        $scope.getShopPageCarouselCommondity();
                        if($scope.shopPage.content.commodities.items.length>0){
                            ShopConfigService.getCommoditiesByArray($scope.shopPage.content.commodities.items).then(function(res){
                                $scope.shopPage.commodities = res.commodity;
                            },function(res){
                                alert(res.message);
                            });
                        }
                    },function(res){
                        alert(res.message);
                    });
                }else{

                }
                $scope.show.list='';
            }

            //删除主页轮播图
            $scope.deleteShopPageCarousel = function(id,$event){
                $scope.shopPage.content.carousel.items.splice(id,1);
                $scope.shopPage.carouselCommondity.splice(id,1);
                $event.stopPropagation();
                $scope.show.list='';
            };

            //添加或减少主页主推商品
            $scope.addToShopPageCommondities = function(commodity,$event){
                var a=$scope.shopPage.content.commodities.items.indexOf(commodity.id);
                if(a>=0){
                    angular.forEach($scope.shopPage.commodities,function(val,key){
                        if(val.id==commodity.id){
                            $scope.shopPage.commodities.splice(key,1);
                        }
                    });
                    $scope.shopPage.content.commodities.items.splice(a,1);
                }else{
                    if($scope.shopPage.content.commodities.items.length>=6){
                        alert('主推商品最多6个');
                        $event.preventDefault();
                        return;
                    }
                    $scope.shopPage.commodities.unshift(commodity);
                    $scope.shopPage.content.commodities.items.unshift(commodity.id);
                }
            }

            //删除底部介绍
            $scope.deleteShopPageIntroduce = function(){
                $scope.shopPage.content.introduce.url = '';
            };

            //保存主页
            $scope.saveShopPage = function(){
                ShopConfigService.saveShopPage($scope.shopPage.content).then(function(res){
                    alert('主页保存成功');
                },function(res){
                    alert(res.message);
                });
            }

            //显示轮播图编辑页
            $scope.editShopPageCarousel = function(a){
                $scope.shopPage.carousel={};
                if(a === 'add'){
                    $scope.shopPage.carousel.type = 'commodity';
                    $scope.shopPage.carousel.image = '';
                    $scope.shopPage.carousel.url = '';
                }else{
                    $scope.shopPage.carousel = $scope.shopPage.carouselCommondity[a];
                }
                if($scope.shopPage.carousel.type == 'commodity'){
                    //重置搜索条件
                    $scope.pages.commodity.nowPage=1;
                    $scope.sort.commodity.title.sel='';
                }
                $scope.getCommoditiesList();
                $scope.shopPage.carousel.indexId = a;//在列表中的位置
                $scope.show.list='c';
            };

            //添加至主页轮播图
            $scope.addToShopPageCarousel = function(){
                if(!$scope.shopPage.carousel.image){
                    alert('请上传一张图片');
                    return;
                }
                if($scope.shopPage.carousel.type === 'commodity'){
                    if(!$scope.shopPage.carousel.commodityId){
                        alert('请选择一种商品');
                        return;
                    }
                    var a={
                        'commodityId':$scope.shopPage.carousel.commodityId,
                        'image':$scope.shopPage.carousel.image,
                        'type':'commodity'
                    };
                    if($scope.shopPage.carousel.indexId === 'add') {
                        $scope.shopPage.content.carousel.items.push(a);
                        $scope.shopPage.carouselCommondity.push($scope.shopPage.carousel);
                    }else{
                        $scope.shopPage.content.carousel.items[$scope.shopPage.carousel.indexId] = a;
                        $scope.shopPage.carouselCommondity[$scope.shopPage.carousel.indexId] = $scope.shopPage.carousel;
                    }
                }
                if($scope.shopPage.carousel.type === 'url' || $scope.shopPage.carousel.type === 'image'){
                    if($scope.shopPage.carousel.type === 'url') {
                        if (!$scope.shopPage.carousel.url) {
                            alert('请输入链接地址');
                            return;
                        }
                        var a = {
                            'url': $scope.shopPage.carousel.url,
                            'image': $scope.shopPage.carousel.image,
                            'type':'url'
                        };
                    }
                    if($scope.shopPage.carousel.type === 'image'){
                        var a = {
                            'image': $scope.shopPage.carousel.image,
                            'type':'image'
                        };
                    }
                    if($scope.shopPage.carousel.indexId === 'add') {
                        $scope.shopPage.content.carousel.items.push(a);
                        $scope.shopPage.carouselCommondity.push(a);
                    }else{
                        $scope.shopPage.content.carousel.items[$scope.shopPage.carousel.indexId] = a;
                        $scope.shopPage.carouselCommondity[$scope.shopPage.carousel.indexId] = a;
                    }
                }
                $scope.shopPage.carousel={};
                $scope.show.list='';
            };

            //得到轮播图商品信息
            $scope.getShopPageCarouselCommondity=function(){
                var a=[];//轮播图中的商品类型的商品ID
                var b={};//轮播图中的连接和图片类型
                $scope.shopPage.carouselCommondity = [];
                angular.forEach($scope.shopPage.content.carousel.items,function(val,key){
                    if(val.type == 'commodity'){
                        a[key] = val.commodityId;
                    }else{
                        b[key] = val;
                    }
                });

                ShopConfigService.getCommoditiesByArray(a).then(function(res){
                    angular.forEach(a,function(val,key){
                        angular.forEach(res.commodity,function(commodityVal,commodityKey){
                            if(val == commodityVal.id){
                                $scope.shopPage.carouselCommondity[key] ={};
                                $scope.shopPage.carouselCommondity[key].name = commodityVal.name;
                                $scope.shopPage.carouselCommondity[key].image = $scope.shopPage.content.carousel.items[key].image;
                                $scope.shopPage.carouselCommondity[key].commodityId = commodityVal.id;
                                $scope.shopPage.carouselCommondity[key].type = 'commodity';
                            }
                        });
                    });

                    angular.forEach(b,function(val,key){
                        if(val){
                            $scope.shopPage.carouselCommondity[key] = val;
                        }
                    });
                },function(res){
                    alert(res.message);
                });
            };

            //选择一个商品作为轮播图的一个元素
            $scope.checkShopPageCarousel =function(commodity){
                $scope.shopPage.carousel.commodityId = commodity.id;
                $scope.shopPage.carousel.name = commodity.name;
            }
        }
    ]);
