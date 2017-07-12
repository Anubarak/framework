//switches positions between units
function forceUnitMovement(unit, newField){
	newField.addClass("filled");
	var oldField = unit.parent();
	oldField.removeClass("filled");
	newField.append(unit);
	
	var tmpmover = unit.clone().appendTo("body");
	unit.css("display", "none");
	tmpmover.css("position", "absolute");
	var oldTop = oldField.position().top;
	var oldLeft = oldField.position().left;
	
	var newTop = newField.position().top;
	var newLeft = newField.position().left;
	
	var leftChange = newLeft - oldLeft;
	var topChange = newTop - oldTop;
	
	tmpmover.css({top:oldTop, left:oldLeft }).animate( {left:oldLeft + leftChange, top: oldTop + topChange}, 200, function(){
		unit.css("display", "block");
		tmpmover.remove();
	});
		
	
}

var isUnitMovePlaying = false;
function moveUnit(movementSet){
	var counter = movementSet.length;
	if (counter > 1){
        toggleDroppableState("disable");
		var firstMove = movementSet.slice(0,1);
		var firstField = $('td[data-x="' + firstMove[0].x + '"][data-y="'+firstMove[0].y+'"]'); 
		var unit = firstField.children().first();
		var x = firstMove[0].x;
		var y = firstMove[0].y;
		var currentField = $('td[data-x="' + x + '"][data-y="'+y+'"]');
		var startPos = unit.position();
		unit.appendTo('body');
		unit.css("position", "absolute");
		unit.css("top", currentField.position().top);
		unit.css("left", currentField.position().left);
		var isUnitGegoZ = unit.hasClass("gegoZ");
		var gegoZEntryField;
		var gegoZIsStealthActivated = false;
		var prevField;
        isUnitMovePlaying = true;
		$.each(movementSet, function(index, item){
			setTimeout(function(){
				prevField = currentField;
				if (index != 0){
					x = item.x;
					y = item.y;
					currentField = $('td[data-x="' + x + '"][data-y="'+y+'"]'); 
					var unitOnField = currentField.children().first();
					if (isUnitGegoZ == true){
						if (unitOnField.hasClass("draggableEnemy") && gegoZIsStealthActivated == false){
							gegoZEntryField = prevField;
							gegoZIsStealthActivated = true;
						}
						if(gegoZIsStealthActivated == true && currentField.hasClass("filled") == false) gegoZIsStealthActivated = false;
						if (unitOnField.hasClass("draggablePlayerUnit") && gegoZIsStealthActivated == true){
							prevField = gegoZEntryField;
							gegoZIsStealthActivated = false;
							
						}
						
					}
                    console.log(index);
					unit.animate( {left:currentField.position().left, top: currentField.position().top}, 200, "linear", function(){
                        console.log("finisH");
                        if (index+1 === counter) {
                            unit.appendTo(currentField);
							unit.css("position", "static");
							unit.css("left", "0");
							unit.css("top", "0");
                            isUnitMovePlaying = false;
							toggleDroppableState("enable");
						}
					});

					if (currentField.hasClass("filled") == true && unitOnField.hasClass("draggablePlayerUnit") == true){
						forceUnitMovement(unitOnField, prevField);
					}
				}
			},300 + ( index * 300 ));
		});
	}
}

////////////////////////////////////////////////////////////
//
//	Document.ready
//
////////////////////////////////////////////////////////////
$(document).ready(function (){
	
});