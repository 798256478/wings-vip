(function () {
    'use strict';

angular.module('app', ['ui.bootstrap', 'ui.router', 'mn.keyboard','angu-complete','angu-payment'])
    .constant('urls', {
        BASE: '',
        BASE_API: '/api'
    })
    .config(['$stateProvider', '$urlRouterProvider', '$httpProvider', 'AuthServiceProvider', 
    function ($stateProvider, $urlRouterProvider, $httpProvider, AuthServiceProvider) {
        // $urlRouterProvider.otherwise("/home");
        
        // $stateProvider
        //     .state('home', {
        //         url: '/home',
        //         templateUrl: '/cashier/partials/home.html'
        //     });
            
        $httpProvider.interceptors.push(['$q', '$location', function ($q, $location) {
            return {
                'request': function (config) {
                    config.headers = config.headers || {};
                    if (AuthServiceProvider.token) {
                        config.headers.Authorization = 'Bearer ' + AuthServiceProvider.token;
                    }
                    return config;
                },
                'responseError': function (response) {
                    if (response.status === 401 || response.status === 403) {
                        // delete AuthServiceProvider.token;
                        // $location.path('/signin');
                    }
                    return $q.reject(response);
                }
            };
        }]);
    }
    ])
    .run(['$rootScope', 'AuthService', 'DesktopManager', function($rootScope, AuthService, DesktopManager) {
        
        $rootScope.AuthService = AuthService;
        $rootScope.$watch('AuthService.current_user',function(newValue, oldValue){
            if (newValue){
                DesktopManager.tryStartWork();
            }
        });
        // $rootScope.$on( "$routeChangeStart", function(event, next) {
        //     if ($localStorage.token == null) {
        //         if ( next.templateUrl === "partials/restricted.html") {
        //             $location.path("/signin");
        //         }
        //     }
        // });
        
    }]);
})();