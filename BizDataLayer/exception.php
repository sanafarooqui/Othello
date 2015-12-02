<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
  // echo "<br/> in exception.php <br/>";

/*$dir = 'myDir';

 // create new directory with 744 permissions if it does not exist yet
 // owner will be the user/group the PHP script is run under
 if ( !file_exists($dir) ) {
     $oldmask = umask(0);  // helpful when used in linux server  
     mkdir ($dir, 0744);
 } 

 file_put_contents ($dir.'/dataerror.log', 'Hello File');*/

function log_error($e, $sqlst, $params){
   /* $myFile = "dataerror.log";
	$fh = fopen("dataerror.log", 'a+') or die("can't open file");
	try{
   		fwrite($fh, "Exception caught @".date("H:i:s m.d.y")."\n"); 
		fwrite($fh, "    Message: ".$e->getMessage()."\n");
        fwrite($fh, "    SQL: ".$sqlst."\n");
		if (is_array($params))
            fwrite($fh, "    Params: ".implode(",",$params)."\n");
        fwrite($fh, "    File: ".$e->getFile()."\n");
        fwrite($fh, "    Line: ".$e->getLine()."\n");
        fwrite($fh, "    Trace: ".$e->getTraceAsString()."\n");
        fclose($fh);
	}catch (Exception $e) {
		echo 'error';
    }*/
}
?>