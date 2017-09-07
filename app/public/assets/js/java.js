/**
 * Created by SECONDRED on 25.07.2017.
 */

String.prototype.setTime = function(time){
    var target = this;
    alert(target);
};

Array.prototype.removeElementByValue = function(value){
    var index = this.indexOf(value);
    if (index >= 0) {
        this.splice( index, 1 );
    }
};

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};

var showNotification = function (message, notificationClass) {
    var container = $("#notifications").children().first();
    container.text(message);
    container.toggleClass(notificationClass).fadeIn(800, function(){
        setTimeout(function(){
            container.fadeOut(800, function(){
                container.toggleClass(notificationClass);
            })
        }, 2000);
    });
};


var b = null;
var c = null;
myApp.directive('unique', function($http) {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attr, ngModel) {
            if('unique' in scope.attributes){
                ngModel.$asyncValidators.slug = function(value) {
                    c = ngModel;
                    var entryId = scope.rootScope.data.id? scope.rootScope.data.id : 0;
                    var entryClass = scope.rootScope.entryClass;
                    var form = new FormData();
                    form.append("class", entryClass);
                    form.append("slug", value);
                    form.append("id", entryId);
                    form.append('action', "entry/validateSlug");
                    return $http({
                        method: 'POST',
                        url: '',
                        data: form,
                        headers: { 'Content-Type': undefined},
                        transformRequest: angular.identity
                    }).then(function resolved(data) {
                        if(!data.data.isValid){
                            b = scope;
                            console.log("rootScope", scope.rootScope[scope.rootScope.form]);
                            scope.rootScope[scope.rootScope.form][scope.htmlPrefix + scope.index].$setValidity('unique', false);
                            //scope.$root.$$childTail[scope.$root.$$childTail.form]['slug'].$setValidity('unique', false);
                            //alert("the fuck trigger");
                        }else{
                            scope.rootScope[scope.rootScope.form][scope.htmlPrefix + scope.index].$setValidity('unique', true);
                        }
                        return value;
                    }, function rejected(data) {
                        //username does not exist, therefore this validation passes
                        return true;
                    });
                }
            }

        }
    };
});

myApp.directive('stringToNumber', function() {
    return {
        require: 'ngModel',
        link: function (scope, element, attrs, ngModel) {
            ngModel.$parsers.push(function (value) {
                return '' + value;
            });
            ngModel.$formatters.push(function (value) {
                return parseFloat(value);
            });
        }
    };
});

myApp.directive('setSlug', function() {
    return {
        require: 'ngModel',
        scope: false,
        link: function (scope, element, attrs, ngModel) {
            if(attrs.setSlug === 'title'){
                element.bind("keyup", function() {
                    console.log('scope', scope);
                    console.log('root', scope.rootScope);
                    scope.rootScope.titleChange();
                });
            }
        }
    };
});



myApp.directive('datepicker', function () {
    return {
        restrict: 'A',
        link: function ($scope, element, attrs, ngModelCtrl) {
            var key = element.data('id');
            element.datepicker(datepickerOptions).on('changeDate', function() {
                var value = element.val();
                $scope.datasource.set('date', value.substr(0,2));
                $scope.datasource.set('month', value.substr(3,2)-1);
                $scope.datasource.set('year', value.substr(6,4));
                $scope.$apply();
            });
            console.log('scope', $scope);
            element.datepicker('update', $scope.datasource.format('DD.MM.YYYY'));
        }
    };
});

myApp.directive('timepicker', function () {
    return {
        restrict: 'A',
        link: function ($scope, element, attrs, ngModelCtrl) {
            element.timepicker({
                'timeFormat': 'H:i:s'
            });
            element.timepicker('setTime', $scope.datasource.format('H:mm:ss'));
            element.on('changeTime', function() {
                var value = element.val();
                $scope.datasource.set('hour', value.substr(0,2));
                $scope.datasource.set('minute', value.substr(3,2));
                $scope.datasource.set('second', value.substr(6,2));
                $scope.$apply();
            });
        }
    };
});

myApp.directive('moduleRelations', ['RelationService', function(RelationService) {
    return {
        restrict: 'E',

        replace: true,
        transclude: false,
        scope: {
            x:'=x',
            item:'=item',
            index:'=index',
            model: '=model'
        },
        template:   '<div>' +
                        '{[{ b.title }]}' +
                        ' <a href=""  ng-click="removeRelation(item, x)">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                        '</a>' +
                    '</div>',
        link: function(scope) {
            RelationService.getElements(scope.model, true).then(function(elements){
                console.log(elements);
                var newElement = elements.filter(function (el) {
                    return el.id == scope.x;
                });
                scope.b = (newElement.length)? newElement[0] : null;
            });

            scope.removeRelation = function(item, relationId){
                var index = item.indexOf(relationId);
                if(index !== -1){
                    item.splice( index, 1 );
                }
            };
        }
    }
}]);


myApp.directive('thinDirective', function($compile,$templateRequest, configService) {
    return {
        restrict: 'A',
        scope: {
            datasource: "=",
            attributes:"=",
            index: "=",
            rootScope: "="
        },
        compile: function (element, attrs) {
            return function (scope, element, attrs) {
                scope.htmlPrefix = attrs['prefix'];
                $templateRequest(configService.angularTemplatePath + 'admin/forms/matrix/' + attrs.thinDirective + ".twig").then(function (html) {
                    //element.append($compile(html)(scope));
                    if("options" in scope.attributes && scope.attributes.options.length){
                        scope.datasource = scope.attributes.options[0].id;
                    }
                    var template = angular.element(html);
                    element.append(template);
                    $compile(template)(scope);
                    $(":not(#modalFormContainer) > .modalForm").appendTo($('#modalFormContainer'));
                });
            }
        },
        link: function ($scope, element, attrs, ngModelCtrl) {
            /**
             * callback after editor is created
             * @param editor
             */
            $scope.editorCreated = function (editor) {
                alert("test");
            };

            $scope.removeMatrixElement = function(test){
                alert(test);
            }
        }
    }
});

angular.module("myApp").factory("configService", function () {
    var config = anu.config;
    return config;
});

angular.module("myApp").factory('RelationService', function($http) {
    return {
        getElements: function(relationModel) {
            var action = 'ajax/' + relationModel + "/find";
            return $http({
                method: 'POST',
                url: '',
                data: {
                    action: action
                }
            }).then(function successCallback(response) {
                //console.log(response.data);
                return response.data;

            }, function errorCallback(response) {
                console.log(response);
                return [];
            });
        }
    }
});


var datepickerOptions = {
    constrainInput: false,
        format: "dd.mm.yyyy",
        prevText: 'Vorheriger',
        nextText: 'Weiter',
        firstDay: 0,
        dayNames: ["Sonntag","Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag"],
        dayNamesShort: ["So","Mo","Di","Mi","Do","Fr","Sa"],
        dayNamesMin: ["S","M","D","M","D","F","S"],
        monthNames: ["Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember"],
        monthNamesShort: ["Jan","Feb","Mär","Apr","Mai","Jun","Jul","Aug","Sep","Okt","Nov","Dez"]
};