/*$(document).ready(function(){
    $.ajax({
        type: "post",
        url: '',
        dataType : 'json',
        data: {
            action: "home/test", entry: question
        },
        success: function(data){

            console.log(data);
        },
        error: function (XMLHttpRequest, textStatus) {
            console.log("Status: " + textStatus);
        }
    });
});
*/


/*

 var myApp = angular.module('myApp',[]).config(function($interpolateProvider){
 console.log("startet");
 $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
 });

myApp.controller('myCtrl', ['$scope','$http', function($scope,$http) {
    $scope.data = {};
    $.each(question, function(index, item){
        $scope.data[index] = item;
    });

    $scope.send = function(){
        console.log($scope.data);
        $http({
            method: 'POST',
            url: '',
            data: {
                action: "home/test", entry: $scope.data
            }
        }).then(function successCallback(response) {
            console.log(response);
            // this callback will be called asynchronously
            // when the response is available
        }, function errorCallback(response) {
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        });
    }
}]);

*/