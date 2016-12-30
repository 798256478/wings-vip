/**
 * Customer Controller
 */
'use strict';

angular.module('app')
    .controller('UserController', ['$scope', '$location', 'AuthService', 'UserService',
        function($scope, $location, AuthService, UserService) {
            if (AuthService.current_user == null) {
                $location.path("/login");
                return;
            }
            $(".header .meta .page").text('用户');
            $scope.user = {};
            $scope.form = {};

            $scope.getUserList = function() {
                UserService.getUsers().then(function(res) {
                    $scope.userList = res.users;
                }, function(res) {
                    $scope.error = res.message;
                });
            }
            $scope.getUserList();

            $scope.getUser = function(userid) {
                UserService.getUser(userid).then(function(res) {
                    $scope.user = res.user;
                    $scope.selected = true;
                    if (res.user.id == AuthService.current_user.id) {
                        $scope.nowType = true;
                    } else {
                        $scope.nowType = false;
                    }
                }, function(res) {
                    alert(res.message);
                });
            }

            $scope.submit = function(isValid) {
                if (isValid) {
                    if ($scope.user.id) {
                        UserService.updateUser($scope.user).then(function(res) {
                            alert("修改成功！");
                            $scope.getUserList();
                            $scope.getUser(res);
                        }, function(res) {
                            alert(res.message);
                            $scope.error = res.errors;
                        });
                    }else{
                        UserService.addUser($scope.user).then(function(res) {
                            alert("添加成功！");
                            $scope.getUserList();
                            $scope.getUser(res);
                        }, function(res) {
                            alert(res.message);
                            $scope.error = res.errors;
                        });
                    }
                }
            }

            $scope.delete = function(id) {
                var del = confirm("是否要删除该用户？");
                if (del) {
                    UserService.deleteUser(id).then(function(res) {
                        $scope.clearData();
                    }, function(res) {
                        alert(res.message);
                    });
                }
            }

            $scope.clearData = function() {
                $scope.user = {};
                $scope.getUserList();
                $scope.selected = false;
                $scope.error = {};
                $scope.form.userForm.$setPristine();
            }
        }
    ]);
