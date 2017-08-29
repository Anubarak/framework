var test = null;
myApp.controller('recordController', ['$scope','$http', function($scope,$http) {
    $scope.data = {
        records: anu.records
    };

    test = $scope;

    $scope.isSubmitting = false;
    $scope.toggleInstall = function(record){
        if($scope.isSubmitting){
            return false;
        }
        $scope.isSubmitting = true;
        var form = new FormData();
        form.append("record", record.tableName);
        form.append('action', "record/toggleInstallation");
        $http({
            method: 'POST',
            url: '',
            data: form,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity
        }).then(function successCallback(response) {
            console.log(response.data);
            $scope.isSubmitting = false;
            if(response.data.success === true){
                showNotification('Das Record wurde erfolgreich gespeichert', 'notice');
            }else{
                showNotification('Fehler beim Speichern des Records', 'error');
            }
            if('installed' in response.data){
                record.installed = response.data.installed;
            }
            // this callback will be called asynchronously
            // when the response is available
        }, function errorCallback(response) {
            $scope.isSubmitting = false;
            // called asynchronously if an error occurs
            // or server returns response with an error status.
        });
    }
}]);
