/**
 * Created by SECONDRED on 03.08.2017.
 */

var className = '';
var test = [];
var container = $('.entryController').first();
id = container.data('id');
myApp.controller('entryController', ['$scope', '$http', '$timeout', '$compile', 'configService', function ($scope, $http, $timeout, $compile, configService, RelationService) {
    $scope.data = {
        id: anu.entry.id
    };
    $scope.form = anu.entry.class + 'Form';
    $scope.slugEmpty = true;
    $scope.errorMessages = {};
    $scope.editors = {};
    $scope.attributes = anu.entry.attributes;
    // class/type of entry... not really used but we need to store it
    $scope.entryClass = anu.entry.class;
    $scope.rootScope = $scope;
    /**
     * Contentmatrix build
     * @type {{matrix: [*]}}
     */

    $scope.editor = null;

    $scope.getTemplatePath = function(){
        return configService.angularTemplatePath;
    };

    /**
     * init scope
     */
    $.each(anu.entry, function (index, item) {
        if(index === "attributes" || !(index in $scope.attributes)) return true;
        if(index in $scope.attributes && $scope.attributes[index][0] === 'datetime') {
            var dateTime = entry[index];
            var zone = 'Europe/London';
            var format = 'YYYY-MM-DD HH:mm:ss ZZ';
            var d = new Date();
            var n = d.getTimezoneOffset() * -1;
            $scope.data[index] = moment(dateTime, format).utcOffset(0).add(n, 'minute');
        } else {
            $scope.data[index] = item;
        }
    });

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
    test.push($scope);

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
     * onchange - titlechange for slug
     */
    $scope.titleChange = function () {
        var touched = $scope[$scope.form].slug.$dirty;
        console.log(!touched);
        console.log($scope);
        if (!touched && $scope.slugEmpty) {
            var title = $scope.data.title.replaceAll(/ /, "-");
            console.log(title);
            $scope.data.slug = title;
        }
    };

    /**
     * Save entry -> upload to server
     */
    $scope.send = function () {
        //$scope.reset();
        var form = new FormData();
        var data = angular.copy($scope.data);
        data.class = $scope.entryClass;
        var matrix = angular.copy($scope.matrixElements);
        form.append("entry", JSON.stringify(data));
        form.append("matrix", JSON.stringify(matrix));
        form.append('action', "entry/save");
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


    /**
     * callback after editor is created
     * @param editor
     * @param key
     */
    $scope.editorCreated = function (editor, key) {
        //$scope.editors[key] = editor;
        editor.on('selection-change', function(range, oldRange, source) {
            if (range) {
                var toolbar = editor.getModule('toolbar');
                var container = $(toolbar.container);
                container.show();
                if (range.length === 0) {
                    //console.log('User cursor is on', range.index);
                } else {
                    //var text = editor.getText(range.index, range.length);
                    //console.log('User has highlighted', text);
                }
            } else {
                //$scope[$scope.form].$setValidity(key, true);
                var toolbar = editor.getModule('toolbar');
                var container = $(toolbar.container);
                container.hide();
                //console.log('Cursor not in the editor');
            }
        });
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
}]);
