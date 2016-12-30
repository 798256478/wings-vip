/**
 * Login Controller
 */
'use strict';
angular.module('app')
    .controller('LoginController', ['$scope', '$http', '$cookieStore', '$location', 'AuthService',
        function($scope, $http, $cookieStore, $location, AuthService) {
            $(".header .meta .page").text('登陆');
            /**
             * 登录
             * @method function
             * @return {null}
             */
            $scope.login = function(){
                var formData = {
                    login_name: $scope.login_fields.login_name,
                    password: $scope.login_fields.password
                };

                AuthService.login(formData).then(function(result){
                    $location.path("/");
                },
                function(result){
                    alert(result.message);
                });
            }
        }
]);
