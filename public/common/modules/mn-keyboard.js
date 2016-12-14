(function () {
'use strict';
    
angular.module('mn.keyboard',[])
    .service('MnkService',['$rootScope',MnkService])
    .directive('mnkInput', ['MnkService','$window',mnkInput])
    .directive('mnkDiv', ['MnkService','$window',mnkDiv])
    .directive('mnkKeyboard', ['MnkService',mnkKeyboard])
    .directive('mnkKeyGroup', ['MnkService',mnkKeyGroup])
    .directive('mnkKey', ['MnkService',mnkKey])
    .directive('mnkShortcutGroup', ['MnkService',mnkShortcutGroup])
    .directive('mnkShortcut', ['MnkService',mnkShortcut]);
    
var isPC = IsPC();
var eventName = isPC ? 'click' : 'touchend';

function MnkService($rootScope){
    var self = this;
    
    var currentInput = null;
    var keyboard;
    
    var keyGroups = {};
    var defaultKeyGroup = null;
    var showedKeyGroup = null;
    
    var shortcutGroups = {};
    var defaultShortcutGroup = null;
    var notFocusShortcutGroup = null;
    var showedShortcutGroup = null;
    
    function clearKeyboard(){
        if (showedKeyGroup) {
            showedKeyGroup.hide();
            showedKeyGroup = null;
        }
        
        if (showedShortcutGroup){
            showedShortcutGroup.hide();
            showedShortcutGroup = null;
        }
        
        if (notFocusShortcutGroup){
            notFocusShortcutGroup.show();
            showedShortcutGroup = notFocusShortcutGroup;
        }
        
    }
    
    function buildKeyboard(){
        
        if (showedKeyGroup) {
            showedKeyGroup.hide();
            showedKeyGroup = null;
        }
        
        if (showedShortcutGroup){
            showedShortcutGroup.hide();
            showedShortcutGroup = null;
        }
        
        if (currentInput.keyGroup && keyGroups[currentInput.keyGroup]){
            showedKeyGroup = keyGroups[currentInput.keyGroup];
            showedKeyGroup.show();
        }else if(defaultKeyGroup){
            showedKeyGroup = defaultKeyGroup;
            showedKeyGroup.show();
        }
        
        if (currentInput.shortcutGroup && shortcutGroups[currentInput.shortcutGroup]){
            showedShortcutGroup = shortcutGroups[currentInput.shortcutGroup];
            showedShortcutGroup.show();
        }else if(defaultShortcutGroup){
            showedShortcutGroup = defaultShortcutGroup;
            showedShortcutGroup.show();
        }
        
    }
    
    
    this.setKeyboard = function (element, attrs){
        keyboard = element;
    }
    
    this.pushKeyGroup = function (element, attrs){
        element.hide();
        
        if (attrs.mnkKeyGroup){
            keyGroups[attrs.mnkKeyGroup] = element;
            
            if (attrs.hasOwnProperty('mnkDefault'))
                defaultKeyGroup = element;
            
            if (currentInput)
                buildKeyboard();
        }
    }
    
    this.pushShortcutGroup = function (element, attrs){
        element.hide();
        
        if (attrs.mnkShortcutGroup){
            shortcutGroups[attrs.mnkShortcutGroup] = element;
            
            if (attrs.hasOwnProperty('mnkDefault'))
                defaultShortcutGroup = element;
                
            if (attrs.hasOwnProperty('mnkNotFocus'))
                notFocusShortcutGroup = element;
            
            if (currentInput)
                buildKeyboard();
        }
    }
    
    this.setCurrentInput = function(scope, element, attrs){
        currentInput = {
            scope:scope,
            element:element,
            //modelName:attrs.ngModel,
            keyGroup: attrs.mnkInput,
            shortcutGroup: attrs.shortcutGroup
        };
        buildKeyboard();
    }
    
    this.clearCurrentInput = function(){
        currentInput = null;
        clearKeyboard();
    }
    
    this.enterKey = function(key){
        // if (currentInput.modelName){
        //     $rootScope.$apply(function(){
        //         eval('currentInput.scope.' + currentInput.modelName + '+=' + key);
        //     });
        // }
        
        if (currentInput && currentInput.element){
            if (key == 'CLEAR')
                currentInput.element.val('');
            else
                currentInput.element.val(currentInput.element.val() + key);
            
            currentInput.element.trigger('change');
        }
    }
    
    this.enterShortcut = function(shortcut){
        if (shortcut && currentInput && currentInput.scope){
            
            //暂存执行Scope，清除当前Input，再执行
            var excuScope = currentInput.scope;
            //self.clearCurrentInput();
            excuScope.$apply(shortcut);
            
        }
    }
    
    
}

function mnkInput(MnkService, $window) {
    return {
        restrice : 'A',
        link : function (scope, element, attrs){
            
            if (!isPC){
                element.attr('readonly', 'readonly');
                element.css({
                    'background-color' : '#fff',
                    'cursor' : 'text'
                })
            }
           
            if (element.is(":focus")){
                MnkService.setCurrentInput(scope, element, attrs);
            }
                
            element.on('focus', function(){
                MnkService.setCurrentInput(scope, element, attrs);
            });
            element.on('blur', function(){
                if (event.relatedTarget&&!event.relatedTarget.hasAttribute('mnk-key')
                    && !event.relatedTarget.hasAttribute('mnk-shortcut')){
                    MnkService.clearCurrentInput();
                }
                else{
                    event.currentTarget.focus();
                }
            });
            
            if (attrs.hasOwnProperty('mnkAutofocus'))
                element.focus();
        }
    }
}

function mnkDiv(MnkService, $window) {
    return {
        restrice : 'A',
        link : function (scope, element, attrs){
            element.on('click', function(){
                MnkService.setCurrentInput(scope, element, attrs);
            });
            
        }
    }
}

function mnkKey(MnkService) {
    return {
        restrice : 'A',
        link : function (scope, element, attrs){
            element.on (eventName, function(){
                MnkService.enterKey(attrs.mnkKey);
            })
        }
    }
}

function mnkShortcut(MnkService) {
    return {
        restrice : 'A',
        link : function (scope, element, attrs){
            element.on (eventName, function(){
                MnkService.enterShortcut(attrs.mnkShortcut);
            })
        }
    }
}

function mnkKeyGroup(MnkService) {
    return {
        restrice : 'EA',
        link : function (scope, element, attrs){
            MnkService.pushKeyGroup(element, attrs);
        }
    }
}

function mnkShortcutGroup(MnkService) {
    return {
        restrice : 'EA',
        link : function (scope, element, attrs){
            MnkService.pushShortcutGroup(element, attrs);
        }
    }
}

function mnkKeyboard(MnkService) {
    return {
        restrice : 'EA',
        link : function (scope, element, attrs) {
            MnkService.setKeyboard(element, attrs);
        }
    }
}

function IsPC()  
{  
    var userAgentInfo = navigator.userAgent;  
    var Agents = new Array("Android", "iPhone", "SymbianOS", "Windows Phone", "iPad", "iPod");  
    var flag = true;  
    for (var v = 0; v < Agents.length; v++) {  
        if (userAgentInfo.indexOf(Agents[v]) > 0) { flag = false; break; }  
    }  
    return flag;  
}  
    
})();