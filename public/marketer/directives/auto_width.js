/**
 * Loading Directive
 * @see http://tobiasahlin.com/spinkit/
 */

angular
    .module('app')
    .directive('baseInfoTwoTwo', baseInfoTwoTwo)
    .directive('categoryTable', categoryTable)
    .directive('shopBaseInfoOne', shopBaseInfoOne)
    .directive('shopList', shopList)
    .directive('noCategory', noCategory)
    .directive('commodityListHeight', commodityListHeight)
    .directive('screenThreeTwo', screenThreeTwo)

function baseInfoTwoTwo() {
    var directive = {
        restrict: 'A',
        scope:{
            a:'=baseInfoTwoTwo'
        },
        link:function(scope,element,attr){

            element.children().eq(1).css({
                'width':element.innerWidth()-element.children().eq(0).innerWidth()-scope.a
            });
            //element.children().eq(1).width(element.width()-element.children().eq(0).width()-40);
        }
    };
    return directive;
}

function categoryTable() {
    var directive = {
        restrict: 'A',
        link:function(scope,element,attr){
            element.css({
	            'height':$("html").height()-610,
	            "overflow-y": "auto"
            });
        }
    };
    return directive;
}


function shopBaseInfoOne() {//店铺基本信息设置第一行第二个子元素的宽度以及第二个字元素的输入框宽度
    var directive = {
        restrict: 'A',
        link:function(scope,element,attr){
            element.children().eq(1).css({
                'width':element.innerWidth()-element.children().eq(0).innerWidth()-element.children().eq(2).innerWidth()-1
            });
            element.children().eq(1).children().eq(1).css({
                'width':element.children().eq(1).innerWidth()-element.children().eq(1).children().eq(0).innerWidth()-21
            });
        }
    };
    return directive;
}

function shopList() {
    var directive = {
        restrict: 'A',
        link:function(scope,element,attr){
            element.children().eq(0).css({
	            'height':$("html").height() - 206,
	            "overflow-y": "auto"
            });
        }
    };
    return directive;
}

function noCategory() {
    var directive = {
        restrict: 'A',
        link:function(scope,element,attr){
            element.height($("html").height()-451);
        }
    };
    return directive;
}

function commodityListHeight(){
    var directive = {
        restrict: 'A',
        link: function(scope, element, attr) {
            element.css({
                "height": $("html").height() - 165,
                "overflow-y": "auto"
            });
        }
    };
    return directive;
}

function screenThreeTwo(){
    var directive = {
        restrict: 'A',
        link: function(scope, element, attr) {
            element.children().eq(2).css({
                'width':element.innerWidth() - element.children().eq(0).outerWidth() -element.children().eq(1).outerWidth()-40
            });
        }
    };
    return directive;
}

