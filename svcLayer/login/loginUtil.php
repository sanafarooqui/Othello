<?php
//ALL chat goes in this folder
require_once('./BizDataLayer/loginData.php');

function loginUser($d){
  
	//are they someone who should be able to do this?  (check token, ip, etc)
	//split $d - would probably hold null if just looking for latest chat, $userId|$message if a new message...  (split like we are doing in game)
	
	//go to the data layer and actually get the data I want
	return checkUserLogin($d);
}

function saveUser($d,$ip,$token){
	
	return saveUserDetails($d);
}

function updateUser($d,$ip,$token){
	
	return updateUserDetails($d);
}



?>