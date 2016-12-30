/**
 * Reservation Controller
 */
'use strict';

angular.module('app')
	.controller('ReservationController', ['$scope', '$location', 'AuthService', 'ReservationService', '$q',
		function ($scope, $location, AuthService, ReservationService, $q) {
			if (AuthService.current_user == null) {
				$location.path("/login");
				return;
			}
			$(".header .meta .page").text('预约解读');

			$scope.filter={
				'pageSize':17,
				'nowPage':1,
				'filter':null
			};

			$scope.total = 0;

			$scope.$watch('$viewContentloaded',function(){
                $(".widget .widget-body.auto-nohead").css({
                        "height": $("html").height() - 155,
                        "overflow-y": "auto"
                });
            })

			//初始化
			$scope.getReservationList=function(){
				ReservationService.getReservationList($scope.filter).then(function(res){
					$scope.total=parseInt(res.total);
					$scope.reservationList=res.reservations;
				},function(res){
					alert(res.message);
				});
			};
			$scope.getReservationList();

			$scope.search=function(filter){
				$scope.filter.nowPage=1;
				$scope.filter.filter=filter;
				$scope.getReservationList();
			}

			$scope.$watchCollection('pages.nowPage',function(newVal,oldVal){
				if(newVal==oldVal){
					return;
				}
				$scope.getCustomerList(newVal,$scope.search.name);
			});

		}
	]);