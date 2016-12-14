'use strict';

angular.module('app', ['ui.router','ngAnimate', 'ui.bootstrap','ngCookies'])
    .constant('urls', {
        BASE: '',
        BASE_API: '/api'
    })
    .config(['$stateProvider', '$urlRouterProvider', '$httpProvider', 
        function($stateProvider, $urlRouterProvider, $httpProvider) {

            // For unmatched routes
            $urlRouterProvider.otherwise('/');

            // Application routes
            $stateProvider
                .state('reprot', {
                    url: '/',
                    templateUrl: 'partials/report.html'
                })
                .state('/reportDetail/:code/:projectId', {
                    url: '/reportDetail/:code/:projectId',
                    templateUrl: 'partials/report_detail.html'
                })
        }
    ]);

