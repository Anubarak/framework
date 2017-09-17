/**
 * Created by SECONDRED on 03.08.2017.
 */
var blub;
myApp.controller('userController', ['$scope','$http', function($scope,$http) {
    $scope.init = function(){
        $scope.data = {};
    };

    $scope.users = anu.users;
    blub = $scope;
}]);