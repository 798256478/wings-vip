// (function () {
// 'use strict';

    angular.module('angu-complete',[])
    .directive('complete',['$parse','$http','$sce',completeDirective]);

    function completeDirective($parse,$http, $sce)
    {
        return {
            restrict:'E',
            scope:{
                "id":"@id",
                "placeholder":"@placeholder",
                "inputClass":"@inputclass",
                "searchField":"@searchfield",
                "titleField":"@titlefield",
                "localData":"=localdata",
                "minLengthUser":"@minlength",
                "selectedObject": "=selectedobject",
                "matchClass":"@matchclass",
                "pageSize":"@pagesize",
            },
            template:'<div class="complete-holder">'+
                        '<input id="{{id}}_input" mnk-input shortcut-group="choose" ng-model="searchStr" type="text" placeholder="{{placeholder}}" class="{{inputClass}}"  />'+
                        '<div id="{{id}}_dropdown" class="complete-dropdown" ng-if="showDropdown">'+
                            '<div class="complete-row {{ $index == currentIndex?\'complete-selected-row\':\'\'}}" ng-repeat="result in results" ng-mousedown="choose($index)" >'+
                                '<div ng-repeat="title in titleFields " ng-bind-html="result[title]">'+
                                '</div>'+
                            '</div>'+
                        '</div>'+
                    '</div>',
            replace: true,
            transclude: true,
            link:function ($scope,element,attrs) {
                $scope.currentIndex=null;
                $scope.minLength=4;
                $scope.results=[];
                $scope.originalResults=[];
                $scope.titleFields=[];
                $scope.searchFields=[];
                if($scope.titleField&&$scope.titleField != ""){
                    $scope.titleFields=$scope.titleField.split(",");
                }
                if($scope.pageSize&&$scope.pageSize != ""){
                    $scope.pageSize=10;
                }
                
                if($scope.titleField&&$scope.titleField != ""){
                    $scope.searchFields=$scope.searchField.split(",")
                }
                
                if($scope.minLengthUser&&$scope.minLengthUser != ""){
                    $scope.minLength=$scope.minLengthUser;
                }
                
                $scope.change=function (event) {
                    if($scope.searchStr.length >= $scope.minLength){
                        $scope.showDropdown=true;
                        $scope.currentIndex=-1;
                        $scope.search($scope.searchStr);
                    }
                    else{
                        $scope.showDropdown=false;
                        $scope.currentIndex=null;
                        $scope.results=[];
                        $scope.originalResults=[];
                    }
                    $scope.$apply();
                }
                
                $scope.choose=function (index) {
                    $scope.selectedObject = $scope.originalResults[index];
                    $scope.showDropdown = false;
                    $scope.searchStr="";
                    angular.element('#'+$scope.id+'_value').focus();
                }
                
                $scope.showResult=function (matchs,str) {
                    if( $scope.matchClass&& $scope.matchClass!=''){
                        for(var i = 0; i < matchs.length; i++){
                            for(var j=0; j < $scope.titleFields.length; j++){
                                var title=matchs[i][$scope.titleFields[j]];
                                if(title&&title!='')
                                {
                                    var re = new RegExp(str, 'i');
                                    title = $sce.trustAsHtml(title.replace(re, '<span class="'+ $scope.matchClass +'">'+ str +'</span>'));
                                    matchs[i][$scope.titleFields[j]]=title;
                                }
                            }
                        }
                    }
                    $scope.results=matchs;
                }
                
                // $scope.next=function() {
                //     if($scope.results&&($scope.currentIndex+1)<$scope.results.length){
                //         $scope.currentIndex++;
                //         // $scope.$apply();
                      
                //         //  $scope.setFocus('#'+$scope.id+'_value')
                //     }
                //        angular.element('#'+$scope.id+'_value').focus();
                        
                // }
                
                // $scope.previous=function () {
                //     if($scope.results&&$scope.currentIndex >= 1){
                //         $scope.currentIndex--;
                //         // $scope.$apply();
                       
                //     }
                //     angular.element('#'+$scope.id+'_value').focus();
                // }
                
                $scope.returnButton=function()
                {
                    
                }
                
                $scope.search=function(str)
                {
                    if($scope.localData){
                        var matchs=[];
                        $scope.originalResults=[];
                        for(var i = 0; i < $scope.localData.length; i++){
                            var match=false;
                            for(var s = 0; s <  $scope.searchFields.length; s++){
                                var result=typeof($scope.localData[i][$scope.searchFields[s]]) === 'string' && typeof(str) === 'string' 
                                            && $scope.localData[i][$scope.searchFields[s]].toLowerCase().indexOf(str.toLowerCase()) >= 0;
                                match=match||result;
                            }
                            if(matchs.length>10)
                                break;
                            if(match)
                            {
                                $scope.originalResults[ $scope.originalResults.length]= $scope.localData[i];
                                matchs[matchs.length]=jQuery.extend({}, $scope.localData[i]);//浅层复制
                            }
                        }
                       $scope.showResult(matchs,str);
                    }
                }
                var inputEle = element.find('input');
                inputEle.bind('input propertychange change',$scope.change);
            }
            
        }
    }
// })();


