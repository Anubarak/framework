////////////////////////////////////////////////////////////////////////////
//
//
//	Draggable Options for each unit Type
//
//	playerUnit
//	Boss
// 	Minion
//
//
////////////////////////////////////////////////////////////////////////////
//global Variables
var DraggableOption = {};
//has Unit Stealth ability -> slip through enemies
var hasUnitStealth = false;
//may Unit be moved or does it stuck in enemie
var isUnitMoveable = true;
//last valid position of unit
var posX = 0;
var posY = 0;

//get all Top,Left properties from Enemies, return Object.x Object.y Object.width, Object.height
var enemyPositions = [];
function getAllEnemyPositions(){
	var allEnemys = $(".enemyOnField");
	enemyPositions = [];
	$.each(allEnemys, function(index, item){
		item = $(item);
        var bossdragpart = item.data("bossdragpart");
        var extendedWidth = 0;
        var extendedHeight = 0;
        switch(bossdragpart){
            case "topLeft":
                extendedHeight = item.height() * 0.1;
                extendedWidth = item.width() * 0.1;
                break;
            case "topRight":
                extendedHeight = item.height() * 0.1;
                break;
            case "bottomLeft":
                extendedWidth = item.width() * 0.1;
                break;
        }
		enemyPositions.push({x:item.position().left, y:item.position().top, width:item.width()+extendedWidth, height:item.height()+extendedHeight});
	});
}

//returns true when the mouse is inside a enemy, returns false if not
function isMouseInEnemy(x,y){
	var isInEnemy = false;
	$.each(enemyPositions, function(index, item){
		if(x <= item.x+item.width && x >= item.x && y <= item.y+item.height && y >= item.y){
			isInEnemy = true;	
			return false;
		}
	});
	return isInEnemy;
}

////////////////////////////////////////////////////////////
//
//	Document.ready
//
////////////////////////////////////////////////////////////
$(document).ready(function (){
	DraggableOption.successfullyDropped = false;
	DraggableOption.draggable_player_opt = {
		appendTo: "body",
		helper: "clone",
		cursorAt: { left:25, top:25 }, //moves cursor in the middle of the unit
		revertDuration: 10,
		revert: false,
		containment: $("table#field"),
		start: function(event, ui) {
			var unit = $(this);
			//change mode of droppable from pointer to intersect
			$("#field tr td").droppable("option", "tolerance", "intersect");
			//uiHelper = td of draggable
			uiHelper = unit.parent();
			//remove filled attribute
			uiHelper.removeClass("filled");
			//hide unit, we use a clone to drop;
			unit.data("isDragged", "true");
			unit.css("display", "none");
			//add stealh abilitys
			if (unit.hasClass("gegoZ")) hasUnitStealth = true;
			if (hasUnitStealth == false) getAllEnemyPositions();
			//create new Array for movement
			currentMove = [];
			//unit may be moved
			isUnitMoveable = true;
			//reset lastAcceptedField
			lastAcceptedField = null;
			//creates blur/shadow effect around the moved unit
			ui.helper.toggleClass("dragging");
			//unitData = getAllUnitsOnTable();
			//check if the unit is dropped = false
			DraggableOption.successfullyDropped = false;
		},
		stop: function(event, ui) {
			//reset variables
			isUnitMoveable = true;
			hasUnitStealth = false;
			$(this).data("isDragged", "false");
			$(this).css("display", "block");
			//lastAcceptedField = field on that unit is dropped
			//if unit has no valid Drop, move unit to lastAcceptedField
			if (DraggableOption.successfullyDropped == false) $(this).appendTo(lastAcceptedField);
			lastAcceptedField.addClass("filled");
            ajaxUploadMovement(currentMove);
		},
		drag: function( event, ui ){
			//when unit has no Stealth -> forbid moving through enemies
			if (hasUnitStealth == false){
				//if mouse is inside enemy and unit was moveable before that
				if (isMouseInEnemy(event.pageX, event.pageY) == true){
					if (isUnitMoveable == true){
						//save last movable position of unit
						isUnitMoveable = false;
						posX = event.pageX - (event.pageX - ui.position.left);
						posY = event.pageY - (event.pageY - ui.position.top);
					}	
				}else{
					//if mouse is not in enemy But inside the unit -> enable movement again
					if(event.pageX >= posX && event.pageX <= posX+50 && event.pageY >= posY && event.pageY <= posY+50){
						isUnitMoveable = true;
					}
				}
				//if Unit stuck in enemy, disable movement
				if (isUnitMoveable == false){
					ui.position={'top': posY, 'left': posX};
				}
			}
			
			
		}
	};
	
	var helperPosition = [];
	DraggableOption.draggable_boss_opt = {
		appendTo: "body",
		helper: 'clone',
		revert: 'invalid',
		containment: $("table#field"),
		start: function(event, ui) {
			$("#field tr td").droppable("option", "tolerance", "pointer");
			var unit = $(this);
			var bossDragPart = unit.data("bossdragpart");
			switch(bossDragPart){
				case "topLeft":
					helperPosition.x = 0;
					helperPosition.y = 0;
					break;
				case "topRight":
					helperPosition.x = unit.width();
					helperPosition.y = 0;
					break;
				case "bottomLeft":
					helperPosition.x = 0;
					helperPosition.y = unit.height();
					break;
				case "bottomRight":
					helperPosition.x = unit.width();
					helperPosition.y = unit.height();
					break;
			}
			uiHelper = $(this).parent();
			ui.helper.css("height", $(this).height()*2);
			ui.helper.css("width",$(this).width()*2);
		},
		drag: function(event, ui) {
			ui.position={'top': ui.position.top - helperPosition.y, 'left': ui.position.left - helperPosition.x};
		}
	};
		
	DraggableOption.draggable_minion_opt = {
		appendTo: "body",
		helper: 'clone',
		revert: 'invalid',
        zIndex: 110,
		containment: $("table#field"),
		start: function(event, ui) {
			$("#field tr td").droppable("option", "tolerance", "pointer");
			uiHelper = $(this).parent();
		}
	};
});