/**
 * sitedata Controller
 */
'use strict';

angular.module('app')
    .controller('SiteDataController', ['$scope', '$location', 'AuthService', 'SiteDataService','ExperimentService',
        function($scope, $location, AuthService, SiteDataService,ExperimentService) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('上传检测数据');
            $scope.error = {};
            $scope.state = {
                'data': false,
                'edit': false,
            };
            $scope.form = {
                'inspector': '',
                'assessor': '',
                'time': '',
                'file': undefined,
                'experimentId': 1
            };
            $scope.datePicker = {
                format: 'yyyy-MM-dd',
                minDate: new Date(),
                dateOptions: {
                    formatYear: 'yyyy',
                    startingDay: 1
                }
            };
            $scope.timeDate = {
                opened: false
            };
            $scope.selectDate = function() {
                $scope.timeDate.opened = true;
            };
            ExperimentService.get_experiments().then(function(res) {
                $scope.experimentList = res;
                if (res.length > 0) {
                    $scope.form.experimentId = $scope.experimentList[0].id;
                }
            }, function(res) {
                alert(res.message);
            });

            $scope.uploadData = function(file, errFiles) {
                $scope.state.data = true;
                SiteDataService.uploadSiteData(file, errFiles, $scope.form).then(function(res) {
                    $scope.state.data = false;
                    if (res != undefined && res != '') {
                        if (res.data) {
                            $scope.error.data = res.data;
                            $scope.error.form = res.form;
                        } else {
                            $scope.form = {
                                'inspector': '',
                                'assessor': '',
                                'time': '',
                                'file': undefined,
                                'experimentId':$scope.experimentList[0].id
                            };
                            alert('保存成功');
                        }
                    }
                }, function(res) {
                    $scope.state.data = false;
                    if (errFiles && errFiles.length > 0) {
                        alert(res);
                    } else {
                        alert(res.message);
                    }
                });
            }

            $scope.saveData = function() {
                $scope.state.edit = true;
                angular.forEach($scope.error.data, function(value, key) {
                    value.data = _.union(value.data, value.empty);
                });
                SiteDataService.editSiteData({
                    'data': $scope.error.data,
                    'form': $scope.error.form
                }).then(function(res) {
                    $scope.state.edit = false;
                    $scope.form = {
                        'inspector': '',
                        'assessor': '',
                        'time': '',
                        'file': undefined,
                        'experimentId':$scope.experimentList[0].id
                    };
                    $scope.error = {};
                    alert('保存成功');
                }, function(res) {
                    $scope.state.edit = false;
                    alert(res.message);
                });
            }
        }
    ]);
