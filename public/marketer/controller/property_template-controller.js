/**
 * property Controller
 */
'use strict';

angular.module('app')
    .controller('PropertyTemplateController', ['$scope', '$location', 'AuthService', 'PropertyTemplateService', '$uibModal',
        function($scope, $location, AuthService, PropertyTemplateService, $uibModal) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('服务');
            $scope.propertyTemplate = {};
            $scope.form = {};
            $scope.detailText = "新增服务";

            PropertyTemplateService.getIcons().then(function(res) {
                $scope.icons = res;
            }, function(res) {
                alert(res.message);
            });

            $scope.getPropertyTemplateList = function() {
                PropertyTemplateService.getPropertyTemplateList().then(function(res) {
                    $scope.propertyTemplateList = res.property_templates;
                }, function(res) {
                    alert(res.message);
                });
            }
            $scope.getPropertyTemplateList();

            $scope.getPropertyTemplate = function(id) {
                PropertyTemplateService.getPropertyTemplate(id).then(function(res) {
                    $scope.propertyTemplate = res.property_template;
                    $scope.detailText = "修改:" + $scope.propertyTemplate.title;
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.getIcons = function() {
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
                    if(setItems[index1].value != ''){
                        $scope.propertyTemplate.icon = setItems[index1].value;
                        $scope.propertyTemplate.image_icon = '';
                    }else if(setItems[index2].value != ''){
                        $scope.propertyTemplate.icon =  '';
                        $scope.propertyTemplate.image_icon = setItems[index2].value;
                    }else {
                        $scope.propertyTemplate.icon =  '';
                        $scope.propertyTemplate.image_icon = '';
                    }
                }, function() {
                    if ($scope.propertyTemplate.icon == undefined) {
                        $scope.propertyTemplate.icon =  '';
                    }
                    if ($scope.propertyTemplate.image_icon == undefined) {
                        $scope.propertyTemplate.image_icon = '';
                    }
                });
            }

            $scope.submit = function(isValid) {
                if (isValid) {
                    if (($scope.propertyTemplate.image_icon == '' || $scope.propertyTemplate.image_icon == undefined) &&
                        ($scope.propertyTemplate.icon == '' || $scope.propertyTemplate.icon == undefined)) {
                        alert('图标不能为空');
                        return;
                    }
                    if ($scope.propertyTemplate.id) {
                        PropertyTemplateService.updatePropertyTemplate($scope.propertyTemplate).then(function(res) {
                            alert("修改成功");
                            $scope.clearData();
                            $scope.getPropertyTemplate(res);
                        }, function(res) {
                            alert(res.message);
                            $scope.error = res.errors;
                        });
                    } else {
                        PropertyTemplateService.addPropertyTemplate($scope.propertyTemplate).then(function(res) {
                            alert("添加成功");
                            $scope.clearData();
                            $scope.getPropertyTemplate(res);
                        }, function(res) {
                            alert(res.message);
                            $scope.error = res.errors;
                        });
                    }
                }
            }

            $scope.delete = function(id) {
                var del = confirm("是否要删除该服务？有可能造成系统异常");
                if (del) {
                    PropertyTemplateService.deletePropertyTemplate(id).then(function(res) {
                        $scope.clearData();
                    }, function(res) {
                        alert(res.message);
                    });
                }
            }

            $scope.clearData = function() {
                $scope.propertyTemplate = {};
                $scope.getPropertyTemplateList();
                $scope.form.propertyForm.$setPristine();
                $scope.detailText = "新增服务";
            }
        }
    ]);
