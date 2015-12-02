<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR);

require_once("../dbConn.php");

	//include exceptions
require_once('exception.php');

function checkUserLogin($d){
		$username = $d["username"];
        $password = $d["password"];
        $res = array();
       
        global $mysqli;
		$sql="Select * from user where BINARY username=? and BINARY password=?";
		try {
			if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("ss",$username,$password);
				$data =  returnJson($stmt);
                
                if(!$data){
                    $res["success"]=false;
                    $res["message"]="Username/password doesnt exist.";
                }else{
                    //???TODO - check user already logged in
                    $sql="Select * from login where BINARY username=?";
                    $stmt=$mysqli->prepare($sql);
                    $stmt->bind_param("s",$username);
				    $data1 =  returnJson($stmt);
                    if($data1){
                        $res["success"]=false;
                        $res["message"]="User already logged in.";
                   }else{
                    //update login table with login details
                    //generate token
                    $tokenLength = 32;
                    $token = getToken($tokenLength);
                    
                    $sql="Insert into login(userID,username,token) values(?,?,?)";
                    if($stmt=$mysqli->prepare($sql)){
                        $d1 = $data[0];
                        $stmt->bind_param("iss",$d1["userID"],$d1["username"],$token);
                        $stmt->execute();
                         $result = mysqli_stmt_get_result($stmt);
                       //???? result is false but its inserting!
                    }
                    
                    $_SESSION['username'] = $username;
                    //$_SESSION['token'] = $token;
                    $_SESSION['userID'] = $userID;
                    header("location: chat.php");
                    $res["success"]=true;
                    }
                }
                 $stmt->close();
                 $mysqli->close();
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
	
	function saveUserDetails($d){
        $username = $d["username"];
        $firstname = $d["firstname"];
        $lastname = $d["lastname"];
        $password = $d["password"];
        
       
        try {
            global $mysqli;
            $res = array();
           //check username already exists
            $sql = "Select * from user where username=?";
            
            if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("s",$username);
                $data =  returnJson($stmt);
                if($data){
                    $res["success"]=false;
                    $res["message"]="Username already exists.Please try another username.";
                }else{ 
           
               $sql="Insert into user(firstname,lastname,username,password) values(?,?,?,?)";

                if($stmt=$mysqli->prepare($sql)){
                    $stmt->bind_param("ssss",$firstname,$lastname,$username,$password);
                    $stmt->execute();
                     $result = mysqli_stmt_get_result($stmt);
                   
                   //???? result is false but its inserting!
                    $res["success"] = true;

                   }
                }
                $stmt->close();
                $mysqli->close();
                return json_encode($res);
            }
			//echo $c;
        }catch (mysqli_sql_exception $e) {
            throw new MySQLiQueryException($SQL, $e->getMessage(), $e->getCode());
        }catch (Exception $e) {
            echo log_error($e, $sql, null);
			//return false;
			echo 'fail';
        }
		
	}

function updateUserDetails($d){
        $username = $d["username"];
        $firstname = $d["firstname"];
        $lastname = $d["lastname"];
        $password = $d["password"];
        
       
        try {
            global $mysqli;
            $res = array();
           //check username already exists
            $sql = "UPDATE user SET firstname=?,lastname=? WHERE username=?";
            
            if($stmt=$mysqli->prepare($sql)){
				$stmt->bind_param("sss",$firstname,$lastname,$username);
                $data =  returnJson($stmt);
                   //???? result is false but its inserting!
                    $res["success"] = true;
                   }else {
                $res["success"] = false;
                $res["message"] = "Error occurred while updating user.";
                    return json_encode($res);
                }
                
                $stmt->close();
                $mysqli->close();
                return json_encode($res);
            
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
function returnJson($stmt){
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
	header("Content-Type:plain/text");
	
    if(!$data){
    return false;
    }
    return $data;
}


//Token generation code - from 
//http://stackoverflow.com/questions/1846202/php-how-to-generate-a-random-unique-alphanumeric-string

function crypto_rand_secure($min, $max)
{
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd >= $range);
    return $min + $rnd;
}

function getToken($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet) - 1;
    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max)];
    }
    return $token;
}
?>