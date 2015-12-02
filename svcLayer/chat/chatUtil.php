<?php
require_once('./BizDataLayer/chatData.php');

function chatData($d,$ip,$token){
	
	return getChatData();
}

function chatUsers($d,$ip,$token){
	
	return getChatUsers($d);
}

function addChat($d,$ip,$token){
	//are they someone who should be able to do this?  (check token, ip, etc)
	//split $d - would probably hold null if just looking for latest chat, $userId|$message if a new message...  (split like we are doing in game)
	
	return addNewChat($d);
}

?>