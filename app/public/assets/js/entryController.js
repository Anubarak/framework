/**
 * Created by SECONDRED on 03.08.2017.
 */
var test = null;
var container = $('.entryController');
$.each(container, function(index, item){
    var id = $(item).data('id');
    var className = $(item).data('class');
    myApp.controller('entryController'+id, ['$scope', '$http', '$timeout', '$compile', 'configService', 'RelationService', function ($scope, $http, $timeout, $compile, configService, RelationService) {
        $scope.data = {
            id: anu.entry.id
        };
        $scope.form = className + id +  'Form';
        $scope.slugEmpty = true;
        $scope.allRelations = {};
        $scope.errorMessages = {};
        $scope.editors = {};
        $scope.attributes = anu.entry.attributes;
        // class/type of entry... not really used but we need to store it
        $scope.entryClass = anu.entry.class;

        /**
         * Contentmatrix build
         * @type {{matrix: [*]}}
         */
        $scope.matrixElements = {
            /*
            matrix: [
                {
                    title: "text",
                    attributes: {
                        headline: ['mixed'],
                        text: ['text']
                    },
                    text: "zewites",
                    headline: "zweites"
                },

            ]
            */
        };
        $scope.matrixTempIdCounter = 0;

        angular.forEach(attributes, function (item, index) {
            if ("relatedTo" in item) {
                RelationService.getElements(item.relatedTo.model).then(function(element){
                    $scope.allRelations[item.relatedTo.model] = element;
                });
            }
        });
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
            } else if (index in $scope.attributes && $scope.attributes[index][0] === 'matrix') {
                angular.forEach(item, function(i){
                    i.tmpId = $scope.matrixTempIdCounter;
                    $scope.matrixTempIdCounter++;
                });
                $scope.matrixElements[index] = item;
            } else {
                $scope.data[index] = item;
            }


        });
        console.log($scope.data);

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
         * Toggle selection for Relation Modal
         * @param item
         * @param id
         */
        $scope.relationTableToggleSelected = function (item, id) {
            if(typeof item !== 'undefined'){
                var index = item.indexOf(id);
                if(index === -1){
                    //add element
                    item.push(id);
                }else{
                    item.splice( index, 1 );
                }
            }
        };

        /**
         * Get all possible active elements, that can be related
         * @param relationModel
         */
        $scope.getRelation = function (relationModel) {
            RelationService.getElements(relationModel).then(function(element){
                $scope.allRelations[relationModel] = element;
            });
        };

        $scope.removeRelations = function(item, key, id){
            console.log("=====removeRelations=====");
            console.log(item);
            if(typeof id === 'undefined'){
                alert("remove all");
                item[key] = [];
            }else{
                var index = item[key].indexOf(id);
                if(index !== -1){
                    item[key].splice( index, 1 );
                }
            }
        };

        /**
         * onchange - titlechange for slug
         */
        $scope.titleChange = function () {
            var touched = $scope[$scope.form].slug.$dirty;
            if (!touched && $scope.slugEmpty) {
                var title = $scope.data.title.replaceAll(/ /, "-");
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
         * Add Matrix Element entry
         *
         * @param matrixKey
         * @param attributeKey
         */
        $scope.addMatrixElement = function(matrixKey, attributeKey){
            var lenght = (attributeKey in $scope.matrixElements)? $scope.matrixElements[attributeKey].length : 0;
            $http({
                method: 'POST',
                url: '',
                data: {
                    action: "entry/getMatrixHtml", matrixKey: matrixKey, entryType: className, attributeKey: attributeKey, index: lenght
                }
            }).then(function successCallback(response) {
                console.log(response);
                var matrixAttributes = {
                    title: matrixKey,
                    attributes: response.data.attributes,
                    id: null,
                    matrixId: attributes[attributeKey][1],
                    type: matrixKey,
                    tmpId: $scope.matrixTempIdCounter
                };
                angular.forEach(response.data.attributes, function(item, index){
                    if(response.data.attributes[index][0] === 'relation'){
                        matrixAttributes[index] = [];
                    }else{
                        matrixAttributes[index] = null;
                    }
                });
                $scope.matrixTempIdCounter++;
                $scope.matrixElements[attributeKey].push(matrixAttributes);
            }, function errorCallback(response) {
                console.log(response);
            });
            //$scope.matrix
        };


        /**
         * Cache relations when opening new window to be able to restore state before
         * @param relations
         */
        $scope.cacheRelations = function(relations){
            $scope.relationCache = angular.copy(relations);
        };

        /**
         * restore cached relations
         *
         * @param relations
         * @returns {XML|XMLList|*}
         */
        $scope.restoreCache = function (relations) {
            relations = angular.copy($scope.relationCache);
            return relations;
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

        $scope.alert = function(test){
            alert(test);
        };

        $scope.inArray =  function(array, index){
            return $.inArray(index, array) > -1;
        }
    }]);
});
