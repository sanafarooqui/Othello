////ajax util/////
//d is data sent, looks like {name:value,name2:val2}
////////////////
function ajaxCall(GetPost,d,callback){
	$.ajax({
 		type: GetPost,
 		async: true,
  		cache:false,
  		url: "../mid.php",
  		data: d,  
  		dataType: "json",
  		success: callback
	});
}
////initGameAjax/////
//d is data sent, looks like {name:value,name2:val2}
//this is my starter call
//goes out and gets all pertinant information about the game (FOR ME)
//callback is callbackInit()
////////////////
function initGameAjax(whatMethod,val){
    console.log("initGameAjax");
	//data is gameId
	ajaxCall("POST",{method:whatMethod,a:"game",data:val},callbackInit);
}
////callbackInit/////
//callback for initGameAjax
////////////////
function callbackInit(jsonObj){
	//compare the session name to the player name to find out my playerId;
    console.log("callbackInit");
    console.log(jsonObj);
	turn = jsonObj[0].turn;
    
	if(currentPlayer == jsonObj[0].player0){
		opponentPlayer = jsonObj[0].player1;
		playerId = 0;
	}else{
		opponentPlayer = jsonObj[0].player0;
		playerId = 1;
	}
	//document.getElementById('output2').firstChild.data='playerId '+playerId+ ' turn '+turn;
	//start building the game (board and piece)
    gameInit(); 
}
////changeServerTurnAjax/////
//change the turn on the server
//no callback
////////////////
function changeServerTurnAjax(whatMethod,val){
	ajaxCall("POST",{method:whatMethod,a:"game",data:val},null);
	//change the color of the names to be the other guys turn
	document.getElementById('youPlayer').setAttributeNS(null,'fill',"black");
	document.getElementById('opponentPlayer').setAttributeNS(null,'fill',"orange");
}
////changeBoardAjax/////
//change the board on the server
//no callback
////////////////
function changeBoardAjax(pieceId,boardI,boardJ,whatMethod,val){
	//data: gameId~pieceId~boardI~boardJ~playerId
	ajaxCall("POST",{method:whatMethod,a:"game",data:val+"~"+pieceId+"~"+boardI+"~"+boardJ+"~"+playerId},null);
}
////checkTurnAjax/////
//check to see whose turn it is
//callback is callbackcheckTurn
////////////////
function checkTurnAjax(whatMethod,val){
	if(turn!=playerId){
		ajaxCall("GET",{method:whatMethod,a:"game",data:val},callbackcheckTurn);
	}
	setTimeout(function(){checkTurnAjax('checkTurn',gameId)},3000);
}
////callbackcheckTurn/////
//callback for checkTurnAjax
////////////////
function callbackcheckTurn(jsonObj){
	if(jsonObj[0].turn == playerId){
		//switch turns
		turn=jsonObj[0].turn;
		//get the data from the last guys move
		getMoveAjax('getMove',gameId);
	}
}
////checkTurnAjax/////
//get the last move
//-called after I find out it is my turn
//callback is callbackGetMove
////////////////
function getMoveAjax(whatMethod,val){
	ajaxCall("GET",{method:whatMethod,a:"game",data:val},callbackGetMove);
}
////callbackGetMove/////
//callback for getMoveAjax
////////////////
function callbackGetMove(jsonObj){
	//tests to see what I'm getting back!
	//alert(jsonObj[0]['player'+Math.abs(playerId-1)+'_pieceID']);
    //alert(jsonObj[0]['player'+Math.abs(playerId-1)+'_boardI']);
    //alert(jsonObj[0]['player'+Math.abs(playerId-1)+'_boardJ']);
    
    //change the text output on the side for whose turn it is
	//var hold='playerId '+playerId+ ' turn '+turn;
	//document.getElementById('output2').firstChild.data=hold;
	
	//change the color of the names for whose turn it is:
	document.getElementById('youPlayer').setAttributeNS(null,'fill',"orange");
	document.getElementById('opponentPlayer').setAttributeNS(null,'fill',"black");
	
	//make the other guys piece move to the location
	//first, clear the other guy's cell
	var toMove=getPiece(jsonObj[0]['player'+Math.abs(playerId-1)+'_pieceID']);
	toMove.current_cell.notOccupied();
	//now, actually move it! 
	var x=boardArr[jsonObj[0]['player'+Math.abs(playerId-1)+'_boardI']][jsonObj[0]['player'+Math.abs(playerId-1)+'_boardJ']].getCenterX();
	var y=boardArr[jsonObj[0]['player'+Math.abs(playerId-1)+'_boardI']][jsonObj[0]['player'+Math.abs(playerId-1)+'_boardJ']].getCenterY();
	setTransform(jsonObj[0]['player'+Math.abs(playerId-1)+'_pieceID'],x,y);
		
	//now, for me, make the new cell occupied!
	//Piece.prototype.changeCell = function(newCell,row,col){
	getPiece(jsonObj[0]['player'+Math.abs(playerId-1)+'_pieceID']).changeCell('cell_'+jsonObj[0]['player'+Math.abs(playerId-1)+'_boardI']+jsonObj[0]['player'+Math.abs(playerId-1)+'_boardJ'],jsonObj[0]['player'+Math.abs(playerId-1)+'_boardI'],jsonObj[0]['player'+Math.abs(playerId-1)+'_boardJ']);
}











