/**
 * projectdata Controller
 */
'use strict';

angular.module('app')
    .controller('ProjectDataFengxianController', ['$scope', '$location', 'AuthService', 'ProjectDataService','ExperimentService',
        function($scope, $location, AuthService, ProjectDataService,ExperimentService) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('风险评估');
            $scope.currentExperimentData = null;
            $scope.pages = {
                'nowPage': 1,
                'pageSize':15,
                'experimentId':0,
                'code':''
            };
            ExperimentService.get_experiments().then(function(res){
                $scope.experiments = res;
            },function(res){
                alert(res.message);
            });

            $scope.getList = function() {
                ProjectDataService.getExperimentDataList([4,5,6, 8], $scope.pages.code, $scope.pages.experimentId,$scope.pages.nowPage, $scope.pages.pageSize).then(function(res) {
                    $scope.experimentDataList = res.data;
                    $scope.pages.total = parseInt(res.total);
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.$watchCollection('pages.nowPage', function(newVal, oldVal) {
                $scope.getList(newVal);
            });


            $scope.getDetailById = function(data) {
                $scope.currentExperimentData=data;
                ProjectDataService.getDetailById(data.id).then(function(res) {
                    $scope.parentProjectList = res;
                }, function(res) {
                    alert(res.message);
                });
            }


            $scope.getSel = function(a) {
                if (a.project_risks[0].min != 0 || a.project_risks[0].max) {
                    angular.forEach(a.project_risks, function(value, key) {
                        if (value.min <= a.score && value.max > a.score) {
                            a.abilityLevel = value.tag;
                            a.level = value.level;
                        }
                    });
                }
            }

            $scope.saveData = function() {
                ProjectDataService.editProjectData({
                    'data':  $scope.parentProjectList,
                    'experiment_data_id': $scope.currentExperimentData.id
                }).then(function(res) {
                    $scope.pages.nowPage = 1;
                    $scope.getList();
                    $scope.parentProjectList = null;
                    $scope.currentExperimentData = null;
                    alert('保存成功');
                }, function(res) {
                    alert(res.message);
                });
            }
        }
    ]);
