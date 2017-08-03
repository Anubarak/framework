/**
 * Created by SECONDRED on 03.08.2017.
 */
var blub;
myApp.controller('loginController', ['$scope','$http', function($scope,$http) {
    $scope.init = function(){
        $scope.showLogin = true;
        $scope.showPassword = false;
        $scope.showRegister = false;
        $scope.title = 'Register';
        $scope.state = 'showRegister';
        $scope.login = {
            username: '',
            password: ''
        };
        $scope.register = {
            username: '',
            email: '',
            password: ''
        };
        $scope.reset = {
            email: ''
        };
        $scope.data = 'login';
        $scope.errorMessages = {
            login: {},
            register: {},
            reset: {}
        }
    };

    $scope.init();
    blub = $scope;
    $scope.ShowHide = function (s) {
        $scope.showLogin = false;
        $scope.showPassword = false;
        $scope.showRegister = false;
        $scope[s] = true;
        if(s === 'showRegister'){
            $scope.state = 'showLogin';
            $scope.title = 'Login';
            $scope.data = 'register';
        }
        if(s === 'showLogin'){
            $scope.state = 'showRegister';
            $scope.title = 'Register';
            $scope.data = 'login';
        }
        if(s === 'showPassword'){
            $scope.data = 'reset';
        }
    };

    $scope.reset = function() {
        var data = $scope.data;
        var form = data + "Form";
        $scope[form].$setPristine();
        $scope[form].$setUntouched();
        angular.forEach($scope[data], function(item, index){
            $scope[form][index] = '';
            $scope[form].$setValidity(index, true);
        });
    };

    $scope.send = function(){
        //$scope[$scope.data + "Form"].$setPristine();
        //$scope[$scope.data + "Form"].$setValidity();
        $scope.reset();
        $http({
            method: 'POST',
            url: '',
            data: {
                action: "user/"+$scope.data, user: $scope[$scope.data]
            }
        }).then(function successCallback(response) {
            if ('success' in response.data && response.data['success'] === true) {
                showNotification(response.data['message'], 'notice');
                $('#loginPanel').modal('hide');
            } else {
                showNotification(response.data['message'], 'error');
                angular.forEach($scope[$scope.data], function(item, index){
                    console.log(index);
                    if(index in response.data.errors){
                        console.log(response.data.errors[index]);
                        $scope.errorMessages[$scope.data][index] = response.data.errors[index];
                        $scope[$scope.data + "Form"].$setValidity(index,false);
                    }else{
                        $scope[$scope.data + "Form"][index] = '';
                        $scope[$scope.data + "Form"].$setPristine();
                        $scope[$scope.data + "Form"].$setValidity(index, true);
                    }
                });

            }
            // this callback will be called asynchronously
            // when the response is available
        }, function errorCallback(response) {
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        });
    };
}]);