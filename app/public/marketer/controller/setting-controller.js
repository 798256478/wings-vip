/**
 * property Controller
 */
'use strict';

angular.module('app')
    .controller('SettingController', ['$scope', '$location', 'AuthService', 'SettingService', 'UploadService',
        function($scope, $location, AuthService, SettingService, UploadService) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('全局设置');
            $scope.settingList = {
                'TICKET': '券基础配置',
                'CARD': '会员卡配置',
                'THEME': '主题配置',
                'BALANCE': '储值赠送配置',
                'PAYMENT': '支付方式配置',
                'CASHIER_CLIENT': '收银端配置',
            };
            $scope.setting = {
                'key': '',
                'value': {},
            };
            $scope.paymentType = {
                'WECHAT': '微信支付',
                'BALANCE': '余额支付',
                'CASHIER': '现金支付',
                'BONUS': '积分支付',
            };
            $scope.defaultCardLevel = function(){
                $scope.cardLevel = {
                    'id': null,
                    'name': '',
                    'description': '',
                    'image': '',
                };
            }
            $scope.form = {};

            SettingService.getTheme().then(function(res) {
                $scope.themeList = res;
            }, function(res) {
                alert(res.message);
            });

            $scope.getSetting = function(key) {
                $scope.setting.key = '';
                $scope.setting.value = {};
                SettingService.getSetting(key).then(function(res) {
                    $scope.setting.key = key;
                    $scope.setting.value = res;
                    if (key == 'CARD') {
                        $scope.setting.value.levels = _.sortBy($scope.setting.value.levels, function(n) {
                            return n.order;
                        });
                    }
                    if (key == 'THEME') {
                        var index = _.findIndex($scope.themeList, function(n){
                            return n.key == $scope.setting.value.key;
                        });
                        if (index >= 0) {
                            $scope.setting.value.selIndex = index;
                            if (!$scope.setting.value.colors) {
                                $scope.setting.value.colors = {
                                    'THEME': $scope.themeList[index].colors.THEME.default,
                                    'HIGHLIGHT': $scope.themeList[index].colors.HIGHLIGHT.default,
                                    'PRICE': $scope.themeList[index].colors.PRICE.default,
                                    'BONUS': $scope.themeList[index].colors.BONUS.default,
                                    'BUTTON1': $scope.themeList[index].colors.BUTTON1.default,
                                    'BUTTON2': $scope.themeList[index].colors.BUTTON2.default,
                                };
                            }
                            if (!$scope.setting.value.texts) {
                                $scope.setting.value.texts = {
                                    'TICKET': $scope.themeList[index].texts.TICKET.default,
                                    'BONUS': $scope.themeList[index].texts.BONUS.default,
                                    'BLANACE': $scope.themeList[index].texts.BLANACE.default,
                                    'MEMBER': $scope.themeList[index].texts.MEMBER.default,
                                    'SLOGAN': $scope.themeList[index].texts.SLOGAN.default,
                                };
                            }
                        }else {
                            var def =_.findIndex($scope.themeList, function(n){
                                return n.key == 'default';
                            });
                            $scope.setting.value.key = 'default';
                            $scope.setting.value.selIndex = def;
                            $scope.setting.value.colors = {
                                'THEME': $scope.themeList[def].colors.THEME.default,
                                'HIGHLIGHT': $scope.themeList[def].colors.HIGHLIGHT.default,
                                'PRICE': $scope.themeList[def].colors.PRICE.default,
                                'BONUS': $scope.themeList[def].colors.BONUS.default,
                                'BUTTON1': $scope.themeList[def].colors.BUTTON1.default,
                                'BUTTON2': $scope.themeList[def].colors.BUTTON2.default,
                            };
                            $scope.setting.value.texts = {
                                'TICKET': $scope.themeList[def].texts.TICKET.default,
                                'BONUS': $scope.themeList[def].texts.BONUS.default,
                                'BLANACE': $scope.themeList[def].texts.BLANACE.default,
                                'MEMBER': $scope.themeList[def].texts.MEMBER.default,
                                'SLOGAN': $scope.themeList[def].texts.SLOGAN.default,
                            };
                        }
                    }
                    if (key == 'PAYMENT') {
                        if($scope.setting.value.bonus_rule == undefined) {
                            $scope.setting.value.bonus_rule = {
                                'disabled': false,
                                'exchange': 1,
                                'use': 1,
                                'limit': 500,
                            };
                        }
                        $scope.setting.value.bonus_rule.use = $scope.setting.value.bonus_rule.use * 100;
                    }
                }, function(res) {
                    alert(res.message);
                });
                $scope.defaultCardLevel();
            }

            $scope.submit = function(isValid) {
                if (isValid) {
                    if ($scope.setting.key == 'CARD') {
                        for (var i = 0; i < $scope.setting.value.levels.length; i++) {
                            $scope.setting.value.levels[i].order = i + 1;
                        }
                    }
                    if ($scope.setting.key == 'PAYMENT') {
                        if ($scope.setting.value.bonus_rule.use > 100 || $scope.setting.value.bonus_rule.use < 0) {
                            alert("抵用比例错误！");
                            return;
                        }
                        if ($scope.setting.value.bonus_rule.limit < 0) {
                            alert("抵用金额错误！");
                            return;
                        }
                        if ($scope.setting.value.bonus_rule.exchange < 0) {
                            alert("抵用比例错误！");
                            return;
                        }
                        $scope.setting.value.bonus_rule.use = $scope.setting.value.bonus_rule.use / 100;
                    }
                    SettingService.setSetting($scope.setting).then(function(res) {
                        $scope.setting = {};
                    }, function(res) {
                        alert(res.message);
                        $scope.error = res.errors;
                    });
                    $scope.defaultCardLevel();
                }
            }

            $scope.changeState = function(state) {
                return !state;
            }

            $scope.resetCardLevel = function(){
                if ($scope.cardLevel.id == null && $scope.cardLevel.image != '') {
                    UploadService.delImage($scope.cardLevel.image).then(function(res){
                    }, function(res){
                        alert('出现错误');
                    });
                }
                $scope.cardLevel = {
                    'id': null,
                    'name': '',
                    'description': '',
                    'image': '',
                };
            }

            $scope.uploadLogo = function(file, errFiles) {
                UploadService.addStaticImage(file, errFiles, 'logo').then(function(res) {
                    if (res != undefined && res != '') {
                        $scope.setting.value.logo = res+'?'+ (new Date()).valueOf();
                    }
                }, function(res) {
                    if (errFiles.length > 0)
                        alert(res);
                });
            }

            $scope.uploadCardImage = function(file, errFiles) {
                UploadService.addStaticImage(file, errFiles, 'card').then(function(res) {
                    if (res != undefined && res != '') {
                        $scope.cardLevel.image = res;
                    }
                }, function(res) {
                    if (errFiles.length > 0)
                        alert(res);
                });
            }

            $scope.addLevel = function() {
                if ($scope.setting.key == 'CARD') {
                    if ($scope.cardLevel.name != '') {
                        var levelList = _.sortBy($scope.setting.value.levels, function(n) {
                            return n.id;
                        });
                        var id = levelList[levelList.length - 1].id;
                        for (var i = 0; i < $scope.setting.value.levels.length; i++) {
                            $scope.setting.value.levels[i].order = i + 1;
                        }
                        var newLevel = {
                            'id': id + 1,
                            'order': $scope.setting.value.levels.length + 1,
                            'name': $scope.cardLevel.name,
                            'description': $scope.cardLevel.description,
                            'image': $scope.cardLevel.image,
                        }
                        $scope.setting.value.levels.push(newLevel);
                        $scope.defaultCardLevel();
                    }else {
                        alert('等级名称必填');
                    }
                }
            }

            $scope.editLevel = function() {
                if ($scope.setting.key == 'CARD') {
                    if ($scope.cardLevel.id == null) {
                        alert('状态错误');
                        return;
                    }
                    if ($scope.cardLevel.name == '') {
                        alert('等级名称必填');
                        return;
                    }
                    var index = _.findIndex($scope.setting.value.levels, function(n){
                        return n.id == $scope.cardLevel.id;
                    });
                    if (index >= 0) {
                        $scope.setting.value.levels[index].name = $scope.cardLevel.name;
                        $scope.setting.value.levels[index].description = $scope.cardLevel.description;
                        $scope.setting.value.levels[index].image = $scope.cardLevel.image;
                    }else {
                        alert('等级选择错误');
                    }
                    $scope.defaultCardLevel();
                }
            }

            $scope.getLevel = function(id){
                if ($scope.setting.key == 'CARD') {
                    var index = _.findIndex($scope.setting.value.levels, function(n){
                        return n.id == id;
                    });
                    if (index >= 0) {
                        $scope.cardLevel.id = $scope.setting.value.levels[index].id;
                        $scope.cardLevel.name = $scope.setting.value.levels[index].name;
                        $scope.cardLevel.description = $scope.setting.value.levels[index].description;
                        $scope.cardLevel.image = $scope.setting.value.levels[index].image;
                    }else {
                        alert('异常错误');
                    }
                }
            }

            $scope.delLevel = function(id) {
                if ($scope.setting.key == 'CARD') {
                    var del = confirm("是否要删除该等级？有可能会造成异常！");
                    if (del) {
                        _.remove($scope.setting.value.levels, function(n) {
                            return n.id == id;
                        });
                    }
                }
            }

            $scope.addBuy = function(money, give, dinge) {
                if ($scope.setting.key == 'BALANCE') {
                    if (money > 0) {
                        if (give && give > 0) {
                            $scope.setting.value.buy[money] = give / 100;
                        } else if (dinge && dinge > 0) {
                            $scope.setting.value.buy[money] = dinge;
                        }else {
                            alert("参数错误");
                        }
                    }else {
                        alert("参数错误");
                    }
                }
            }

            $scope.delBuy = function(key) {
                if ($scope.setting.key == 'BALANCE') {
                    var del = confirm("是否要删除该比例？");
                    if (del) {
                        _.unset($scope.setting.value.buy, key);
                    }
                }
            }

            $scope.addExchange = function(money, give) {
                if ($scope.setting.key == 'BALANCE') {
                    if(money > 0 && give > 0) {
                        $scope.setting.value.exchange[money] = give;
                    }else {
                        alert("参数错误");
                    }
                }
            }

            $scope.delExchange = function(key) {
                if ($scope.setting.key == 'BALANCE') {
                    var del = confirm("是否要删除该比例？");
                    if (del) {
                        _.unset($scope.setting.value.exchange, key);
                    }
                }
            }

            $scope.addPayment = function(payName, payType) {
                if ($scope.setting.key == 'PAYMENT') {
                    if (payName != undefined && payName != '' && payType == 'CASHIER') {
                        var index = _.findIndex($scope.setting.value.methods, function(n) {
                            return n.name == payName;
                        });
                        if (index > -1) {
                            alert("支付名称重复");
                            return;
                        }
                        var newPayment = {
                            'name': payName,
                            'type': payType,
                            'disabled': false,
                        }
                        $scope.setting.value.methods.push(newPayment);
                    }else {
                        alert('格式错误');
                    }
                }
            }

            $scope.delPayment = function(payment) {
                if ($scope.setting.key == 'PAYMENT') {
                    var del = confirm("是否要删除该支付方式？");
                    if (del) {
                        _.unset($scope.setting.value.balance.methods, payment.name);
                        _.unset($scope.setting.value.consume.methods, payment.name);
                        _.unset($scope.setting.value.commodities.methods, payment.name);
                        _.remove($scope.setting.value.methods, function(n) {
                            return n == payment;
                        });
                    }
                }
            }

            $scope.addBalance = function(payment, proportion) {
                if ($scope.setting.key == 'PAYMENT') {
                    if (payment != '' && proportion >= 0) {
                        $scope.setting.value.balance.methods[payment] = proportion;
                    }else {
                        alert('参数错误');
                    }
                }
            }

            $scope.delBalance = function(key) {
                if ($scope.setting.key == 'PAYMENT') {
                    var del = confirm("是否要删除该支付方式？");
                    if (del) {
                        _.unset($scope.setting.value.balance.methods, key);
                    }
                }
            }

            $scope.addConsume = function(payment, proportion) {
                if ($scope.setting.key == 'PAYMENT') {
                    if (payment != '' && proportion >= 0) {
                        $scope.setting.value.consume.methods[payment] = proportion;
                    }else {
                        alert('参数错误');
                    }
                }
            }

            $scope.delConsume = function(key) {
                if ($scope.setting.key == 'PAYMENT') {
                    var del = confirm("是否要删除该支付方式？");
                    if (del) {
                        _.unset($scope.setting.value.consume.methods, key);
                    }
                }
            }

            $scope.addCommodities = function(payment, proportion) {
                if ($scope.setting.key == 'PAYMENT') {
                    if (payment != '' && proportion >= 0) {
                        $scope.setting.value.commodities.methods[payment] = proportion;
                    }else {
                        alert('参数错误');
                    }
                }
            }

            $scope.delCommodities = function(key) {
                if ($scope.setting.key == 'PAYMENT') {
                    var del = confirm("是否要删除该支付方式？");
                    if (del) {
                        _.unset($scope.setting.value.commodities.methods, key);
                    }
                }
            }

            $scope.setTheme = function(key) {
                if ($scope.setting.key == 'THEME') {
                    var index =_.findIndex($scope.themeList, function(n){
                        return n.key == key;
                    });
                    $scope.setting.value.key = key;
                    $scope.setting.value.selIndex = def;
                    $scope.setting.value.colors = {
                        'THEME': $scope.themeList[index].colors.THEME.default,
                        'HIGHLIGHT': $scope.themeList[index].colors.HIGHLIGHT.default,
                        'PRICE': $scope.themeList[index].colors.PRICE.default,
                        'BONUS': $scope.themeList[index].colors.BONUS.default,
                        'BUTTON1': $scope.themeList[index].colors.BUTTON1.default,
                        'BUTTON2': $scope.themeList[index].colors.BUTTON2.default,
                    };
                    $scope.setting.value.texts = {
                        'TICKET': $scope.themeList[index].texts.TICKET.default,
                        'BONUS': $scope.themeList[index].texts.BONUS.default,
                        'BLANACE': $scope.themeList[index].texts.BLANACE.default,
                        'MEMBER': $scope.themeList[index].texts.MEMBER.default,
                        'SLOGAN': $scope.themeList[index].texts.SLOGAN.default,
                    };
                }
            }

            $scope.clearData = function() {
                $scope.setting = {};
                $scope.form.settingForm.$setPristine();
            }
        }
    ]);
