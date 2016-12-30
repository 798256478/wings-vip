/**
 * Mass Controller
 */
'use strict';

angular.module('app')
.controller('MassController', ['$scope', '$location', 'AuthService', 'MassService', 'CommodityService', 'SettingService','$uibModal',
    function($scope, $location, AuthService, MassService, CommodityService, SettingService,$uibModal) {
        if(AuthService.current_user == null){
            $location.path("/login");
            return;
        }
        $(".header .meta .page").text('群发');
        SettingService.getSetting('CARD').then(function(res) {
            $scope.levels = res.levels
        }, function(res) {
            alert(res.message);
        });

        $scope.getMassTemplateList = function(){
            MassService.getMassTemplateList().then(function(res){
                $scope.MassTmpList = res.masses;
            },function(res){
                alert(res.message);
            });
        }

        $scope.getSendTop = function(){
            MassService.getSendTop().then(function(res){
                $scope.sendTop = res.cards;
            },function(res){
                alert(res.message);
            });
        }

        $scope.queryList = function(query){
            $scope.quering = '查找中，请稍后。。。';
            MassService.getQueryList(query).then(function(res){
                $scope.queryResult = res.cards;
                $scope.queryTotal = res.count[0].total;
                $scope.queryUnable = res.unable;
                $scope.inputarr();
                $scope.quering = '';
                $scope.getSendTop();
            },function(res){
                $scope.quering = '';
                alert(res.message);
            });
        }

        $scope.changeShow = function(state) {
            return !state;
        }

        $scope.datePicker = {
            format: 'yyyy-MM-dd',
            dateOptions: {
                formatYear: 'yyyy',
                startingDay: 1
            }
        }
        $scope.selectDate = function(state) {
            return !state;
        };

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

        $scope.inputarr = function(){
            var myarr = new Array();
            for (var i = 0,a; a= $scope.advancedQuery[i++];){
                if(a['isshow']){
                    myarr.push(a['text']);
                }
            }
            $scope.myinputarr = myarr;
        }

        $scope.items = [
            {
                'title': '模板名称',
                'name': 'tmpname',
                'type': 'text',
                'value': ''
            }
        ];

        $scope.getMassTemplate = function(id){
            MassService.getMassTemplate(id).then(function(res){
                $scope.queryList(res.mass.template);
                $scope.advancedQuery = res.mass.template;
            },function(res){
                alert(res.message);
            });
        }

        $scope.delMassTemplate = function(id){
            var del = confirm("是否要删除该模板？");
            if(del){
                MassService.delMassTemplate(id).then(function(res){
                    $scope.getMassTemplateList();
                },function(res){
                    alert(res.message);
                });
            }
        }

        $scope.getMassHistory = function(){
            MassService.getMassHistory().then(function(res){
                $scope.massHistory = res.operating_records;
            },function(res){
                alert(res.message);
            });
        }

        $scope.showHistory = function(data){
            $scope.queryList(data.query);
            $scope.advancedQuery = data.query;
            if(data.bonus)
                setValue(data.bonus, 'bonus');
            if(data.balance)
                setValue(data.bonus, 'balance');
            if(data.ticket)
                setValue(data.bonus, 'ticket');
            if (data.wechatText) {
                setValue(data.wechatText, 'wechatText');
            }
            if(data.smsText.length == 2){
                setValue(data.smsText[0], 'smsText1');
                setValue(data.smsText[1], 'smsText2');
            }
        }

        /**
         * 设置群发模板名称并保存
         * @method function
         * @return {null}
         */
        $scope.setTmpName = function(){
            var modalInstance = $uibModal.open({
                animation: false,
                templateUrl: 'modal.html',
                controller: 'ModalInstanceCtrl',
                size: 'sm',
                resolve: {
                    items: function () {
                        return $scope.items;
                    }
                }
            });

            modalInstance.result.then(function (setItems) {
                $scope.tmp = {};
                $scope.tmp.name = setItems[0].value;
                $scope.tmp.template = $scope.advancedQuery;
                MassService.saveMassTemplate($scope.tmp).then(function(res){
                    $scope.getMassTemplateList();
                },function(res){
                    alert(res.message);
                });
                $scope.tmp;
            }, function () {
            });
        }

        //发送
        $scope.sendType = function(){
            var modalInstance = $uibModal.open({
                animation: false,
                templateUrl: 'modal.html',
                controller: 'ModalInstanceCtrl',
                size: 'lg',
                resolve: {
                    items: function () {
                        return $scope.sendTmp;
                    }
                }
            });

            modalInstance.result.then(function (setItems) {
                $scope.sendVal = {};
                if(getValue(setItems, 'notice') != '')
                    $scope.sendVal.notice = getValue(setItems, 'notice');
                if(getValue(setItems, 'action') != '')
                    $scope.sendVal.jobs = getValue(setItems, 'action');
                $scope.sendVal.query = $scope.advancedQuery;
                if ($scope.queryTotal > 0 && $scope.queryUnable >= 0 && $scope.queryTotal - $scope.queryUnable > 1
                    && $scope.sendVal.wechatText != '' || $scope.sendVal.wechatText == undefined) {
                    MassService.sendMessage($scope.sendVal).then(function(res){
                        $scope.refreshMass();
                        alert("操作完成");
                    },function(res){
                        alert(res.message);
                    });
                }else {
                    alert("发送微信消息时不能只有一个人能接收消息")
                }
            }, function () {
            });
        }

        function setValue(value, name){
            var index = -1;
            index = _.findIndex($scope.sendTmp, function(s){return s.name == name});
            if(index >= 0){
                $scope.sendTmp[index].value = value;
            }
        }

        function getValue(items, name){
            var index = _.findIndex(items, function(s){return s.name == name});
            if(index >= 0){
                if(items[index].value != ''){
                    return items[index].value;
                }else{
                    return ''
                }
            }else{
                return '';
            }
        }

        $scope.init = function(){
            $scope.getMassTemplateList();
            $scope.getSendTop();
            MassService.getDefaultMass().then(function(res){
                $scope.advancedQuery = res;
            },function(res){
                alert(res.message);
            });
            $scope.sendTmp = [
                {
                    'name': 'notice',
                    'type': 'notice',
                    'value': '',
                },
                {
                    'name': 'action',
                    'type': 'action',
                    'value': '',
                },
            ];
            //群发具体内容
            CommodityService.getSellableList('App\\Models\\TicketTemplate').then(function(res){
                $scope.ticketTemplateList = res;
            },function(res){
                alert(res.messge);
            });
            $scope.getMassHistory();
        }
        $scope.init();

        $scope.refreshMass = function(){
            $scope.init();
            $scope.queryResult = [];
            $scope.queryTotal = null;
        }
    }
]);
