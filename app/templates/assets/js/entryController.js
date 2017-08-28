/**
 * Created by SECONDRED on 03.08.2017.
 */
var test = null;
var container = $('.entryController');
$.each(container, function(index, item){
    var id = $(item).data('id');
    var className = $(item).data('class');
    myApp.controller('entryController'+id, ['$scope', '$http', '$timeout', '$compile', 'configService', function ($scope, $http, $timeout, $compile, configService) {
        $scope.data = {};
        $scope.relations = [];
        $scope.classes = {};
        $scope.alpha = false;
        $scope.form = className + id +  'Form';
        $scope.slugEmpty = true;
        $scope.allRelations = {};
        $scope.errorMessages = {};
        $scope.editors = {};
        $scope.attributes = attributes;

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
                $scope.allRelations[index] = [];
            }
        });
        $scope.editor = null;

        $scope.getTemplatePath = function(){
            return configService.angularTemplatePath;
        };

        /**
         * init scope
         */
        $.each(entry, function (index, item) {
            if (typeof item === "object" && item) {
                if ("class" in item && item.class === "elementCriteria") {
                    $scope.relations[index] = [];
                    var relationTitles = item.titles;
                    var relationIds = item.ids;
                    $scope.classes[index] = {};
                    for (var i = 0; i < relationTitles.length; i++) {
                        var relation = {
                            index: relationIds[i],
                            title: relationTitles[i]
                        };
                        $scope.relations[index].push(relation);
                        $scope.classes[index][relationIds[i]] = true;
                    }
                }
            }

            if (index in attributes && attributes[index][0] === 'datetime') {
                var dateTime = entry[index];
                var zone = 'Europe/London';
                var format = 'YYYY-MM-DD HH:mm:ss ZZ';
                var d = new Date()
                var n = d.getTimezoneOffset() * -1;
                $scope.data[index] = moment(dateTime, format).utcOffset(0).add(n, 'minute');
            } else {
                $scope.data[index] = item;
            }

            if (index in attributes && attributes[index][0] === 'matrix') {
                angular.forEach(item, function(i){
                    i.tmpId = $scope.matrixTempIdCounter;
                    $scope.matrixTempIdCounter++;
                    console.log(i);
                });
                $scope.matrixElements[index] = item;
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
         * @param key
         * @param id
         */
        $scope.relationTableToggleSelected = function (key, id, item) {
            if(typeof item !== 'undefined'){
                if(typeof item[key] === 'undefined'){
                    item[key] = [];
                }
                var index = item[key].indexOf(id);
                if(index === -1){
                    //add element
                    item[key].push(id);
                }else{
                    item[key].splice( index, 1 );
                }
            }else{
                console.log(key);
                console.log(id);
                console.log($scope.classes[key][id]);
                if (typeof $scope.classes[key][id] === "undefined") {
                    $scope.classes[key][id] = false;
                    console.log("crash");
                }
                $scope.classes[key][id] = !$scope.classes[key][id];
            }
        };

        /**
         * Get all possible active elements, that can be related
         * @param relationModel
         * @param key
         */
        $scope.getRelation = function (relationModel, key) {
            var action = 'ajax/' + relationModel + "/find";
            $http({
                method: 'POST',
                url: '',
                data: {
                    action: action
                }
            }).then(function successCallback(response) {
                console.log(response);
                if (response.data) {
                    $scope.allRelations[key] = response.data;
                }

            }, function errorCallback(response) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });

        };

        /**
         * Remove relation from Entry
         * @param element
         * @param key
         */
        $scope.removeRelation = function (element, key) {
            var id = element.index;
            $scope.relations[key] = $scope.relations[key].filter(function (el) {
                return el.index !== id;
            });
        };

        $scope.getRelationByEntryId = function(index){
            var element = $scope.allRelations["test_id"].filter(function (el) {
                console.log(el);
                console.log(el.id);
                return el.id == index;
            });
            return (element.length)? element[0] : null;
        };

        $scope.removeRelations = function(item, key, id){
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
            var key;
            $scope.reset();
            for (key in $scope.relations) {
                var relations = [];
                angular.forEach($scope.relations[key], function (item, index) {
                    relations.push(item.index);
                });
                $scope.data[key] = relations;
            }
            var form = new FormData();
            var data = angular.copy($scope.data);
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
            $http({
                method: 'POST',
                url: '',
                data: {
                    action: "entry/getMatrixHtml", matrixKey: matrixKey, entryType: className, attributeKey: attributeKey, index: $scope.matrixElements[attributeKey].length
                }
            }).then(function successCallback(response) {
                console.log(response);
                //console.log(response.data.html);
                var matrixAttributes = {
                    title: matrixKey,
                    attributes: response.data.attributes,
                    id: null,
                    matrixId: attributes[attributeKey][1],
                    type: matrixKey,
                    tmpId: $scope.matrixTempIdCounter
                };
                $scope.matrixTempIdCounter++;
                $scope.matrixElements[attributeKey].push(matrixAttributes);
                /*
                $scope.data[attributeKey].push({});
                if("attributes" in response.data){
                    var newMatrixAttributes = {};
                    angular.forEach(response.data.attributes, function(item, index){
                        newMatrixAttributes[index] = '';
                    });
                    $scope.data[attributeKey].push(newMatrixAttributes);
                }

                $('#'+className+id+attributeKey).append($compile('<li>' + response.data.html + '</li>')($scope));*/
                //$scope.apply();
            }, function errorCallback(response) {
                console.log(response);
            });
            //$scope.matrix
        };

        $scope.removeMatrixElement = function(item, key){
            console.log(item);
            console.log(key);
            $scope.matrixElements[key].removeElementByValue(item);
        };


        /**
         * Add Relations
         * @param $id
         * @param relation
         */
        $scope.addRelation = function ($id, relation) {
            alert($id);
            alert(relation);
            return;
            var rows = $("#" + $id).find('.selected');
            var newRelations = [];
            var arrIds = [];
            $.each(rows, function (k, v) {
                var i = $(v);
                arrIds.push(i.data('id'));
                newRelations.push({
                    index: i.data('id'),
                    title: i.data('title')
                })
            });
            $scope.data[relation] = arrIds;
            $scope.relations[relation] = newRelations;
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
            $scope.editors[key] = editor;
            editor.on('selection-change', function(range, oldRange, source) {
                if (range) {
                    var toolbar = $scope.editors[key].getModule('toolbar');
                    var container = $(toolbar.container);
                    container.show();
                    if (range.length === 0) {
                        //console.log('User cursor is on', range.index);
                    } else {
                        //var text = editor.getText(range.index, range.length);
                        //console.log('User has highlighted', text);
                    }
                } else {
                    $scope[$scope.form].$setValidity(key, true);
                    var toolbar = $scope.editors[key].getModule('toolbar');
                    var container = $(toolbar.container);
                    container.hide();
                    //console.log('Cursor not in the editor');
                }
            });
            console.log(editor);
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
        }
    }]);
});
