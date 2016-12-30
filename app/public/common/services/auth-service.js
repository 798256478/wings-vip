(function () {
    'use strict';
    
    angular.module('app')
    .provider('AuthService', function(){
        var provider = this;
        
        this.token = '';

        this.$get = ['$http', 'urls', '$q', function($http, urls, $q){
            var self = {};
            self.logined_users = [];
            self.current_user = null;
            
            self.login = function(data) {
                var deferred = $q.defer();
                $http.post(urls.BASE_API + '/login', data).success(function(res){
                    successAuth(res);
                    deferred.resolve(res);
                }).error(function(res){
                    deferred.reject(res);
                });
                return deferred.promise;
            }
            
            
            self.editPassword = function(data) {
                var deferred = $q.defer();
                $http.post(urls.BASE_API + '/editPassword', data).success(function(res){
                    successAuth(res);
                    deferred.resolve(res);
                }).error(function(res){
                    deferred.reject(res);
                });
                return deferred.promise;
            }
            
            
            self.logout = function(id){
                if (self.current_user && self.current_user.id == id){
                    self.clearCurrent();
                }
                self.logined_users = self.logined_users.filter(function(value){
                    return value.id != id;
                    });
            }
            
            self.setPin = function(pin){
                var index = _.findIndex(self.logined_users, {'pin': pin});
                if (index == -1){
                    self.current_user.pin = pin;
                    return true;
                }
                return false;
            }
            
            self.clearCurrent = function(){
                provider.token = null;
                self.current_user = null;
            }
            
            self.switchAccount = function(pin) {
                var index = _.findIndex(self.logined_users, {'pin': pin});
                if (index == -1)
                    return false;
                else{
                    self.current_user = self.logined_users[index];
                    provider.token = self.current_user.token;
                    return true;
                }
            }
            
            function successAuth(res) {
                var user = res.user;
                
                self.current_user = user;
                provider.token = user.token;
                var myDate = new Date();
                self.current_user.logintime=myDate.getHours()+':'+myDate.getMinutes()+':'+myDate.getSeconds();
                var index = _.findIndex(self.logined_users, {'id': user.id});
                if (index == -1){
                    self.logined_users.push(self.current_user);
                }
                else{
                    self.logined_users[index] = self.current_user;
                }
            }
            
            function urlBase64Decode(str) {
                var output = str.replace('-', '+').replace('_', '/');
                switch (output.length % 4) {
                    case 0:
                        break;
                    case 2:
                        output += '==';
                        break;
                    case 3:
                        output += '=';
                        break;
                    default:
                        throw 'Illegal base64url string!';
                }
                return window.atob(output);
            }

            function getClaimsFromToken() {
                var user = {};
                if (typeof provider.token !== 'undefined') {
                    var encoded = provider.token.split('.')[1];
                    user = JSON.parse(urlBase64Decode(encoded));
                }
                return user;
            }
            
            return self;
        }];
        
    });
    
})();