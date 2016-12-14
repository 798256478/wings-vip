/**
 * projectdata Controller
 */
'use strict';

angular.module('app')
    .controller('RiskDataController', ['$scope', '$location', 'AuthService', 'ProjectDataService','ExperimentService',
        function($scope, $location, AuthService, ProjectDataService,ExperimentService) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('健康评估');
            $scope.currentExperimentData = null;
            $scope.pages = {
                'nowPage': 1,
                'pageSize':15,
                'experimentId':0,
                'code':''
            };

            $scope.getList = function() {
                ProjectDataService.getExperimentDataList([8, 5], $scope.pages.code, $scope.pages.experimentId,$scope.pages.nowPage, $scope.pages.pageSize).then(function(res) {
                   $scope.experimentDataList = res.data;
                   $scope.pages.total = parseInt(res.total);
                }, function(res) {
                    alert(res.message);
                });
            }
            

            $scope.$watchCollection('pages.nowPage', function(newVal, oldVal) {
                $scope.getList(newVal);
            });

            $scope.getRiskById = function(data) {
                $scope.currentExperimentData=data;
                ProjectDataService.getRiskById(data.id).then(function(res) {
                    $scope.projectList = res;
                }, function(res) {
                    alert(res.message);
                });
            }


            $scope.getSel = function(project) {
                if (project.circumRisks[0].min != 0 || project.circumRisks[0].max) {
                    angular.forEach(project.circumRisks, function(circumRisk, key) {
                        if (circumRisk.min <= Number(project.riskdata.total_score) && circumRisk.max > Number(project.riskdata.total_score)) {
                            project.riskdata.abilityLevel = circumRisk.tag;
                            project.riskdata.level = circumRisk.level;
                        }
                    });
                }
            }

            $scope.saveData = function() {
                ProjectDataService.editRiskData({
                    'data':  $scope.projectList,
                    'experiment_data_id':  $scope.currentExperimentData.id
                }).then(function(res) {
                    $scope.pages.nowPage = 1;
                    $scope.getList($scope.pages.nowPage);
                    alert('保存成功');
                    $scope.projectList = null;
                    $scope.currentExperimentData = null;
                }, function(res) {
                    alert(res.message);
                });
            }
        }
    ]);
