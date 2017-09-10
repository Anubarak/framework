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
    $scope.entryType = anu.entryType;
    $scope.tabs = [];
    /**
     * Contentmatrix build
     * @type {{matrix: [*]}}
     */

    $scope.editor = null;

    $scope.getTemplatePath = function(){
        return configService.angularTemplatePath;
    };

    $scope.init = function(){
        angular.forEach(anu.tabs, function(item){
            $scope.tabs.push(item);
        });

        var usedFields = [];
        angular.forEach(anu.tabs, function(item){
            angular.forEach(item['fields'], function(i){
                usedFields.push(i);
            });

        });

        $scope.sortAble1 = $scope.tabs;
        anu.fields.filter(function(e){
            if($.inArray(e.id, usedFields) === -1){
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
        var entryType = angular.copy($scope.entryType);
        var tabs = angular.copy($scope.tabs);
        form.append("record", JSON.stringify(data));
        form.append("entryType", JSON.stringify(entryType));
        form.append("tabs", JSON.stringify(tabs));
        form.append('action', "field/bindFieldsSave");
        $http({
            method: 'POST',
            url: '',
            data: form,
            headers: { 'Content-Type': undefined},
            transformRequest: angular.identity
        }).then(function successCallback(response) {
            if('tabIds' in response.data){
                var i = 0;
                angular.forEach($scope.tabs, function(){
                    $scope.tabs.id = response.data.tabIds[i];
                    i++;
                });
            }
            if ('success' in response.data && response.data['success'] === true) {
                showNotification('Der Eintragstyp wurde erfolgreich gespeichert', 'notice');
                if('id' in response.data && response.data.id){
                    $scope.entryType.id = response.data.id;
                }
            } else {
                showNotification('Fehler beim Speichern des Eintragstyp', 'error');
                angular.forEach($scope.data, function(item, index){
                    $scope.entryTypeForm.label.$setDirty(true);
                    $scope.entryTypeForm.handle.$setDirty(true);
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
                //console.log(e);
            }
        }
    };

    $scope.sortableGrid = {
        opacity: '0.8',
        tolerance: 'pointer',
        update: function(e, ui){
            if (this === ui.item.parent()[0]) {
                //console.log(e);
            }
        }
    };

    $scope.addTab = function(){
        bootbox.prompt({
            title: "Name des Feldlayout",
            buttons: {
                confirm: {
                    label: "Ok",
                    className: 'btn btn-danger',
                },
                cancel: {
                    label: "Abbrechen",
                    className: 'btn btn-default'
                }
            },
            callback: function(message){
                if(message){
                    var label = message;
                    var handle = message.replace(' ', '-');
                    var tabExists = $scope.tabs.filter(function(el){
                        return el.handle == handle;
                    });
                    console.log('tabExists', tabExists.length);
                    if(tabExists.length == 0){
                        $scope.tabs.push({
                            id: 0,
                            handle: handle,
                            label: label,
                            position: $scope.tabs.length,
                            fields: []
                        });
                        $scope.$apply();
                    }
                }
            }
        });
    };

    $scope.removeTab = function(tabKey){
        bootbox.confirm({
            title: "Sind Sie sicher",
            message: "Sind Sie sicher",
            buttons: {
                confirm: {
                    label: "Ok",
                    className: 'btn btn-danger',
                },
                cancel: {
                    label: "Abbrechen",
                    className: 'btn btn-default'
                }
            },
            callback: function (callback) {
                console.log($scope.tabs);
                if(callback){
                    if($scope.tabs[tabKey].fields.length){
                        angular.forEach($scope.tabs[tabKey].fields, function(field){
                           $scope.leftFields.push(field);
                        });
                    }
                    $scope.tabs.splice(tabKey, 1);
                    $scope.$apply();
                }
            }
        });
    };

    $scope.getTabName = function(tabHandle){
        var tab = $scope.tabs.filter(function(el){
            return el.handle == tabHandle;
        });
        if(tab){
            return tab[0].label;
        }
        return "";
    };
}]);

