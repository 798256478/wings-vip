/**
 * config Controller
 */
'use strict';

angular.module('app')
    .controller('ConfigController', ['$scope', '$location', 'AuthService', 'ConfigService', 'ExperimentService',
        function($scope, $location, AuthService, ConfigService, ExperimentService) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('上传配置文件');
            $scope.experiment = {
                'name': '',
                'type':'score',
            };
            $scope.state = {
                'config': false,
            }
            $scope.form.file = null;

            $scope.fileUpload = function(file, errFiles) {
                $scope.state.config = true;
                if ($scope.experiment.name) {
                    ConfigService.upload(file, errFiles, $scope.experiment).then(function(res) {
                        $scope.state.config = false;
                        if (res != undefined && res != '') {
                            $scope.experiment = {
                                'name': '',
                                'type':'score',
                            };
                            $scope.form.file = null;
                            $scope.get_experiments();
                            alert('添加成功');
                        }
                    }, function(res) {
                        $scope.state.config = false;
                        if (errFiles && errFiles.length > 0) {
                            alert(res);
                        } else {
                            alert(res.message);
                        }
                    });
                } else {
                    alert('实验名不能为空');
                }

            }
        }
    ]);
