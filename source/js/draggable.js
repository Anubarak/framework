var lastAcceptedField; //lastAcceptedField from unit


function isPlayerMovingBack(oldField, newField){
	if (oldField == null || newField == null) return false;
	return (oldField.data("x") == newField.data("x") && oldField.data("y") == newField.data("y") && oldField.hasClass("enemyOnField") == true);
	return false;
}
	
var currentMove = [];
var ownMove = [];
function recordUnitMovement(field){
	var x = field.data("x");
	var y = field.data("y");
	currentMove.push({x:x, y:y});
	//console.log(currentMove);
}

function checkDroppableBossArea(field, unit){
    var bossDragPart = unit.data("bossdragpart");
    if (bossDragPart != ""){
        var border = [];
        switch(bossDragPart){
            case "topLeft":
                border.x = 5;
                border.y = 7;
                break;
            case "topRight":
                border.x = 0;
                border.y = 7;
                break;
            case "bottomLeft":
                border.x = 5;
                border.y = 0;
                break;
            case "bottomRight":
                border.x = 0;
                border.y = 0;
                break;
        }
        return (field.data("x") != border.x && field.data("y") != border.y);
    }
    return true;
}

function createBoss(field, unit, clone){
	
	var x = parseInt(field.data("x"));
	var y = parseInt(field.data("y"));
	
	var xUnit = unit.parent().data("x");
	var yUnit = unit.parent().data("y");
	
	var position = unit.data("bossdragpart");
	var field1Position = [];
	var field2Position = [];
	var field3Position = [];
	var field4Position = [];
	
	var field1PositionOld = [];
	var field2PositionOld = [];
	var field3PositionOld = [];
	var field4PositionOld = [];
	switch(position){
		case "topLeft":
			//fieldPosition
			field1Position.x = parseInt(x);
			field1Position.y = parseInt(y);
		
			field2Position.x = parseInt(parseInt(x)+parseInt(1));
			field2Position.y = parseInt(y);
			
			field3Position.x = parseInt(x);
			field3Position.y = parseInt(parseInt(y)+parseInt(1));
			
			field4Position.x = parseInt(parseInt(x)+parseInt(1));
			field4Position.y = parseInt(parseInt(y)+parseInt(1));
			
			//unit Positions
			field1PositionOld.x = parseInt(xUnit);
			field1PositionOld.y = parseInt(yUnit);
		
			field2PositionOld.x = parseInt(parseInt(xUnit)+parseInt(1));
			field2PositionOld.y = parseInt(yUnit);
			
			field3PositionOld.x = parseInt(xUnit);
			field3PositionOld.y = parseInt(parseInt(yUnit)+parseInt(1));
			
			field4PositionOld.x = parseInt(parseInt(xUnit)+parseInt(1));
			field4PositionOld.y = parseInt(parseInt(yUnit)+parseInt(1));
			break;
		case "topRight":
			//fieldPositions
			field1Position.x = parseInt(parseInt(x)-parseInt(1));
			field1Position.y = parseInt(y);
		
			field2Position.x = parseInt(x);
			field2Position.y = parseInt(y);
			
			field3Position.x = parseInt(parseInt(x)-parseInt(1));
			field3Position.y = parseInt(y+parseInt(1));
			
			field4Position.x = parseInt(x);
			field4Position.y = parseInt(parseInt(y)+parseInt(1));
			
			//unit Positions
			field1PositionOld.x = parseInt(parseInt(xUnit)-parseInt(1));
			field1PositionOld.y = parseInt(yUnit);
		
			field2PositionOld.x = parseInt(xUnit);
			field2PositionOld.y = parseInt(yUnit);
			
			field3PositionOld.x = parseInt(parseInt(xUnit)-parseInt(1));
			field3PositionOld.y = parseInt(yUnit+parseInt(1));
			
			field4PositionOld.x = parseInt(xUnit);
			field4PositionOld.y = parseInt(parseInt(yUnit)+parseInt(1));
			break;
		case "bottomLeft":
			//fieldPositions
			field1Position.x = parseInt(x);
			field1Position.y = parseInt(parseInt(y)-parseInt(1));
		
			field2Position.x = parseInt(parseInt(x)+parseInt(1));
			field2Position.y = parseInt(parseInt(y)-parseInt(1));
			
			field3Position.x = parseInt(x);
			field3Position.y = parseInt(y);
			
			field4Position.x = parseInt(parseInt(x)+parseInt(1));
			field4Position.y = parseInt(y);
			
			//unitPositions
			field1PositionOld.x = parseInt(xUnit);
			field1PositionOld.y = parseInt(parseInt(yUnit)-parseInt(1));
		
			field2PositionOld.x = parseInt(parseInt(xUnit)+parseInt(1));
			field2PositionOld.y = parseInt(parseInt(yUnit)-parseInt(1));
			
			field3PositionOld.x = parseInt(xUnit);
			field3PositionOld.y = parseInt(yUnit);
			
			field4PositionOld.x = parseInt(parseInt(xUnit)+parseInt(1));
			field4PositionOld.y = parseInt(yUnit);
			break;
		case "bottomRight":
			//fieldPosition
			field1Position.x = parseInt(parseInt(x)-parseInt(1));
			field1Position.y = parseInt(parseInt(y)-parseInt(1));
		
			field2Position.x = parseInt(x);
			field2Position.y = parseInt(parseInt(y)-parseInt(1));
			
			field3Position.x = parseInt(parseInt(x)-parseInt(1));
			field3Position.y = parseInt(y);
			
			field4Position.x = parseInt(x);
			field4Position.y = parseInt(y);
			
			//unitPositions
			field1PositionOld.x = parseInt(parseInt(xUnit)-parseInt(1));
			field1PositionOld.y = parseInt(parseInt(yUnit)-parseInt(1));
		
			field2PositionOld.x = parseInt(xUnit);
			field2PositionOld.y = parseInt(parseInt(yUnit)-parseInt(1));
			
			field3PositionOld.x = parseInt(parseInt(xUnit)-parseInt(1));
			field3PositionOld.y = parseInt(yUnit);
			
			field4PositionOld.x = parseInt(xUnit);
			field4PositionOld.y = parseInt(yUnit);
			break;
	}
	
	var field1 = $('td[data-x="' + field1Position.x + '"][data-y="'+ field1Position.y+'"]');
	var field2 = $('td[data-x="' + field2Position.x + '"][data-y="'+ field2Position.y+'"]');
	var field3 = $('td[data-x="' + field3Position.x + '"][data-y="'+ field3Position.y+'"]');
	var field4 = $('td[data-x="' + field4Position.x + '"][data-y="'+ field4Position.y+'"]');
	var unitPositions = [field1, field2, field3, field4];
	
	if (clone == false){
		var field1Old = $('td[data-x="' + field1PositionOld.x + '"][data-y="'+ field1PositionOld.y+'"]');
		var field2Old = $('td[data-x="' + field2PositionOld.x + '"][data-y="'+ field2PositionOld.y+'"]');
		var field3Old = $('td[data-x="' + field3PositionOld.x + '"][data-y="'+ field3PositionOld.y+'"]');
		var field4Old = $('td[data-x="' + field4PositionOld.x + '"][data-y="'+ field4PositionOld.y+'"]');
		var unitPositionsOld = [field1Old, field2Old, field3Old, field4Old];
		for (var i = 0; i<4; i++) unitPositionsOld[i].removeClass("filled");
	}
	
	
	if(field1.hasClass("filled") == false && field2.hasClass("filled") == false && field3.hasClass("filled") == false && field4.hasClass("filled") == false){
		var bossDragPart = ["topLeft", "topRight", "bottomLeft", "bottomRight"];
		for (var i = 0; i<4; i++){
			var newUnit;
			
			if (clone == true){
				newUnit = unit.clone();
			}else{
				newUnit = unitPositionsOld[i].children().first();
				unitPositionsOld[i].removeClass("filled");
			}
			newUnit.removeData("bossdragpart");
			newUnit.appendTo(unitPositions[i]);
			newUnit.css("height", "50px");
			newUnit.css("width", "50px");
			newUnit.addClass("draggableBoss");
			newUnit.addClass("enemyOnField");
			newUnit.data("bossdragpart", bossDragPart[i]);
			console.log("part = " + newUnit.data("bossdragpart"));
			unitPositions[i].addClass('filled');
			newUnit.draggable(DraggableOption.draggable_boss_opt)
		}
		//field.children().first().draggable(DraggableOption.draggable_boss_opt);
		return true;
	}else{
		return false;
		if(clone == false) for (var i = 0; i<4; i++) unitPositionsOld[i].removeClass("filled");
	};
}


var currentField;
var previousField;
var droppable_opt = {
		tolerance: "intersect",
		accept: function(e){
			if(e.hasClass("draggableEnemy") || e.hasClass("draggablePlayerUnit")) return true;
		},
		over: function( event, ui ) {
			var field = $(this);
			var dragUnit = ui.draggable;
			var moveUnit = field.children().last();
			if (dragUnit.hasClass("playerUnitOnField")){
				//check if unit is dragged
				if (dragUnit.data("isDragged") == "true" && field != lastAcceptedField && isPlayerMovingBack(field, lastAcceptedField) == false){
					if (hasUnitStealth == false){ //if unit has stealth -> valid move
						if (field.hasClass("draggableEnemy") == false && isUnitMoveable == true){ //if class is not filled and unit is able to move -> valid move
							if (field.children().first().hasClass("enemyOnField") == false){
							recordUnitMovement(field);
							}
						}
					}else{
						recordUnitMovement(field);
					}
				}
				var overClass = (hasUnitStealth == true)? "filled" : "filled";
				if (field.hasClass(overClass) == false && isUnitMoveable == true && isPlayerMovingBack(field, lastAcceptedField) == false){
					lastAcceptedField = field;
					//switch 1 position not working because of that
				}
				
				if (hasUnitStealth == false) previousField = lastAcceptedField;
				else previousField = currentField;
				
				currentField = $(this);
				if (field[0].childNodes.length > 0){
					if(moveUnit.hasClass("playerUnitOnField") && dragUnit.hasClass("playerUnitOnField") && moveUnit.data("isDragged") != "true" && isUnitMoveable == true){
						if (field.hasClass("filled") && hasUnitStealth == true ){
							previousField = lastAcceptedField;
							lastAcceptedField = currentField;
						}
						console.log("force mouve");
                        if(previousField.hasClass("filled") == false) forceUnitMovement(moveUnit, previousField);
						lastAcceptedField = currentField;
					}
				}	
			}
		},
		drop: function(event,ui){
			var unit = ui.draggable;
			var field = $(this);
			switch(unit.attr("id")){
			case "draggableBoss":
				//boss
                if (checkDroppableBossArea(field, unit)){
                    if(unit.hasClass("enemyOnField") == false){
                        createBoss(field, unit, true);
                    }else{
                        createBoss(field, unit, false);
                    }
                }
				break;
				
			case "draggableMinion":
				if(field.hasClass("filled") == false){
					if(unit.hasClass("enemyOnField") == false){
						field.append($(ui.draggable).clone());
						field.addClass("filled");
						var unit = field.children().first();
						unit.addClass("enemyOnField");
						unit.height(field.height());
						unit.width(field.width());
						unit.draggable(DraggableOption.draggable_minion_opt);
					}else{
						field.append($(ui.draggable));
						uiHelper.removeClass("filled");
					}
					field.addClass("filled");
				}
				break;
			
			case "draggableUnit":
				console.log(unit.data("isDragged"));
				if(field.hasClass("filled") == false  && isUnitMoveable == true){
					if(unit.hasClass("playerUnitOnField") == false){
						var unit = $(ui.draggable).clone();
						unit.appendTo(field);
						//field.append($(ui.draggable).clone());
						unit.addClass("playerUnitOnField");
						unit.draggable(DraggableOption.draggable_player_opt);
					}else{
						field.append($(ui.draggable));
						unit.css("display", "block");
						if (field.children().length == 0) uiHelper.removeClass("filled");
					}
					field.addClass("filled");
					DraggableOption.successfullyDropped = true;
					
				}else{
					if(unit.data("isDragged") != "true" && field.hasClass("filled") == false){
                        var unit = $(ui.draggable).clone();
                        lastAcceptedField.append(unit);
                        lastAcceptedField.children().first().css("display", "block").draggable(DraggableOption.draggable_player_opt);
                        lastAcceptedField.addClass("filled");
                        if (hasUnitStealth == true) recordUnitMovement(lastAcceptedField);
					}
					
				}
				break;
			}
		}
	};
//end of Droppable_option

var forcedDroppableState = "enable";
function toggleDroppableState(state, forcedState, specificClass){
	if (forcedState != null){
        forcedDroppableState = forcedState;
    }
    if (state == "disable"){
		$("#field tr td").droppable("disable");
		$("#field tr td div").draggable("disable");
	}
    if (state == "enable" && forcedDroppableState != "disable"){
		$("#field tr td").droppable("enable");
		if (specificClass == null) $("#field tr td div").draggable("enable");
        else $("."+specificClass).draggable("enable");
	}
}
	
	
	
	
////////////////////////////////////////////////////////////
//
//	Document.ready
//
////////////////////////////////////////////////////////////
$(document).ready(function (){
	$("td").droppable(droppable_opt);
	var uiHelper;
	var uiMoveIndex;
	
	$("#draggableBoss").draggable({
		appendTo: "body",
		helper: 'clone',
		revert: 'invalid',
        zIndex: 1000,
		start: function(event, ui) {
			$("#field tr td").droppable("option", "tolerance", "pointer");

		},
        drag: function(event, ui){
            ui.position={'top': event.pageY - 25, 'left': event.pageX - 25};
        }
	});
	
	$("#draggableMinion").draggable({
		appendTo: "body",
		helper: 'clone',
		revert: 'invalid',
        zIndex: 1000,
		start: function(event, ui) {
			$("#field tr td").droppable("option", "tolerance", "pointer");
		}
	});
	
	$(".draggablePlayerUnit").draggable({
		appendTo: "body",
		helper: 'clone',
		revert: 'invalid',
        zIndex: 1000
	});
	
	$.ui.intersect = function(draggable, droppable, toleranceMode) {
		if (!droppable.offset) {
			return false;
		}

		var draggableLeft, draggableTop,
			x1 = (draggable.positionAbs || draggable.position.absolute).left,
			y1 = (draggable.positionAbs || draggable.position.absolute).top,
			x2 = x1 + draggable.helperProportions.width,
			y2 = y1 + draggable.helperProportions.height,
			l = droppable.offset.left,
			t = droppable.offset.top,
			r = l + droppable.proportions.width,
			b = t + droppable.proportions.height;

		switch (toleranceMode) {
			case "custom":
				//you can define your rules here
				return (l < x1 + (draggable.helperProportions.width / 2) && // Right Half
					x2 - (draggable.helperProportions.width / 2) < r && // Left Half
					t < y1 && // Bottom Half
					b > y1 + 15 ); // Top Half
			case "fit":
				return (l <= x1 && x2 <= r && t <= y1 && y2 <= b);
			 case "pointer":
				draggableLeft = ((draggable.positionAbs || draggable.position.absolute).left + (draggable.clickOffset || draggable.offset.click).left);
				draggableTop = ((draggable.positionAbs || draggable.position.absolute).top + (draggable.clickOffset || draggable.offset.click).top);
				return isOverAxis( draggableTop, t, droppable.proportions.height ) && isOverAxis( draggableLeft, l, droppable.proportions.width );
			case "intersect":
				return (l < x1 + (draggable.helperProportions.width * 0.45) && // Right Half
					x2 - (draggable.helperProportions.width * 0.45) < r && // Left Half
					t < y1 + (draggable.helperProportions.height * 0.45) && // Bottom Half
					y2 - (draggable.helperProportions.height * 0.45) < b ); // Top Half
			case "touch":
				return (
					(y1 >= t && y1 <= b) || // Top edge touching
					(y2 >= t && y2 <= b) || // Bottom edge touching
					(y1 < t && y2 > b)      // Surrounded vertically
				) && (
					(x1 >= l && x1 <= r) || // Left edge touching
					(x2 >= l && x2 <= r) || // Right edge touching
					(x1 < l && x2 > r)      // Surrounded horizontally
				);
			default:
				return false;
			}
	};
	
	var isOverAxis = function(g,e,a){return g>e&&g<e+a}
	
	
});