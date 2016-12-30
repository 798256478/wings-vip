/**
 * action Controller
 */
'use strict';

angular.module('app').controller('NoticeController', ['$scope', 'UploadService',
    function($scope, UploadService) {
        if (!$scope.notice) {
            $scope.notice = {
                'APP': {
                    'status': true,
                    'image': '',
                    'content': '',
                    'expires_date': '',
                },
                'SMS': {
                    'status': false,
                    'content1': '',
                    'content2': '',
                },
                'WECHAT': {
                    'status': false,
                    'sendtype': '文本消息',
                    'content': '',
                    'placeholder': '发送文本消息',
                },
            };
        }

        $scope.status = {
            isopen: false
        };

        $scope.datePicker = {
            format: 'yyyy-MM-dd',
            dateOptions: {
                formatYear: 'yyyy',
                startingDay: 1
            },
            'isopen': false,
        }

        $scope.changeState = function(status) {
            return !status;
        }

        $scope.selectWechatType = function(wechatType) {
            $scope.notice.WECHAT.sendtype = wechatType;
            $scope.notice.WECHAT.content = '';
            if (wechatType == '文本消息') {
                $scope.notice.WECHAT.placeholder = '发送文本消息';
            } else if (wechatType == '图文消息') {
                $scope.notice.WECHAT.placeholder = '输入微信图文消息的ID';
            }
        }

        $scope.uploadNoticeImage = function(file, errFiles) {
            UploadService.addIcon(file, errFiles).then(function(res) {
                if (res != undefined && res != '') {
                    $scope.notice.notice.image = res;
                }
            }, function(res) {
                if (errFiles.length > 0)
                    alert(res);
            });
        }

        $scope.delNoticeImg = function(img) {
            if (img.length > 0) {
                UploadService.delImage(img).then(function(res) {
                    $scope.notice.notice.image = '';
                }, function(res) {});
            }
        }
    }
]);
