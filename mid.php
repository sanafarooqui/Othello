<?php
session_start();
ini_set('display_errors', 'On');
error_reporting(E_ALL| E_STRICT );

require_once('./BizDataLayer/exception.php');

	if(isset($_REQUEST['method'])){
		//include all files for needed area (a)
		foreach (glob("./svcLayer/".$_REQUEST['a']."/*.php") as $filename){
			include $filename;
		}
		$serviceMethod=$_REQUEST['method'];
        $data=$_REQUEST['data'];
        
		$result=@call_user_func($serviceMethod,$data,$_SERVER['REMOTE_ADDR'],$_COOKIE['token']);
      
		if($result){
			//might need the header cache stuff
			echo $result;
		}
    }
 
?>