/**
 * Created by SECONDRED on 03.08.2017.
 */

var test = '';
myApp.controller('bindFieldController', ['$scope', '$http', '$timeout', '$compile', 'configService', '$templateRequest', function ($scope, $http, $compile, configService) {
    $scope.data = {
        id: anu.record.id
    };
    $scope.errorMessages = {};
    $scope.fields = anu.fields;
    $scope.leftFields = [];
    $scope.rootScope = $scope;
    $scope.records = anu.records;
    $scope.sortAble1 = [];
    $scope.sortAble2 = [];
    /**
     * Contentmatrix build
     * @type {{matrix: [*]}}
     */

    $scope.editor = null;

    $scope.getTemplatePath = function(){
        return configService.angularTemplatePath;
    };

    $scope.init = function(){
        $scope.data.fields = [];
        angular.forEach(anu.fieldsForRecord, function(item){
            $scope.data.fields.push(item);
        });
        $scope.sortAble1 = $scope.data.fields;
        anu.fields.filter(function(e){
            if($.inArray(e.id, $scope.data.fields) === -1){
                $scope.leftFields.push(e.id);
                return true;
            }
            return false;
        });
    };
    $scope.init();

    /**
     * Slug handling
     */
    if ("slug" in $scope.data && $scope.data.slug) {
        $scope.slugEmpty = false;
    }

    /**
     * TODO remove just for console debuggin
     * @type {*}
     */
    test = $scope;

    /**
     * Sortable options
     * @type {{opacity: string, axis: string, tolerance: string}}
     */
    $scope.sortableOptions = {
        opacity: '0.8',
        axis: 'y',
        tolerance: 'pointer'
    };

    $scope.sortableMatrix = {
        handle: ".move",
        axis: 'y',
        opacity: '0.8'
    };


    /**
     * Save entry -> upload to server
     */
    $scope.send = function () {
        //$scope.reset();
        var form = new FormData();
        var data = angular.copy($scope.data);
        form.append("record", JSON.stringify(data));
        form.append('action', "field/bindFieldsSave");
        $http({
            method: 'POST',
            url: '',
            data: form,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity
        }).then(function successCallback(response) {
            if ('success' in response.data && response.data['success'] === true) {
                showNotification('Der Eintrag wurde erfolgreich gespeichert', 'notice');
                if('id' in response.data && response.data.id){
                    $scope.data.id = response.data.id;
                }
            } else {
                showNotification('Fehler beim Speichern des Eintrags', 'error');
                angular.forEach($scope.data, function(item, index){
                    if(index in response.data.errors){
                        $scope.errorMessages[index] = response.data.errors[index];
                        $scope[$scope.form].$setValidity(index,false);
                    }else{
                        $scope[$scope.form][index] = '';
                        $scope[$scope.form].$setPristine();
                        $scope[$scope.form].$setValidity(index, true);
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

    /**
     * Reset form before save
     * @param key
     */
    $scope.resetError = function(key){
        $scope[$scope.form].$setValidity(key, true);
    };



    $scope.getFieldTitle = function(attributes, index){
        return ('title' in attributes)? attributes['title'] : index;

    };

    /**
     * Reset form before validation
     */
    $scope.reset = function() {
        $scope[$scope.form].$setPristine();
        $scope[$scope.form].$setUntouched();
        angular.forEach($scope.data, function(item, index){
            //$scope[$scope.form][index].$error = {};
            $scope[$scope.form].$setValidity(index, true);
        });
    };

    $scope.inArray =  function(array, index){
        return $.inArray(index, array) > -1;
    };

    $scope.$on('$destroy', function() {
        console.log('Controller destroyed');
    });

    $scope.sortableOptions = {
        opacity: '0.8',
        tolerance: 'pointer',
        //connectWith: ".sortable",
        connectWith: ".sortable",
        update: function(e, ui){
            if (this === ui.item.parent()[0]) {
                console.log(e);
            }
        }
    };
}]);

