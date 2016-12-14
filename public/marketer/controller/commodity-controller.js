/**
 * property Controller
 */
'use strict';

angular.module('app')
    .controller('CommodityController', ['$scope', '$location', 'AuthService', 'CommodityService', 'UploadService', '$q',
        function($scope, $location, AuthService, CommodityService, UploadService, $q) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('商品管理');
            $scope.form = {}; //初始化form。为了重置表单验证状态
            $scope.commodity = {
                'image': []
            }; //初始商品
            $scope.spe_type = {
                'state': 'no'
            }; //是否是套装
            $scope.nowType = 'product'; //现在是添加或修改哪一种
            $scope.specification = {}; //初始化规格
            $scope.garbage = []; //要删除的图片
            $scope.oldImgList = []; //原图片列表
            $scope.active = {
                'val': 1
            };
            $scope.text = "新建商品";
            $scope.commodityCategory = null;
            $scope.commission = {
                'isopen': false,
                'text': '无返还',
                'unit': '',
            }; //分类佣金
            $scope.changeCommission = function(text, unit) {
                $scope.commission.text = text;
                $scope.commission.unit = unit;
            } //分类佣金
            $scope.commodityCommission = {
                'isopen': false,
                'text': '无返还',
                'unit': '',
            }; //商品佣金
            $scope.changeCommodityCommission = function(text, unit) {
                $scope.commodityCommission.text = text;
                $scope.commodityCommission.unit = unit;
            } //商品佣金

            /**
             * 获取当前选择的资产的列表
             * @method function
             * @param  {string} type 对应资产的名称
             * @return {null}
             */
            $scope.getSellableList = function(type) {
                $scope.sellableList = [];
                var deferred = $q.defer();
                CommodityService.getSellableList(type).then(function(res) {
                    $scope.sellableList = res;
                    deferred.resolve();
                }, function(res) {
                    alert(res.message);
                    deferred.reject(res);
                });
                return deferred.promise;
            }

            //商品分类
            $scope.getCommodityCategoryList = function(){
                CommodityService.getCommodityCategoryList().then(function(res) {
                    $scope.commodityCategoryList = res.commodity_categories;
                }, function(res) {
                    alert(res.message);
                });
            }
            $scope.getCommodityCategoryList();

            $scope.getCommodityCategory = function(category) {
                $scope.commodityCategory = category;
                if ($scope.commodityCategory.commission == null) {
                    $scope.commission = {
                        'isopen': false,
                        'text': '无返还',
                        'unit': '',
                    };
                }else if ($scope.commodityCategory.commission >= 1) {
                    $scope.commission = {
                        'isopen': false,
                        'text': '固定金额',
                        'unit': '元',
                    };
                }else if ($scope.commodityCategory.commission < 1) {
                    $scope.commodityCategory.commission = $scope.commodityCategory.commission * 100;
                    $scope.commission = {
                        'isopen': false,
                        'text': '固定比例',
                        'unit': '%',
                    };
                }
                //获得商品列表
                CommodityService.getCommodityListWithCategory($scope.commodityCategory.id).then(function(res) {
                    $scope.commodityList = res.commodities;
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.addCommodityCategory = function() {
                $scope.commodityCategory = {
                    'id': null,
                    'name': '',
                    'commission': null,
                };
            }

            $scope.submitCommodityCategory = function() {
                if ($scope.commission.unit == '%') {
                    $scope.commodityCategory.commission = $scope.commodityCategory.commission / 100;
                }else if ($scope.commission.unit == '') {
                    $scope.commodityCategory.commission = null;
                }
                if ($scope.commodityCategory.id) {
                    CommodityService.updateCommodityCategory($scope.commodityCategory).then(function(res) {
                        $scope.commodityCategory = null;
                        $scope.getCommodityCategoryList();
                    }, function(res) {
                        alert(res.message);
                    });
                }else {
                    CommodityService.addCommodityCategory($scope.commodityCategory).then(function(res) {
                        $scope.commodityCategory = null;
                        $scope.getCommodityCategoryList();
                    }, function(res) {
                        alert(res.message);
                    });
                }
            }

            // 添加一张图片并上传到服务器
            $scope.uploadImage = function(file, errFiles) {
                UploadService.addImage(file, errFiles).then(function(res) {
                    if (res != undefined && res != '') {
                        $scope.commodity.image.push(res);
                    }
                }, function(res) {
                    if (errFiles.length > 0)
                        alert(res);
                });
            }

            // 删除图片并从服务器删除
            $scope.$watchCollection('garbage', function(newGarbage, oldGarbage) {
                if (newGarbage.length > 0) {
                    UploadService.delImage(newGarbage[0]).then(function(res) {
                        _.remove($scope.garbage, function(n) {
                            return n == newGarbage[0];
                        });
                        _.remove($scope.oldImgList, function(n) {
                            return n == newGarbage[0];
                        });
                    }, function(res) {
                        alert(res.message);
                    });
                }
            });

            //选择规格时重置规格列表
            $scope.$watchCollection('spe_type.state', function(newVal, oldVal) {
                if (newVal == 'yes' && oldVal == 'no') {
                    if(!$scope.commodity.specifications[0].name)
                        $scope.commodity.specifications[0].name = '(默认规格)';
                    if(!$scope.commodity.specifications[0].sellable_type){
                        if ($scope.nowType == 'property') {
                            $scope.commodity.specifications[0].sellable_type = 'App\\Models\\PropertyTemplate';
                        } else if ($scope.nowType == 'ticket') {
                            $scope.commodity.specifications[0].sellable_type = 'App\\Models\\TicketTemplate';
                        }
                    }
                } else if (newVal == 'no' && oldVal == 'yes') {
                    var flag = 0;
                    if($scope.commodity && $scope.commodity.specifications.length > 1){
                        $($scope.commodity.specifications).each(function(e){
                            if($scope.commodity.specifications[e].id){
                                flag += 1;
                            }
                        });
                    }
                    if(flag > 1){
                        alert('已配置过的多规格无法修改为单一规格');
                        $scope.spe_type.state = 'yes';
                    }else {
                        var spe = $.extend(true, {}, $scope.commodity.specifications[0]);
                        spe.name = '';
                        $scope.commodity.specifications = [];
                        $scope.commodity.specifications.push(spe);
                    }
                }
            });

            //给套装添一个商品
            $scope.addSuit = function() {
                if (!$scope.suit.suit) {
                    alert('请选择一个商品');
                    return;
                }
                var data = jQuery.parseJSON($scope.suit.suit);
                if (!$scope.suit.count) {
                    alert('请输入数量');
                    return;
                }
                var suit = {
                    'id': data.id,
                    'full_name': data.full_name,
                    'count': $scope.suit.count
                }
                var index = _.findIndex($scope.suitArr, function(n) {
                    return n.id == suit.id;
                });
                if (index >= 0) {
                    alert('不能重复添加，请删除后再添加');
                } else {
                    $scope.suitArr.push(suit);
                }
            }

            //删除套装内的一个商品
            $scope.delSuitArr = function(id) {
                _.remove($scope.suitArr, function(n) {
                    return n.id == id;
                });
            }

            $scope.getCommodityList = function() {
                CommodityService.getCommodityList().then(function(res) {
                    $scope.commodityList = res.commodities;
                }, function(res) {
                    alert(res.message);
                });
                CommodityService.getCommoditySpecificationsWithoutSuit().then(function(res) {
                    $scope.commodityWithoutSuit = res.commodity_specifications;
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.getCommodity = function(id) {
                $scope.commodity = {};
                CommodityService.getCommodity(id).then(function(res) {
                    var commodity = res.commodity;
                    if (commodity.image != null) {
                        $scope.oldImgList = commodity.image;
                    }
                    if (commodity.specifications[0].sellable_type) {
                        $scope.getSellableList(commodity.specifications[0].sellable_type).then(function() {
                            $scope.setSpec();
                            $(commodity.specifications).each(function(e) {
                                var index = _.findIndex($scope.sellableList, function(o) {
                                    return o.id == commodity.specifications[e].sellable_id;
                                });
                                if (index == -1) {
                                    alert('该商品可能已过期或被删除');
                                }
                            });
                            $scope.commodity = commodity;
                            if (commodity.specifications[0].sellable_type == 'App\\Models\\TicketTemplate') {
                                $scope.selType('ticket');
                                $scope.text = "修改：" + $scope.commodity.name;
                            } else if (commodity.specifications[0].sellable_type == 'App\\Models\\PropertyTemplate') {
                                $scope.selType('property');
                                $scope.text = "修改：" + $scope.commodity.name;
                            }
                            $scope.commodity = commodity;
                            $scope.commodity.is_suite = 'no';
                            if ($scope.commodity.image == null) {
                                $scope.commodity.image = [];
                            }
                            if (commodity.specifications.length > 1) {
                                $scope.spe_type.state = 'yes';
                            }else {
                                if(commodity.specifications[0].name){
                                    $scope.spe_type.state = 'yes';
                                }else {
                                    $scope.spe_type.state = 'no';
                                }
                            }
                            for (var i = 0; i < commodity.specifications.length; i++) {
                                commodity.specifications[i].price = parseFloat($scope.commodity.specifications[i].price);
                            }
                            $scope.commodity.is_need_delivery = $scope.commodity.specifications[0].is_need_delivery;
                        });
                    } else {
                        $scope.commodity = commodity;
                        if (commodity.specifications[0].is_suite) {
                            $scope.commodity.is_suite = 'yes';
                            $scope.suitArr = $scope.commodity.suit;
                            $scope.selType('suit');
                            $scope.text = "修改：" + $scope.commodity.name;
                        } else {
                            $scope.commodity.is_suite = 'no';
                            $scope.selType('product');
                            $scope.text = "修改：" + $scope.commodity.name;
                        }
                        if ($scope.commodity.image == null) {
                            $scope.commodity.image = [];
                        }
                        if (commodity.specifications.length > 1) {
                            $scope.spe_type.state = 'yes';
                        }else {
                            if(commodity.specifications[0].name){
                                $scope.spe_type.state = 'yes';
                            }else {
                                $scope.spe_type.state = 'no';
                            }
                        }
                        for (var i = 0; i < $scope.commodity.specifications.length; i++) {
                            $scope.commodity.specifications[i].price = parseFloat($scope.commodity.specifications[i].price);
                        }
                        $scope.commodity.is_need_delivery = $scope.commodity.specifications[0].is_need_delivery;
                    }
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.submit = function(isValid) {
                if (isValid) {
                    if ($scope.nowType == 'suit') {
                        $scope.commodity.is_suite = true;
                        if ($scope.suitArr.length > 0) {
                            $scope.commodity.suit = $scope.suitArr;
                        } else {
                            alert('套装内无商品');
                            return;
                        }
                    } else if ($scope.nowType != 'suit') {
                        $scope.commodity.is_suite = false;
                        _.unset($scope.commodity, 'suit');
                    } else {
                        return null;
                    }
                    if ($scope.commodity.image.length > 0) {
                        if ($scope.commodity.image.length >= 6) {
                            alert('最多上传6张图片');
                            return;
                        }
                    } else {
                        alert('至少上传一张图片');
                        return;
                    }
                    if ($scope.spe_type.state == 'no') {
                        if ($scope.nowType == 'property') {
                            $scope.commodity.specifications[0].sellable_type = 'App\\Models\\PropertyTemplate';
                        } else if ($scope.nowType == 'ticket') {
                            $scope.commodity.specifications[0].sellable_type = 'App\\Models\\TicketTemplate';
                        }
                        $scope.commodity.specifications[0].name = '';
                        $scope.commodity.specifications[0].is_on_offer = true;
                        if (!$scope.checkSpec($scope.commodity.specifications[0])) {
                            return;
                        }
                    }
                    if ($scope.commodity.specifications == undefined || $scope.commodity.specifications.length <= 0) {
                        alert('商品规格不能为空');
                        return;
                    }
                    if ($scope.commodity.id) {
                        CommodityService.updateCommodity($scope.commodity).then(function(res) {
                            $scope.clearData();
                            $scope.commodity = {
                                'image': []
                            };
                            $scope.getCommodityCategory($scope.commodityCategory.id);
                        }, function(res) {
                            alert(res.message);
                            $scope.error = res.errors;
                        });
                    } else {
                        CommodityService.addCommodity($scope.commodity).then(function(res) {
                            $scope.clearData();
                            $scope.commodity = {
                                'image': []
                            };
                            $scope.getCommodityCategory($scope.commodityCategory.id);
                        }, function(res) {
                            alert(res.message);
                            $scope.error = res.errors;
                        });
                    }
                }
            }

            $scope.delete = function(id) {
                var del = confirm("是否要删除这个商品？有可能造成系统异常");
                if (del) {
                    CommodityService.deleteCommodity(id).then(function(res) {
                        $scope.clearData();
                        $scope.selType('product');
                    }, function(res) {
                        alert(res.message);
                    });
                }
            }

            //多规格时判断结构
            $scope.addSpecification = function() {
                if ($scope.commodity.specifications.length >= 5) {
                    alert('最多有五个规格');
                    $scope.setSpec($scope.spec.sellable_id);
                    return;
                }
                if ($scope.spe_type.state == 'yes' && ($scope.spec.name == undefined || $scope.spec.name == '')) {
                    alert('规格名不能为空');
                    return;
                }
                if (!$scope.checkSpec($scope.spec)) {
                    return;
                }
                var index = _.findIndex($scope.commodity.specifications, function(n) {
                    return n.name == $scope.spec.name;
                });
                if (index >= 0) {
                    alert('规格名不能重复');
                    return;
                }
                if ($scope.nowType == 'property') {
                    $scope.spec.sellable_type = 'App\\Models\\PropertyTemplate';
                } else if ($scope.nowType == 'ticket') {
                    $scope.spec.sellable_type = 'App\\Models\\TicketTemplate';
                }
                $scope.commodity.specifications.push($scope.spec);
                $scope.setSpec($scope.spec.sellable_id);
            }

            $scope.checkSpec = function(spec){
                if (spec.stock_quantity == undefined || spec.stock_quantity < 0) {
                    alert('库存错误');
                    return false;
                }
                if ($scope.nowType == 'property' && (spec.sellable_quantity == undefined || spec.sellable_quantity < 1)) {
                    alert('资产数量错误');
                    return false;
                }
                if ($scope.nowType == 'property' && (spec.sellable_validity_days == undefined || spec.sellable_validity_days < 1)) {
                    alert('资产效期错误');
                    return false;
                }
                if (spec.price == undefined || spec.price < 0) {
                    alert('售价不正确');
                    return false;
                }
                if (spec.bonus_require == undefined || spec.bonus_require < 0) {
                    alert('积分不正确');
                    return false;
                }
                return true;
            }

            //多规格时添加规格行
            $scope.setSpec = function(id = null) {
                $scope.spec = {
                    'name': '',
                    'price': 0,
                    'stock_quantity': 0,
                    'is_on_offer': 1,
                    'bonus_require': 0
                };
                if (id != null) {
                    $scope.spec.sellable_id = id;
                }
            }

            //多规格删除未上传的规格
            $scope.delSpec = function(spec){
                if(!spec.id){
                    _.remove($scope.commodity.specifications, function(n) {
                        return n == spec;
                    });
                }else{
                    alert("该规格不能删除！");
                }
            }

            //商品类型
            $scope.selType = function(type) {
                $scope.btn_product = '';
                $scope.btn_property = '';
                $scope.btn_ticket = '';
                $scope.btn_suit = '';
                if (!$scope.commodity.name) {
                    $scope.spe_type.state = 'no';
                }
                if (type == 'product') {
                    $scope.nowType = 'product';
                    $scope.btn_product = "active";
                    $scope.text = "新建商品";
                } else if (type == 'property') {
                    $scope.nowType = 'property';
                    $scope.spe_name = "<span>服&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;务</span>";
                    $scope.btn_property = "active";
                    $scope.text = "新建服务";
                    if (!$scope.commodity.name) {
                        $scope.getSellableList('App\\Models\\PropertyTemplate');
                    }
                } else if (type == 'ticket') {
                    $scope.nowType = 'ticket';
                    $scope.spe_name = "<span>优&nbsp;&nbsp;惠&nbsp;券</span>";
                    $scope.btn_ticket = "active";
                    $scope.text = "新建优惠券";
                    if (!$scope.commodity.name) {
                        $scope.getSellableList('App\\Models\\TicketTemplate');
                    }
                } else if (type == 'suit') {
                    $scope.nowType = 'suit';
                    $scope.btn_suit = "active";
                    $scope.text = "新建套装";
                }
            }

            $scope.changeState= function(status){
                return !status;
            }

            $scope.init = function() {
                if($scope.commodity.image == undefined){
                    $scope.commodity.image = [];
                }
                if ($scope.commodity.image.length > 0) {
                    $($scope.commodity.image).each(function(e) {
                        var index = _.findIndex($scope.oldImgList, function(n) {
                            return n == $scope.commodity.image[e];
                        });
                        if (index == -1) {
                            UploadService.delImage($scope.commodity.image[e]).then(function(res) {}, function(res) {
                                alert(res.message);
                            });
                        }
                    });
                }
                $scope.commodity = {
                    'is_on_offer': true,
                    'quota_number': 0,
                    'is_need_delivery': false,
                    'disable_coupon': false,
                    'is_suite': 'no',
                    'image': [],
                    'commodity_category_id': $scope.commodityCategory.id,
                    'specifications': [{
                        'name': '',
                        'price': 0,
                        'stock_quantity': 0,
                        'bonus_require': 0,
                        'is_on_offer': 1
                    }],
                };
                $scope.spe_type.state = 'no';
                $scope.setSpec();
                $scope.suit = {
                    'count': 1
                };
                $scope.suitArr = [];
                $scope.getCommodityList();
                $scope.error = {};
            }

            $scope.clearData = function() {
                $scope.init();
                $scope.form.commodityForm.$setPristine();
                $scope.selType($scope.nowType);
            }

            //富文本编辑器配置
            $scope.tinymceOptions = {
                resize: false,
                language: 'zh_CN',
                theme: 'modern',
                height: 400,
                width: 550,
                content_css: [
                    "/components/bootstrap/dist/css/bootstrap.min.css",
                    "/marketer/css/editer.css",
                ],
                plugins: [
                    'advlist autolink lists link image charmap hr anchor pagebreak',
                    'searchreplace visualblocks visualchars code fullscreen',
                    'insertdatetime nonbreaking save table contextmenu directionality',
                    'emoticons paste textcolor colorpicker textpattern'
                ],
                toolbar1: 'insertfile undo redo | styleselect fontsizeselect | bold italic | ' +
                    'alignleft aligncenter alignright alignjustify',
                toolbar2: 'link image imagetools | forecolor backcolor | bullist numlist outdent indent',
                fontsize_formats: '14px 18px 24px 30px 36px',
                relative_urls : false,
                remove_script_host : true,
                convert_urls : true,
                file_picker_types: 'image',
                file_picker_callback: function(callback, value, meta) {
                    if (meta.filetype == 'image') {
                        //图片上传
                        $("#textImage").trigger('click');
                        $scope.richTextUploadImage = function(file, errFiles) {
                            UploadService.addImage(file, errFiles).then(function(res) {
                                if (res != undefined && res != '') {
                                    callback(res, {
                                        alt: ''
                                    });
                                }
                            }, function(res) {
                                if (errFiles.length > 0)
                                    alert(res);
                            });
                        }
                    }
                },

            };
        }
    ]);
