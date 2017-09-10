/**
 * Created by SECONDRED on 03.08.2017.
 */

myApp.controller('entryTypeController', ['$scope', '$http', 'configService',
        function ($scope, $http, configService) {
    $scope.data = {

    };

    $scope.entryTypes = anu.entryTypes;


}]);
