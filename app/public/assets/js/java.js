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
}



myApp.directive('uniqueSlug', function($http) {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attr, ngModel) {
            ngModel.$asyncValidators.slug = function(value) {
                return $http({
                    method: 'POST',
                    url: '',
                    data: {
                        action: 'entry/validateSlug', slug: value, class: scope.data.class, id: scope.data.id
                    }
                }).then(function resolved(data) {
                    if(!data.data.isValid){
                        return $q.reject("slug");
                    }
                    return true;
                }, function rejected(data) {
                    //username does not exist, therefore this validation passes
                    return true;
                });
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



myApp.directive('datepicker', function () {
    return {
        restrict: 'A',
        link: function ($scope, element, attrs, ngModelCtrl) {
            var key = element.data('id');
            element.datepicker(datepickerOptions).on('changeDate', function() {
                var value = element.val();
                $scope.data[key].set('date', value.substr(0,2));
                $scope.data[key].set('month', value.substr(3,2)-1);
                $scope.data[key].set('year', value.substr(6,4));
            });

            element.datepicker('update', $scope.data[key].format('DD.MM.YYYY'));
        }
    };
});

myApp.directive('timepicker', function () {
    return {
        restrict: 'A',
        link: function ($scope, element, attrs, ngModelCtrl) {
            var key = element.data('id');
            element.timepicker({
                'timeFormat': 'H:i:s'
            });
            element.timepicker('setTime', $scope.data[key].format('H:mm:ss'));
            element.on('changeTime', function() {
                var value = element.val();
                $scope.data[key].set('hour', value.substr(0,2));
                $scope.data[key].set('minute', value.substr(3,2));
                $scope.data[key].set('second', value.substr(6,2));
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
            allRelations:'=relations'
        },
        template:   '<div>' +
                        '{[{ b.title }]}' +
                        ' <a href=""  ng-click="removeRelations(item, index, x)">' +
                            '<i class="fa fa-trash-o" aria-hidden="true"></i>' +
                        '</a>' +
                    '</div>',
        link: function(scope) {
            RelationService.getElements('answer', true).then(function(elements){
                var newElement = elements.filter(function (el) {
                    return el.id == scope.x;
                });
                scope.b = (newElement.length)? newElement[0] : null;
            });

            scope.removeRelations = function(item, key, id){
                var index = item[key].indexOf(id);
                if(index !== -1){
                    item[key].splice( index, 1 );
                }
            };
        }
    }
}]);


myApp.directive('thinDirective', function($compile,$templateRequest, configService) {
    return {
        restrict: 'A',
        scope: false,
        compile: function (element, attrs) {
            return function (scope, element, attrs) {
                $templateRequest(configService.angularTemplatePath + 'admin/forms/matrix/' + attrs.thinDirective + ".twig").then(function (html) {
                    //var template = angular.element(html);
                    //console.log(html);
                    element.append($compile(html)(scope));

                });
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