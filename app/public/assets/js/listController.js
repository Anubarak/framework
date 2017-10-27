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
                    if(attribute && attribute.length && attribute[0] === 'position' && 'relatedField' in attribute){
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
                    _.each(entries[list], function (o) {
                        o.children.forEach(function (childId) {
                            _.findWhere(entries[list], {id: childId}).parents = o.id;
                        });
                    });

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

        /**
         *
         * @param model
         * @param prevId
         * @returns {boolean}
         */
        $scope.send = function(model, prevId){
            var form = new FormData();

            var tmpModel = angular.copy(model);
            form.append("entry", JSON.stringify(tmpModel));
            form.append('action', "entry/saveTree");
            form.append('prevId', prevId);

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
        console.log($scope.entries);

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

        $scope.treeOptions = {
            dropped: function(event) {
                console.log("event", event);
                console.log("parent", event.dest.nodesScope.$parent.$modelValue);
                var parentNode = event.dest.nodesScope.$parent.$modelValue;
                var prevId = 0;
                if(event.prev === null){
                    if(parentNode){
                        prevId = parentNode.id;
                    }
                }else{
                    prevId = event.prev.$modelValue.id
                }

                //console.log("countChildren", event.source.nodeScope.childNodesCount());

                var node = event.source.nodeScope.$modelValue;
                node.parent = (typeof parentNode !== 'undefined')? [parentNode.id] : [];
                $scope.send(node, prevId);
            }
        };


        scopes[list] = $scope;
    }]);
});
var event