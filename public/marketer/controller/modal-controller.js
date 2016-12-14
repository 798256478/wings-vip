/**
 * Modal Controller
 */
'use strict';

angular.module('app').controller('ModalInstanceCtrl', function ($scope, $uibModalInstance, items) {
    $scope.items = items;
    $scope.ok = function () {
        $uibModalInstance.close($scope.items);
    };

    $scope.cancel = function () {
        $uibModalInstance.dismiss('cancel');
    };
});
