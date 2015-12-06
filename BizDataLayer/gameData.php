<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL|E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR);


require_once("../dbConn.php");
//include exceptions
require_once('exception.php');

//if we have gotten here - we know:
//-they have permissions to be here
//-we are ready to do something with the database
//-method calling these are in the svcLayer
//-method calling specific method has same name droping 'Data' at end checkTurnData() here is called by checkTurn() in svcLayer

//remember that dbInfoPS.inc looks like:
/*
$mysqli=new mysqli("localhost","yourUsername","yourPass",'yourUsername');             
if(mysqli_connect_errno()){
	printf("connection failed: ",mysqli_connect_errno());
	exit();
}
*/

/*************************
	 game
	
*/
function initialGameData($d){
	global $mysqli;
     $res = array();
    
   /* echo $d["playerID0"];
     echo $d["playerID1"];
     echo $d["player0"];
     echo $d["player1"]; */
     $challengeId = intVal($d["challengeId"],10);
    $playerID0 = intVal($d["fromID"],10);
    $playerID1 = intVal($d["toID"],10);
    $color0 = 'white';
    $color1 = 'black';
     $turn = 0;
     $score0 = 0;
     $score1 = 0;

	$sql = "INSERT INTO game(challengeId,playerID0,playerID1,player0,player1,color0,color1,turn,score0,score1) values(?,?,?,?,?,?,?,?,?,?)";
	try{
		if($stmt=$mysqli->prepare($sql)){
		   // 
			$stmt->bind_param("iiissssiii",$challengeId,$playerID0,$playerID1,$d["fromName"],$d["toName"],$color0,$color1,$turn,$score0,$score1);
            $stmt->execute();
            
            $result = mysqli_stmt_get_result($stmt);
           // echo "<br> result login insert <br/>";
            $gameid=$mysqli->insert_id;
            
            $stmt->close();
            $mysqli->close();
           
             $res["success"]=true;
               $res["gameID"]=$gameid;
            $res["responseText"]=$d;
		}else{
             $res["success"]=false;
        }
         return json_encode($res);
	}catch (mysqli_sql_exception $e) {
            throw new MySQLiQueryException($SQL, $e->getMessage(), $e->getCode());
    }catch (Exception $e) {
         echo "ex: ".$e; 
       // log_error($e, $sql, null);
		return false;
    }
}

function joinGameChallenge($d){
    $fromID = intVal($d["fromID"],10);
    $challengeId = intVal($d["challengeId"],10);
    $res = array();
  
    global $mysqli;
    //getting the chats for todays date
    $sql="Select gameID from game where challengeId=? and playerID0=?;";
    try {
        if($stmt=$mysqli->prepare($sql)){
            $stmt->bind_param("ii",$challengeId,$fromID);
            $data = returnJson($stmt);
            $stmt->close();
            $mysqli->close();
           
            if(!$data || empty($data)){
                $res["success"]=false;
                $res["message"]="No challenges accepted.";
            }else{
                $res["success"]=true;
                $res["response"]=$data;
            }
             return json_encode($res);
        }
        }catch (mysqli_sql_exception $e) {
            throw new MySQLiQueryException($SQL, $e->getMessage(), $e->getCode());
        }catch (Exception $e) {
            echo log_error($e, $sql, null);
			//return false;
			echo 'fail';
        }
	}

/*************************
	startData
	
*/
function layoutInitialGame1($gameId){
	global $mysqli;
  
    //get the init of the game
	$sql = "SELECT * FROM game WHERE gameID=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			//bind parameters for the markers (s - string, i - int, d - double, b - blob)
			$stmt->bind_param("i",$gameId);
			$data=returnJson($stmt);
			$mysqli->close();
            
			return json_encode($data);
		}else{
            throw new Exception("An error occurred while fetching record data");
        }
	}catch (Exception $e) {
        echo $e;
        log_error($e, $sql, null);
		return false;
    }
}
/*************************
	checkTurnData
*/
function checkTurnData($gameId){
	global $mysqli;
	$sql="SELECT turn FROM game WHERE gameID=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("i",$gameId);
			$data=returnJson($stmt);
			$mysqli->close();
			return $data;
		}else{
        	throw new Exception("An error occurred while checking turn");
        }
    }catch (Exception $e) {
        log_error($e, $sql, null);
		return false;
    }
}
/*************************
	changeTurnData
*/
function changeTurnData($gameId){
	global $mysqli;
	//ugly, but toggle the turn (if the turn was 0, then make it 1, else make it 0)
	try{
		if($stmt=$mysqli->prepare("UPDATE checkers_games SET whoseTurn='2' WHERE game_id=? AND whoseTurn='0'")){
			$stmt->bind_param("i",$gameId);
			$stmt->execute();
			$stmt->close();
		}else{
        	throw new Exception("An error occurred while changing turn, step 1");
        }
		if($stmt=$mysqli->prepare("UPDATE checkers_games SET whoseTurn='0' WHERE game_id=? AND whoseTurn='1'")){
			$stmt->bind_param("i",$gameId);
			$stmt->execute();
			$stmt->close();
		}else{
        	throw new Exception("An error occurred while changing turn, step 2");
        }
		if($stmt=$mysqli->prepare("UPDATE checkers_games SET whoseTurn='1' WHERE game_id=? AND whoseTurn='2'")){
			$stmt->bind_param("i",$gameId);
			$stmt->execute();
			$stmt->close();
		}else{
        	throw new Exception("An error occurred while changing turn, step 3");
        }
	}catch (Exception $e) {
        log_error($e, $sql, null);
		return false;
    }
	$mysqli->close();
}
/*************************
	changeBoardData
*/
function changeBoardData($gameId,$pieceId,$boardI,$boardJ,$playerId){
	//update the board
	global $mysqli;
	$sql="UPDATE checkers_games SET player".$playerId."_pieceId=?, player".$playerId."_boardI=?, player".$playerId."_boardJ=? WHERE game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("siii",$pieceId,$boardI,$boardJ,$gameId);
			$stmt->execute();
			$stmt->close();
		}else{
        	throw new Exception("An error occurred while changeBoard");
        }
	}catch (Exception $e) {
        log_error($e, $sql, null);
		return false;
    }
	$mysqli->close();
}
/*************************
	getMoveData
*/
function getMoveData($gameId){
	global $mysqli;
	$sql="SELECT * FROM checkers_games WHERE game_id=?";
	try{
		if($stmt=$mysqli->prepare($sql)){
			$stmt->bind_param("i",$gameId);
			$data=returnJson($stmt);
			$mysqli->close();
			return $data;
		}else{
			throw new Exception("An error occurred while getMoveData");
		}
	}catch (Exception $e) {
        log_error($e, $sql, null);
		return false;
    }
}
/*********************************Utilities*********************************/
/*************************
	returnJson
	takes: prepared statement
		-parameters already bound
	returns: json encoded multi-dimensional associative array
*/
function returnJson ($stmt){
	$stmt->execute();
	$stmt->store_result();
 	$meta = $stmt->result_metadata();
    $bindVarsArray = array();
	//using the stmt, get it's metadata (so we can get the name of the name=val pair for the associate array)!
	while ($column = $meta->fetch_field()) {
    	$bindVarsArray[] = &$results[$column->name];
    }
	//bind it!
	call_user_func_array(array($stmt, 'bind_result'), $bindVarsArray);
	//now, go through each row returned,
	while($stmt->fetch()) {
    	$clone = array();
        foreach ($results as $k => $v) {
        	$clone[$k] = $v;
        }
        $data[] = $clone;
    }
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	//MUST change the content-type
	header("Content-Type:plain/text");
	// This will become the response value for the XMLHttpRequest object
    return $data;
}
?>