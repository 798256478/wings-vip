(function () {
    'use strict';
    
    angular.module('app')
    .service('DesktopManager', ['$http', 'urls', '$q', function($http, urls, $q){
        var self = this;
        
        this.isLocked = true;
        this.startWork = false;
        
        this.lockupDesktop = function(){
            self.isLocked = true;
        }
        
        this.unlockDesktop = function(){
            self.isLocked = false;
            self.setFocus('.card-search-panel>input');
        }
        
        this.tryStartWork = function(){
            if (!self.startWork)
                self.startWork = true;
        }
        
        this.setFocus = function(select){
            angular.element(select).focus();
        }
        
    }]);
    
})();