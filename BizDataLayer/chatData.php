<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL|E_STRICT);
mysqli_report(MYSQLI_REPORT_ERROR);

require_once("../dbConn.php");
//include exceptions
require_once('./BizDataLayer/exception.php');


function getChatUsers($d){
    $username = $d["username"];
    $res = array();
   
    global $mysqli;
    $sql="Select * from login where username!=?";
    try {
        if($stmt=$mysqli->prepare($sql)){
            $stmt->bind_param("s",$username);
            $data =  returnJson($stmt);
            $stmt->close();
            $mysqli->close();
                
            if(!$data){
                //$res["count"]=$data;
                $res["success"]=false;
                $res["message"]="No users logged in at this time.";
            }else{
                $res["success"]=true;
                $res["responseJSON"]=$data;
                return json_encode($res);
             }
            }
        }catch (mysqli_sql_exception $e) {
            throw new MySQLiQueryException($SQL, $e->getMessage(), $e->getCode());
        }catch (Exception $e) {
            echo log_error($e, $sql, null);
			//return false;
			echo 'fail';
        }
	}

function getChatData(){
 
    $res = array();
  
    global $mysqli;
    //getting the chats for todays date
    $sql="Select * from chat where chattime >= CURRENT_DATE and chattime < CURRENT_DATE + INTERVAL 1 DAY;";
    try {
        if($stmt=$mysqli->prepare($sql)){
            //$stmt->bind_param("ss",$username,$password);
            $data = returnJson($stmt);
            $stmt->close();
            $mysqli->close();
           
            if(!$data || empty($data)){
                $res["success"]=false;
                $res["message"]="No chats available";
            }else{
                $res["success"]=true;
                $res["responseJSON"]=$data;
               
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

	
function addNewChat($d){
    $res = array();
  
    global $mysqli;
    $sql="Insert into chat(userID,username,message) values(?,?,?)";
    try {
        if($stmt=$mysqli->prepare($sql)){
            $stmt->bind_param("sss",$d["userID"],$d["username"],$d["chatText"]);
            $stmt->execute();
            $result = mysqli_stmt_get_result($stmt);
           
            $stmt->close();
            $mysqli->close();
           
            $res["success"]=true;
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


function challengeUserGame($d){
    $res = array();
    $fromName = $d["fromName"];
    $toName = $d["toName"];
    $fromID = intVal($d["fromID"],10);
    $toID = intVal($d["toID"],10);
    
    global $mysqli;
    $sql="Insert into challenge(fromID,toID,fromName,toName) values(?,?,?,?)";
    try {
        if($stmt=$mysqli->prepare($sql)){
            $stmt->bind_param("iiss",$fromID,$toID,$fromName,$toName);
             $stmt->execute();
            $result = mysqli_stmt_get_result($stmt);
           
           
            $stmt->close();
            $mysqli->close();
           
            $res["success"]=true;
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

function saveChallengeStatusGame($d){
   $res = array();
   $challengeID = intVal($d["challengeID"],10);
  // $toID = intVal($d["toID"],10);
   $accepted = filter_var($d["accepted"], FILTER_VALIDATE_BOOLEAN); 
    
    global $mysqli;
    $sql="Update challenge set accepted=? where challengeID=?";
    try {
        if($stmt=$mysqli->prepare($sql)){
            $stmt->bind_param("ii",$accepted,$challengeID);
            $stmt->execute();
            $result = mysqli_stmt_get_result($stmt);
           
            $stmt->close();
            $mysqli->close();
            
           if($accepted){
                $res["success"]=true;
               $res["accepted"]=true;
                $res["responseText"]=$d;
            }
        }
        return json_encode($res);
        }catch (mysqli_sql_exception $e) {
            throw new MySQLiQueryException($SQL, $e->getMessage(), $e->getCode());
        }catch (Exception $e) {
            echo log_error($e, $sql, null);
			//return false;
			echo 'fail';
        }
	}

function checkChallengeGame($d){
 
    $res = array();
  $userID = intVal($d["userID"],10);
    global $mysqli;
    //getting the chats for todays date
    $sql="Select * from challenge where toID=? and accepted IS NULL;";
    try {
        if($stmt=$mysqli->prepare($sql)){
            $stmt->bind_param("i",$userID);
            $data = returnJson($stmt);
            $stmt->close();
            $mysqli->close();
           
            if(!$data || empty($data)){
                $res["success"]=false;
                $res["message"]="No challenges available";
            }else{
                $res["success"]=true;
                $res["responseJSON"]=$data;
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

function checkChallengeAcceptedGame($d){
 
    $res = array();
  
    global $mysqli;
    //getting the chats for todays date
    $sql="Select * from challenge where fromID=? and accepted=true;";
    try {
        if($stmt=$mysqli->prepare($sql)){
            $stmt->bind_param("i",$d["userID"]);
            $data = returnJson($stmt);
            $stmt->close();
            $mysqli->close();
           
            if(!$data || empty($data)){
                $res["success"]=false;
                $res["message"]="No challenges accepted.";
            }else{
                $res["success"]=true;
                $res["responseJSON"]=$data;
                
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
	header("Content-Type:text/plain");
	
    return $data;
}
?>