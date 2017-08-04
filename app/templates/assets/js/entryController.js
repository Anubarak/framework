/**
 * Created by SECONDRED on 03.08.2017.
 */
var test = null;
var container = $('.entryController');
$.each(container, function(index, item){
    var id = $(item).data('id');
    var className = $(item).data('class');
    myApp.controller('entryController'+id, ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {
        $scope.data = {};
        $scope.relations = [];
        $scope.classes = {};
        $scope.alpha = false;
        $scope.form = className + id +  'Form';
        $scope.slugEmpty = true;
        $scope.allRelations = {};
        $scope.errorMessages = {};
        angular.forEach(attributes, function (item, index) {
            if ("relatedTo" in item) {
                $scope.allRelations[index] = [];
            }
        });
        $scope.editor = null;

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
        });
        if ("slug" in $scope.data && $scope.data.slug) {
            $scope.slugEmpty = false;
        }
        test = $scope;

        $scope.sortableOptions = {
            opacity: '0.8',
            axis: 'y',
            tolerance: 'pointer',
        };

        $scope.relationTableToggleSelected = function (key, id) {
            console.log(key);
            console.log(id);
            console.log($scope.classes[key][id]);
            if (typeof $scope.classes[key][id] === "undefined") {
                $scope.classes[key][id] = false;
                console.log("crash");
            }
            $scope.classes[key][id] = !$scope.classes[key][id];
        };

        $scope.getRelation = function (key) {
            var relationModel = attributes[key]['relatedTo']['model'];
            var action = 'ajax/' + relationModel + "/find";
            $http({
                method: 'POST',
                url: '',
                data: {
                    action: action
                }
            }).then(function successCallback(response) {
                if (response.data) {
                    $scope.allRelations[key] = response.data;
                }

            }, function errorCallback(response) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });

        };

        $scope.removeRelation = function (element, key) {
            var id = element.index;
            $scope.relations[key] = $scope.relations[key].filter(function (el) {
                return el.index !== id;
            });
        };


        $scope.titleChange = function () {
            var touched = $scope[$scope.form].slug.$dirty;
            if (!touched && $scope.slugEmpty) {
                var title = $scope.data.title.replaceAll(/ /, "-");
                $scope.data.slug = title;
            }
        };

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

            $http({
                method: 'POST',
                url: '',
                data: {
                    action: "entry/save", entry: $scope.data
                }
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

        //TODO quill callback
        $scope.$on("onSelectionChanged", function () {
           alert("selekt change");
        });

        $scope.addRelation = function ($id, relation) {
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

        $scope.resetError = function(key){
            $scope[$scope.form].$setValidity(key, true);
            console.log(key);
            console.log($scope[$scope.form]);
        };



        $scope.editorCreated = function (editor) {
            alert("test");
            console.log(editor)
        }

        $scope.reset = function() {
            $scope[$scope.form].$setPristine();
            $scope[$scope.form].$setUntouched();
            angular.forEach($scope.data, function(item, index){
                //$scope[$scope.form][index].$error = {};
                $scope[$scope.form].$setValidity(index, true);
            });
        };
    }]);
});
