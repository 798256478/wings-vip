/**
 * TemplateMessage Controller
 */
'use strict';

angular.module('app')
.controller('TemplateMessageController', ['$scope', '$location', 'AuthService', 'SettingService',
    function($scope, $location, AuthService, SettingService) {
        if(AuthService.current_user == null){
            $location.path("/login");
            return;
        }
        $(".header .meta .page").text('模板消息');
        $scope.message = {};
        $scope.form = {};
        $scope.key = '';

        $scope.getMessage = function(){
            SettingService.getSetting('TEMPLATE_MESSAGES').then(function(res){
                $scope.message = res;
            }, function(res){
                alert(res.message);
            });
        }

        $scope.editMessage = function(key){
            $scope.template = {};
            $scope.key = key;
            var message = $.extend(true, {}, $scope.message[key]);
            message.wechat.data_format = $scope.getString(message.wechat.data_format);
            message.sms.sms_param = $scope.getString(message.sms.sms_param);
            $scope.template = message;
        }

        $scope.saveMessage = function(){
            var message = $.extend(true, {}, $scope.template);
            message.wechat.data_format = $scope.getObj(message.wechat.data_format);
            message.sms.sms_param = $scope.getObj(message.sms.sms_param);
            $scope.message[$scope.key] = message;
            var setting = {
                'key': 'TEMPLATE_MESSAGES',
                'value': $scope.message,
            };
            SettingService.setSetting(setting).then(function(res){
                $scope.getMessage();
                $scope.template = null;
            }, function(res){
                alert(res.message);
            });
        }

        $scope.getString = function(obj){
            var str = "";
            $.each(obj, function(key, value){
                str += key + ':' + value + "\n";
            });
            return str;
        }

        $scope.getObj = function(str){
            var arrList = str.split("\n");
            var Obj = {};
            $.each(arrList, function(key, value){
                if(value != ''){
                    var arr = value.split(":");
                    if(arr.length > 2){
                        var key = arr[0];
                        var value = '';
                        for (var i = 0; i < arr.length; i++) {
                            if(i != 0){
                                value += arr[i] + ":";
                            }
                        }
                        value = value.substring(0, value.length - 1);
                        Obj[key] = value;
                    }else {
                        Obj[arr[0]] = arr[1];
                    }
                }
            });
            return Obj;
        }

        $scope.changeState = function(state){
            return !state;
        }

        $scope.getMessage();
    }
]);
