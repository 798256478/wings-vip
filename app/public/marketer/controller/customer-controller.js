/**
 * Customer Controller
 */
'use strict';

angular.module('app')
.controller('CustomerController', ['$scope', '$location', 'AuthService', 'CardService', 'TicketService',
    function($scope, $location, AuthService, CardService, TicketService) {
        if(AuthService.current_user == null){
            $location.path("/login");
            return;
        }
        $(".header .meta .page").text('会员');
        $scope.getCardList = function(){
            CardService.getNewCardSummaries().then(function(res){
                $scope.cardList = res.cards;
            },function(res){
                $scope.error = res.message;
            });
        }
        $scope.getCardList();

        CardService.getTotal().then(function(res){
            $scope.cardTotal = res;
        },function(res){
            $scope.error = res.message;
        });

        $scope.Card = null;
        $scope.getCustomer = function(cardid){
            CardService.getCardDetail(cardid).then(function(res){
                $scope.Card = res;
            },function(res){
                alert(res.message);
            });
            TicketService.getCardTicketList(cardid).then(function(res){
                if(res.tickets){
                    $scope.TicketList = res.tickets;
                }else {
                    $scope.TicketList = [];
                }
            },function(res){
                alert(res.message);
            });
        }

        $scope.searchValue = '';

        $scope.Search = function(val){
            if(val.length > 3){
                CardService.Search(val).then(function(res){
                    $scope.cardList = res.cards;
                },function(res){
                    alert(res.message);
                });
            }else if(val.length == 0){
                $scope.getCardList();
            }
        }
    }
]);
