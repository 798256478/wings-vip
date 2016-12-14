'use strict';

angular.module('app', ['ui.router', 'ui.bootstrap', 'ngAnimate', 'ngCookies', 'oitozero.ngSweetAlert',
        'ngFileUpload', 'angular-sortable-view', 'ui.tinymce', 'bw.paging', 'ngSanitize', "kendo.directives"
    ])
    .constant('urls', {
        BASE: '',
        BASE_API: '/api'
    })
    .config(['$stateProvider', '$urlRouterProvider', '$httpProvider','$compileProvider', 'AuthServiceProvider',
        function($stateProvider, $urlRouterProvider, $httpProvider,$compileProvider, AuthServiceProvider) {

            // For unmatched routes
            $urlRouterProvider.otherwise('/');
            $compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|chrome-extension):/);//链接出现unsafe的问题

            // Application routes
            $stateProvider
                .state('index', {
                    url: '/',
                    templateUrl: 'partials/dashboard.html'
                })
                .state('login', {
                    url: '/login',
                    templateUrl: 'partials/login.html'
                })
                .state('customer', {
                    url: '/customer',
                    templateUrl: 'partials/customer.html'
                })
                .state('ticket_template', {
                    url: '/ticket_template',
                    templateUrl: 'partials/ticket_template.html'
                })
                .state('mass', {
                    url: '/mass',
                    templateUrl: 'partials/mass.html'
                })
                .state('propertytemplate', {
                    url: '/propertytemplate',
                    templateUrl: 'partials/property_template.html'
                })
                .state('commodity', {
                    url: '/commodity',
                    templateUrl: 'partials/commodity.html'
                })
                .state('event', {
                    url: '/event',
                    templateUrl: 'partials/event.html',
                    params:{
                        rule:null
                    }
                })
                .state('order', {
                    url: '/order',
                    templateUrl: 'partials/order.html'
                })
                .state('order_details', {
                    url: '/order_details/:id',
                    templateUrl: 'partials/order_details.html'
                })
                .state('deliver',{
                    url:'/deliver',
                    templateUrl:'partials/deliver.html',
                    params:{
                        id:null
                    }
                })
                .state('refund', {
                    url: '/refund',
                    templateUrl: 'partials/refund.html'
                })
                .state('deal_refund', {
                    url: '/deal_refund/:id',
                    templateUrl: 'partials/deal_refund.html'
                })
                .state('shop_config', {
                    url: '/shop_config',
                    templateUrl: 'partials/shop_config.html'
                })

                .state('setting', {
                    url: '/setting',
                    templateUrl: 'partials/setting.html'
                })
                .state('user', {
                    url: '/user',
                    templateUrl: 'partials/user.html'
                })
                .state('templatemessage', {
                    url: '/templatemessage',
                    templateUrl: 'partials/template_message.html'
                })
                .state('redeemcode', {
                    url: '/redeemcode',
                    templateUrl: 'partials/redeemCode.html'
                })
                .state('redeemcodestatistics', {
                    url: '/redeemcodestatistics',
                    templateUrl: 'partials/redeemCode_statistics.html'
                })
                .state('statistics', {
                    url: '/statistics',
                    templateUrl: 'partials/statistics.html'
                })
                .state('commodities_statistics', {
                    url: '/commodities_statistics',
                    templateUrl: 'partials/commodities_statistics.html'
                })
                .state('commodity_statistics_details/:id', {
                    url: '/commodity_statistics_details/:id',
                    templateUrl: 'partials/commodity_statistics_details.html'
                })
                .state('login_records', {
                    url: '/login_records',
                    templateUrl: 'partials/login_records.html'
                })
                .state('operating_records', {
                    url: '/operating_records',
                    templateUrl: 'partials/operating_records.html'
                })
                .state('backup', {
                    url: '/backup',
                    templateUrl: 'partials/backup.html'
                })
                .state('gene', {
                    url: '/gene',
                    templateUrl: 'health/gene.html'
                })
                .state('sitedata', {
                    url: '/sitedata',
                    templateUrl: 'health/sitedata.html'
                })
                .state('projectdata', {
                    url: '/projectdata',
                    templateUrl: 'health/projectdata.html'
                })
                .state('riskdata', {
                    url: '/riskdata',
                    templateUrl: 'health/riskdata.html'
                })
                .state('experiment', {
                    url: '/experiment',
                    templateUrl: 'health/experiment.html'
                })
                .state('tables', {
                    url: '/tables',
                    templateUrl: 'partials/tables.html'
                })
                .state('questionnaire', {
                    url: '/questionnaire',
                    templateUrl: 'health/questionnaire.html'
                })
                .state('barcode', {
                    url: '/barcode',
                    templateUrl: 'health/barcode.html'
                })
                .state('health_customer', {
                    url: '/health_customer',
                    templateUrl: 'health/health_customer.html'
                })
                .state('reservation', {
                    url: '/reservation',
                    templateUrl: 'health/reservation.html'
                })
                .state('sync', {
                    url: '/sync',
                    templateUrl: 'yuda/sync.html'
                });

            $httpProvider.interceptors.push(['$q', '$location', function($q, $location) {
                return {
                    'request': function(config) {
                        config.headers = config.headers || {};
                        if (AuthServiceProvider.token) {
                            config.headers.Authorization = 'Bearer ' + AuthServiceProvider.token;
                        }
                        return config;
                    },
                    'responseError': function(response) {
                        return $q.reject(response);
                    }
                };
            }]);
        }
    ])
    .run(['$rootScope', '$location', 'AuthService', function($rootScope, $location, AuthService) {
        if (AuthService.current_user == null) {
            $location.path("/login");
        } else {
            $location.path("/");
        }
    }])
    .controller('ModalInstanceCtrl', ['$scope', '$uibModalInstance', 'items', 'UploadService',
        function($scope, $uibModalInstance, items, UploadService) {
            $scope.items = items;
            if (!$scope.rule) {
                $scope.massOpen = true;
                $scope.rule = {
                    'jobs': []
                };
            }
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
            $scope.ok = function() {
                var index = _.findIndex($scope.items, function(n) {
                    return n.type == 'action';
                });
                if (index > -1) {
                    $scope.items[index].value = $scope.rule.jobs;
                }
                var index1 = _.findIndex($scope.items, function(n) {
                    return n.type == 'notice';
                });
                if (index1 > -1) {
                    $scope.items[index1].value = $scope.notice;
                }
                $uibModalInstance.close($scope.items);
            };

            $scope.cancel = function() {
                $uibModalInstance.dismiss('cancel');
            };

            $scope.listSelect = function(name) {
                var index1 = _.findIndex($scope.items, function(n) {
                    return n.type == "list";
                });
                var index2 = _.findIndex($scope.items, function(n) {
                    return n.type == "image";
                });
                $scope.selectKey = name;
                $scope.items[index1].value = name;
                if($scope.items[index2].value != ''){
                    $scope.delImg($scope.items[index2].value);
                    $scope.items[index2].value = '';
                }
            }

            $scope.uploadImage = function(file, errFiles) {
                $scope.selectKey = '';
                var index1 = _.findIndex($scope.items, function(n) {
                    return n.type == "list";
                });
                var index2 = _.findIndex($scope.items, function(n) {
                    return n.type == "image";
                });
                UploadService.addIcon(file, errFiles).then(function(res) {
                    if (res != undefined && res != '') {
                        $scope.items[index2].value = res;
                        $scope.items[index1].value = '';
                    }
                }, function(res) {
                    if (errFiles.length > 0)
                        alert(res);
                });
            }

            $scope.delImg = function(img) {
                if (img.length > 0) {
                    UploadService.delImage(img).then(function(res) {
                        var index = _.findIndex($scope.items, function(n) {
                            return n.type == "image";
                        });
                        $scope.items[index].value = '';
                    }, function(res) {});
                }
            }
        }
    ])
    .directive('onFinishRender', function($timeout) {
        return {
            restrict: 'A',
            link: function(scope, element, attr) {
                if (scope.$last === true) {
                    $(".widget .widget-body.auto").css({
                        "height": $("html").height() - 145,
                        "overflow-y": "auto"
                    });
                    $(".widget .widget-body.auto-nohead").css({
                        "height": $("html").height() - 95,
                        "overflow-y": "auto"
                    });
                    $(".widget .widget-body.auto-height").css({
                        "height": $("html").height() - 145,
                    });
                    $(".widget .widget-body.auto-height-part").css({
                        "height": $("html").height() - 335,
                        "overflow-y": "auto"
                    });
                    $(".tab-content > .tab-pane, .pill-content > .pill-pane ").css({
                        "height": $("html").height() - 145,
                        "overflow-y": "auto"
                    });
                    scope.$watch(function(){
                       return $("html").height();
                    }, function(value) {
                        $(".widget .widget-body.large").css({
                            "height": value - 568,
                            "overflow-y": "auto"
                        });
                   });
                }
            }
        }
    }).directive('textTransform', function() {
        var transformConfig = {
            uppercase: function(input) {
                return input.toUpperCase();
            },
            capitalize: function(input) {
                return input.replace(
                    /([a-zA-Z])([a-zA-Z]*)/gi,
                    function(matched, $1, $2) {
                        return $1.toUpperCase() + $2;
                    });
            },
            lowercase: function(input) {
                return input.toLowerCase();
            }
        };
        return {
            require: 'ngModel',
            link: function(scope, element, iAttrs, modelCtrl) {
                var transform = transformConfig[iAttrs.textTransform];
                if (transform) {
                    modelCtrl.$parsers.push(function(input) {
                        return transform(input || "");
                    });

                    element.css("text-transform", iAttrs.textTransform);
                }
            }
        };
    });
