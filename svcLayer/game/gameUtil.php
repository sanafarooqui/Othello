<?php
//What do we do here?
//Check if they should be here!
//if so - prepare data and make call to data/biz layer

//error_reporting (E_ALL);
require "./BizDataLayer/gameData.php";
//Why include the database stuff here?  (not doing any db stuff in the service layer!)
//because it forces all to go through the service layer in order to get to the bizLayer
//if someone tries to access the bizLayer on it's own the code will fail since there isn't a connection!


function playGame($d){

	return playGameData($d);
}
/*************************
	start
	takes: 		gameId
	uses in bizLayer: gameBiz.php->startData
	returns:	gameInfo
				[{"game_id":38,"whoseTurn":1,"player0_name":"Dan","player0_pieceID":null,"player0_boardI":null,"player0_boardJ":null,"player1_name":"Fred","player1_pieceID":null,"player1_boardI":null,"player1_boardJ":null,"last_updated":"0000-00-00 00:00:00"}]
*/
function start($d){
	//Should they be here?  (check)
	//if true:
	return startData($d);
}
/*************************
	changeTurn
	takes: gameId
	uses in bizLayer: gameBiz.php->changeTurnData
	returns:	Nothing
*/
function changeTurn($d){
	//can they change the turn?
	//if true:
	changeTurnData($d);
}
/*************************
	checkTurn
	takes: gameId
	uses in bizLayer: gameBiz.php->checkTurnData
	returns:	whoseTurn
				[{"whoseTurn":1}]
*/
function checkTurn($d){
	//Can they check is it my turn yet?
	//if true:
	return checkTurnData($d);
	
}
/*************************
	changeBoard
	takes: gameId~pieceId~boardI~boardJ~playerId
	uses in bizLayer: gameBiz.php->changeBoardData
	returns:	Nothing
*/
function changeBoard($d){
	//can they change the board?
	//if true:
	//split the data  //data: gameId~pieceId~boardI~boardJ~playerId
							//38~piece_1|10~4~6~1
	$h=explode('~',$d);
	//changeBoardData($gameId,$pieceId,$boardI,$boardJ,$playerId);
	changeBoardData($h[0],$h[1],$h[2],$h[3],$h[4]);
}
/*************************
	getMove
	takes: gameId
	uses in bizLayer: gameBiz.php->getMoveData
	returns:	gameInfo
				[{"game_id":38,"whoseTurn":1,"player0_name":"Dan","player0_pieceID":"piece_0|10","player0_boardI":"6","player0_boardJ":"2","player1_name":"Fred","player1_pieceID":"piece_1|3","player1_boardI":"0","player1_boardJ":"2","last_updated":"0000-00-00 00:00:00"}]
*/
function getMove($d){
	//if it is my turn and I should be here, get the other players move	
	return getMoveData($d);
}


/*function checkTurn($d,$ip,$token){
	//check the token, should they be here?
	
	//if so...
	//split the data?  $d=gameId|userId
	$h=explode('|',$d);
	$gameId=$h[0];
	$userId=$h[1];
	
	//go to the data layer and actually get the data I want
	require_once('BizDataLayer/checkTurn.php');
	echo(checkTurnData($gameId,$userId));
}

function changeTurn($d,$ip,$token){
	//check the token, should they be here?
	//change the turn.... (when?)
	
	//if so...
	//split the data?  $d=gameId|userId
	$h=explode('|',$d);
	$gameId=$h[0];
	$userId=$h[1];
	
	//go to the data layer and actually get the data I want
	require_once('BizDataLayer/changeTurn.php');
	changeTurnData($gameId,$userId);//would change the turn in the db
	
	//now what?
	checkTurn($d,$ip,$token);
}*/
?>