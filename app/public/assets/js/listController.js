/**
 * Created by SECONDRED on 03.08.2017.
 */
function treeify(list, idAttr, parentAttr, childrenAttr) {
    if (!idAttr) idAttr = 'id';
    if (!parentAttr) parentAttr = 'parent';
    if (!childrenAttr) childrenAttr = 'children';
    var treeList = [];
    var lookup = {};
    list.forEach(function(obj) {
        lookup[obj[idAttr]] = obj;
        obj[childrenAttr] = [];
    });
    list.forEach(function(obj) {
        if (obj[parentAttr] != null) {
            lookup[obj[parentAttr]][childrenAttr].push(obj);
        } else {
            treeList.push(obj);
        }
    });
    return treeList;
}

var buildTree = function(elements, tree){
    console.log('allElements', elements);
    if(typeof tree === 'undefined'){
        tree = [];
    }
    angular.forEach(elements, function(item){
        var tmpChildren = item.children;
        item.children = [];
        angular.forEach(tmpChildren, function(id){
            item.children.push(elements.filter(function(el){
                return el.id == id;
            }));
        });
        tree.push(item);
    });
    console.log(tree);
    return tree;
};

if(typeof scopes === 'undefined'){
    var scopes = {};
}

var container = $('.listController');
var test = null;
$.each(container, function(index, item){
    var list = $(item).data('list');
    myApp.controller('listController'+list, ['$scope','$http', function($scope,$http) {
        test = $scope;
        $scope.init = function(){
            $scope.hasChildren = false;
            $scope.entries = [];
            $scope.parentFieldId = '';
            if(typeof entries[list] !== 'undefined'){
                var myEntries = [];
                //create object with id => entryId
                var parentKey = '';
                angular.forEach(attributes[list], function(attribute, index){
                    if(index === 'parent'){
                        $scope.parentFieldId = 'parent';
                        parentKey = 'parent';
                        $scope.hasChildren = true;
                    }
                    if(attribute[0] === 'position' && 'relatedField' in attribute){
                        console.log(attribute);
                        parentKey = attribute['relatedField'];
                        $scope.parentFieldId = parentKey;
                    }
                });
                if(parentKey){
                    /*angular.forEach(entries[list], function(item){
                        item['hasChildren'] = true;
                        item.parent = (item[parentKey].ids.length)? item[parentKey].ids[0] : null;
                        myEntries.push(item);
                    });*/
                    console.log('buidTree', entries[list]);
                    _.each(entries[list], function (o) {
                        o.children.forEach(function (childId) {
                            _.findWhere(entries[list], {id: childId}).parents = o.id;
                        });
                    });

                    console.log('after under', entries[list]);
                    var tree = treeify(entries[list], null, 'parents');
                    console.log('tree', tree);
                    $scope.entries = tree;
                    //console.log('entries', $scope.entries);
                    //$scope.entries = treeify(myEntries);
                }else{
                    $scope.entries = entries[list];
                }

            }
        };

        $scope.send = function(data, model){
            var form = new FormData();

            var model = angular.copy(model);
            if($scope.parentFieldId){
                model[$scope.parentFieldId] = [data.parentId];
            }else{
                model[$scope.parentFieldId] = [];
            }
            delete model.children;
            form.append("entry", JSON.stringify(model));
            form.append("data", JSON.stringify(data));
            form.append('action', "entry/saveTree");

            console.log(data);
            console.log(model);
            return true;
            $http({
                method: 'POST',
                url: '',
                data: form,
                headers: { 'Content-Type': undefined},
                transformRequest: angular.identity
            }).then(function successCallback(response) {
                console.log(response.data);
                if(response.data === "true"){
                    showNotification('Der Eintrag wurde erfolgreich gespeichert', 'notice');
                }else{
                    showNotification('Fehler beim Speichern des Eintrags', 'error');
                }
                // this callback will be called asynchronously
                // when the response is available
            }, function errorCallback(response) {
                // called asynchronously if an error occurs
                // or server returns response with an error status.
            });
        };


        var tmpList = [];

        $scope.init();
        $scope.rootItem = {
            title: list,
            children: $scope.entries,
            hasChildren: true,
            first: true
        };

        $scope.sortableOptions = {
            opacity: '0.8',
            tolerance: 'pointer',
            handle: ".move",
            //connectWith: ".sortable",
            connectWith: ".apps-container",
            update: function(e, ui){
                if (this === ui.item.parent()[0]) {
                    var sortable = ui.item.sortable;
                    var model = sortable.model;
                    var sourceModel = sortable.sourceModel;
                    var data = {
                        position: sortable.dropindex,
                        parentId: $(sortable.droptarget[0]).data('id'),
                        oldPosition: sortable.index
                    };

                    if(sortable.droptargetModel !== sortable.sourceModel){
                        var sourceIds = [];

                        for(var i = 0; i < sourceModel.length; i++){
                            if(model.id !== sourceModel[i].id){
                                sourceIds.push(sourceModel[i].id);
                            }
                        }
                        data['sourceIds'] = sourceIds;
                    }
                    $scope.send(data, model);
                }
            }
        };

        $scope.getView = function (item) {
            /*
             you can return a different url
             to load a different template dynamically
             based on the provided item
             */
            if (item) {
                return 'nestable_item' + list + '.html';
            }
            return null;
        };


        scopes[list] = $scope;
    }]);
});