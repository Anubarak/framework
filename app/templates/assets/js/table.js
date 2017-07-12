var gameData = [];

var tableData = [
	{x:0, y:0, class:"droppableBossArea"},
	{x:1, y:0, class:"droppableBossArea"},
	{x:2, y:0, class:"droppableBossArea"},
	{x:3, y:0, class:"droppableBossArea"},
	{x:4, y:0, class:"droppableBossArea"},
	{x:5, y:0, class:""},
	
	
	{x:0, y:1, class:"droppableBossArea"},
	{x:1, y:1, class:"droppableBossArea"},
	{x:2, y:1, class:"droppableBossArea"},
	{x:3, y:1, class:"droppableBossArea"},
	{x:4, y:1, class:"droppableBossArea"},
	{x:5, y:1, class:""},
	
	{x:0, y:2, class:"droppableBossArea"},
	{x:1, y:2, class:"droppableBossArea"},
	{x:2, y:2, class:"droppableBossArea"},
	{x:3, y:2, class:"droppableBossArea"},
	{x:4, y:2, class:"droppableBossArea"},
	{x:5, y:2, class:""},
	
	{x:0, y:3, class:"droppableBossArea"},
	{x:1, y:3, class:"droppableBossArea"},
	{x:2, y:3, class:"droppableBossArea"},
	{x:3, y:3, class:"droppableBossArea"},
	{x:4, y:3, class:"droppableBossArea"},
	{x:5, y:3, class:""},
	
	{x:0, y:4, class:"droppableBossArea"},
	{x:1, y:4, class:"droppableBossArea"},
	{x:2, y:4, class:"droppableBossArea"},
	{x:3, y:4, class:"droppableBossArea"},
	{x:4, y:4, class:"droppableBossArea"},
	{x:5, y:4, class:""},
	
	{x:0, y:5, class:"droppableBossArea"},
	{x:1, y:5, class:"droppableBossArea"},
	{x:2, y:5, class:"droppableBossArea"},
	{x:3, y:5, class:"droppableBossArea"},
	{x:4, y:5, class:"droppableBossArea"},
	{x:5, y:5, class:""},
	
	{x:0, y:6, class:"droppableBossArea"},
	{x:1, y:6, class:"droppableBossArea"},
	{x:2, y:6, class:"droppableBossArea"},
	{x:3, y:6, class:"droppableBossArea"},
	{x:4, y:6, class:"droppableBossArea"},
	{x:5, y:6, class:""},
	
	{x:0, y:7, class:""},
	{x:1, y:7, class:""},
	{x:2, y:7, class:""},
	{x:3, y:7, class:""},
	{x:4, y:7, class:""},
	{x:5, y:7, class:""}
];

var unitData = [
	/*{x:0, y:0, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"},
	{x:1, y:0, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"},
	{x:2, y:0, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"},
	{x:3, y:0, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"},
	{x:0, y:1, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"},
	{x:1, y:1, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"},
	{x:2, y:1, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"},
	{x:3, y:1, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"},
	{x:0, y:2, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"},
	{x:1, y:2, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"},
	{x:2, y:2, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"},
	{x:3, y:2, classes: "draggablePlayerUnit ui-draggable playerUnitOnField", unitID:"draggableUnit"}*/
];

////////////////////////////////////////////////////////////////////////////
//
//
//	Tableactions
//
//	createTable
//	createUnitsOnTable
//	getAllUnitsOnTable
//	getAllSavedGames
//	
//
//
////////////////////////////////////////////////////////////////////////////


//create the Table
function createTable(tableData){
	var counter = 0;
	var table = $("#field");
	var htmlCode = "";
	$.each(tableData, function(index, item){
		if (counter == 6){
			htmlCode += "</tr>";
			counter = 0;
		}
		if (counter == 0){
			htmlCode += "<tr>";
		}
		htmlCode += "<td data-x='"+ item.x +"' data-y='" + item.y + "' class='" + item.class + "'></td>";
		counter++;
	});
	table.empty();
	table.append(htmlCode);
	$("#field tr td").droppable(droppable_opt);
}

//create Units on Table
function createUnitsOnTable(unitData){
	var table = $("#field");
    var bossDragPart = ["topLeft", "topRight", "bottomLeft", "bottomRight"];
    var bossDragPartCounter = 0;
    var isFieldDroppable =  $('td[data-x="0"][data-y="0"]').droppable( "option", "disabled" );
	$.each(unitData, function(index, item){
		var x = parseInt(item.x);
		var y = parseInt(item.y);
		var field = $('td[data-x="' + x + '"][data-y="' + y + '"]');
		var unit = $("<div class='" + item.classes + "' id='" + item.unitID + "'></div>");
		unit.height(50);
		unit.width(50);
		unit.appendTo(field);
		field.addClass("filled");
		var draggableOptions = unit.attr("id");
		switch(draggableOptions){
			case "draggableMinion":
				unit.draggable(DraggableOption.draggable_minion_opt);
				break;
				
			case "draggableBoss":
                unit.data("bossdragpart", bossDragPart[bossDragPartCounter]);
                bossDragPartCounter++;
                if (bossDragPartCounter == 3) bossDragPartCounter = 0;
				unit.draggable(DraggableOption.draggable_boss_opt);
				break;
				
			case "draggableUnit":
				unit.draggable(DraggableOption.draggable_player_opt);
				break;
		}
        if (isFieldDroppable == false) unit.draggable("disable");
	});
    return true;
}
var isUnitDraggable = false;
function ajaxUploadMovement(moveData){
    game_id = gameData.game_id; //0 if not exist
    if(game_id != 0){
        $.ajax({
            type: "POST",
            url: "index.php?page=ajaxCallInsertMovement&game_id=" + game_id+"&renderOnlyContent=true",
            data: {
                moveData: moveData
            },
            success: function (data) {
                gameData.ownTurn_id = data;
                gameData.turn_id = data;
                ownMove = moveData;
                $("#publishTurn").hide().show("explode", 500);
                toggleDroppableState("disable", "disable");
                $("#enableMovement").show("explode", 500);
                isUnitDraggable = false;
            }
        });
    }
}

function ajaxDownloadMovement(){
    game_id = gameData.game_id; //0 if not exist
    turn_id = gameData.turn_id; //0 if not exist
    if (game_id != 0 && turn_id != 0){
        $.ajax({
            type: "POST",
            url: "index.php?page=ajaxCallGetMovement&game_id=" + game_id+"&turn_id="+ turn_id +"&renderOnlyContent=true",
            dataType:"json",
            data: {
            },
            success: function (data) {
                currentMove = data;
                console.log("movement downloaded")
                moveUnit(currentMove);
            }
        });
    }
}

function getAllUnitsOnTable(){
	var allUnits = [];
	var selector = $("#field tr td div");
	$.each(selector, function(index, item){
		var field = $(item).parent();
		var x = field.data("x");
		var y = field.data("y");
		var classList = $(item)[0].classList.value;
		allUnits.push({x:x, y:y, classes:classList, unitID:item.id});
	});
	return allUnits;
}

var ajaxUploadUnitsOnTable = function(savedGame){
    var game_id = gameData.game_id; //0 if game does not exist -> create new game
    $body.addClass("loading");
    return $.ajax({
        type: "POST",
        url: "index.php?page=ajaxCallInsertAllUnitsFromGame&game_id=" + game_id+"&renderOnlyContent=true",
        data: {
            unitData: savedGame
        },
        success: function (data) {
            gameData.game_id = data; //returns game_id;
            $body.removeClass("loading");
        },
        error: function (data){
            console.log(data);
			$body.removeClass("loading");
        }
    });
}

var savedGame = unitData;
function ajaxDownloadUnitsFromGame(){
    game_id = gameData.game_id; //0 if no data to download
    if (game_id != 0){
        $body.addClass("loading");
        return $.ajax({
            type: "POST",
            url: "index.php?page=ajaxCallGetAllUnitsFromGame&game_id=" + game_id+"&renderOnlyContent=true",
            dataType:"json",
            /*data: {
             unitData: savedGame
             },*/
            success: function (data) {
                savedGame = data;
                $body.removeClass("loading");
                createUnitsOnTable(savedGame);
                console.log("Unit download finished");
            }
        });
    }
}

var savedGameString = "";
function ajaxGetAllSavedGames(){
	//do some funny Ajax stuff here
    var game_id = gameData.game_id; //0 if no data to download
    if (game_id != 0) {
        $.ajax({
            type: "POST",
            url: "index.php?page=ajaxCallGetAllTurnsFromGame&game_id=" + game_id + "&renderOnlyContent=true",
            contentType: "application/json; charset=utf-8",
            //dataType: "json",
            data: {},
            success: function (data) {
                //if (savedGameString != data){
                    savedGameString = data;
                    $("#containerGameFiles").show().html(data);
                //}
            }
        });
    }
}

function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

function disableDraggable(){
    $("#unitSelector div").draggable("option", "disable");
    $("#unitSelection").hide("explode", 500);
}

////////////////////////////////////////////////////////////
//
//	Document.ready
//
////////////////////////////////////////////////////////////
$(document).ready(function (){
    $body = $("body");
	createTable(tableData);
	//createUnitsOnTable(unitData);
	gameData.game_id = (getUrlParameter("game_id") === undefined)? 0 : getUrlParameter("game_id");
	gameData.turn_id = (getUrlParameter("turn_id") === undefined)? 0 : getUrlParameter("turn_id");
    var timerReloadTurns = null;
    if( gameData.game_id == 0){
        $("#containerGameFiles").hide();
        $("#publishTurn").hide();
        $("#replayMove").hide();
        $("#enableMovement").hide();
        gameData.gameURL = null;
    }else{
        gameData.gameURL = window.location.origin + window.location.pathname + "index.php?page=game&game_id="+gameData.game_id;
        timerReloadTurns  = setInterval(ajaxGetAllSavedGames, 10000);
        $("#publishGame").hide();
        disableDraggable();
        ajaxGetAllSavedGames();
        $.when(ajaxDownloadUnitsFromGame()).done(function(){
            console.log("start Download Movement");
            if (gameData.turn_id != 0){
                ajaxDownloadMovement();
                $("#replayMove").show("explode", 500);
            }
        });
    }
	
    $("#publishContainer").hide();
    if(gameData.turn_id != 0){
        toggleDroppableState("disable", "disable");
    }else{
        $("#publishTurn").hide();
        $("#replayMove").hide();
    }

	$("#publishGame").click(function(){
		currentMove = [];
		savedGame = getAllUnitsOnTable();
        if (timerReloadTurns != null) clearInterval(timerReloadTurns);
        $.when(ajaxUploadUnitsOnTable(savedGame)).done(function() {
            var gameUrl = window.location.origin + window.location.pathname + "?page=game&game_id="+gameData.game_id;
            gameData.gameURL = gameUrl;
            disableDraggable();
            $("#publishContainer").hide().show("explode", 500);
            $("#inputPublishString").val(gameUrl);
            ajaxGetAllSavedGames();
            timerReloadTurns  = setInterval(ajaxGetAllSavedGames, 10000);
            $("#saveGame").text("Reupload units");
            toggleDroppableState("enable", "enable");

            $("#moveUnit").fadeIn();
            $("#replayMove").hide("explode", 500);
            $("#enableMovement").hide("explode", 500);
            $("#publishGame").hide();
            //$("#inputPublishString").disable();
        });
        return false;
	});

    $("#replayMove").click(function(){
        if (savedGame != null && currentMove != null && isUnitMovePlaying == false && isUnitDraggable == false){
            createTable(tableData);
            createUnitsOnTable(savedGame);
            moveUnit(currentMove);
        }
        return false;
    });


	$("#moveUnit").click(function(){
        if (isUnitMovePlaying == false){
            createTable(tableData);
            createUnitsOnTable(savedGame);
            isUnitDraggable = true;
            ajaxDownloadMovement();
        }
        return false;
	});

    $("#publishTurn").click(function(){
        if (isUnitMovePlaying == false) {
            var turnUrl = window.location.origin + window.location.pathname + "?page=game&game_id=" + gameData.game_id + "&turn_id=" + gameData.turn_id;
            gameData.turnUrl = turnUrl;
            $("#publishContainer").hide().show("explode", 500);
            $("#inputPublishString").val(turnUrl);
        }
        return false;
    });

	$("#containerGameFiles").on('click', 'a' , function(event) {
        if (isUnitMovePlaying == false && isUnitDraggable == false) {
            if (gameData.turn_id != event.target.dataset.turnid) {
                gameData.turn_id = event.target.dataset.turnid;
                createTable(tableData);
                $.when(ajaxDownloadUnitsFromGame()).done(function () {
                    ajaxDownloadMovement();
                    toggleDroppableState("disable", "disable");
                    $("#replayMove").show("explode", 500);
                    $("#publishTurn").show("explode", 500);
                    $("#publishContainer").hide();
                    $("#inputPublishString").val("");
                });
            } else {
                createTable(tableData);
                createUnitsOnTable(savedGame);
                moveUnit(currentMove);
            }
        }
		return false;
	});

    $("#enableMovement").click(function(){
        if (isUnitMovePlaying == false){
            createTable(tableData);
            isUnitDraggable = true;
            $.when(ajaxDownloadUnitsFromGame()).done(function(){
                $("#enableMovement").hide();
                toggleDroppableState("enable", "enable", "playerUnitOnField");
            });
        }
        return false;
    });

    /*$(document).on({
        ajaxStart: function() { $body.addClass("loading");},
        ajaxStop: function() { $body.removeClass("loading"); }
    });*/
});