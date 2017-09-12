/**
 * Created by anuba on 12.09.2017.
 */
myApp.directive('minNumeric', function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attr, ngModel) {
            if('attributes' in scope && 'min_numeric' in scope.attributes){
                ngModel.$validators.min_numeric = function (value) {
                    if(value == "null" && scope.attributes.min_numeric == 0){
                        return true;
                    }
                    if(!value || value >= scope.attributes.min_numeric){
                        return true;
                    }else{
                        return false;
                    }
                };
            }
        }

    };
});

myApp.directive('maxNumeric', function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attr, ngModel) {
            if('attributes' in scope && 'max_numeric' in scope.attributes){
                ngModel.$validators.max_numeric = function (value) {
                    if(value == "null"){
                        return true;
                    }
                    if(value < scope.attributes.max_numeric){
                        return true;
                    }else{
                        return false;
                    }
                };
            }
        }
    };
});

/**
 *
 */
myApp.directive('minLen', function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attr, ngModel) {
            if('attributes' in scope && 'min_len' in scope.attributes){
                ngModel.$validators.min_len = function (value) {
                    if(value == "null" && scope.attributes.min_len == 0){
                        return true;
                    }

                    if(value != null && value.length >= scope.attributes.min_len){
                        return true;
                    }else{
                        return false;
                    }
                };
            }
        }

    };
});

/**
 * max len validator for mixed
 */
myApp.directive('maxLen', function() {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attr, ngModel) {
            if('attributes' in scope && 'max_len' in scope.attributes){
                ngModel.$validators.max_len = function (value) {
                    if(value == "null" || !value || value.length <= scope.attributes.max_len){
                        return true;
                    }else{
                        return false;
                    }
                };
            }
        }

    };
});


myApp.directive('depth', function(){
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function(scope, element, attr, ngModel) {
            if('attributes' in scope && 'depth' in scope.attributes){
                ngModel.$validators.depth = function (value) {
                    if(value == "null"){
                        return true;
                    }

                    if(countDecimalPlaces(value) <= scope.attributes.depth){
                        return true;
                    }else{
                        return false;
                    }
                };
            }
        }
    };
});