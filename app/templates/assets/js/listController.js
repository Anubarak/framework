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
};

if(typeof scopes === 'undefined'){
    var scopes = {};
}

var container = $('.listController');
$.each(container, function(index, item){
    var list = $(item).data('list');
    myApp.controller('listController'+list, ['$scope','$http', function($scope,$http) {
        $scope.init = function(){
            $scope.hasChildren = false;
            $scope.entries = [];
            if(typeof entries[list] !== undefined){
                var myEntries = [];
                //create object with id => entryId
                var parentKey = '';
                angular.forEach(attributes[list], function(attribute){
                    if(attribute[0] === 'position' && 'relatedField' in attribute){
                        parentKey = attribute['relatedField'];
                    }
                });
                if(parentKey){
                    angular.forEach(entries[list], function(item){
                        item['hasChildren'] = true;
                        item.parent = (item[parentKey].ids.length)? item[parentKey].ids[0] : null;
                        myEntries.push(item);
                    });
                    $scope.entries = treeify(myEntries);
                }else{
                    $scope.entries = entries[list];
                }

            }
        };

        $scope.send = function(data, model){
            $http({
                method: 'POST',
                url: '',
                data: {
                    action: "entry/saveTree", entry: model, data: data
                }
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
        }


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