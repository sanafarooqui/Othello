<?php 
ob_start();
session_start();
?>
<!doctype html>
<html lang='en'>
<head>
  <meta charset='utf-8' />
  <title>Login Page</title>
 <script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript">
    
$(document).ready(function(){
    // Stop the browser from submitting the form.
    $('form').submit(function(event) {
        event.preventDefault();
    });
 }); 
    
    function validateLogin(){
     var rex_name = /^[A-Za-z ]{3,20}$/,
     rex_lastname = /^[A-Za-z ]{3,30}$/;
        
     var errorMsg="";    
     var uname = $('#username').val(),
         pass =$('#password').val();
            
    if(!uname){
     errorMsg += "Please enter your username.</br>";   
     }
            
    if(!pass){
     errorMsg += "Please enter your password.</br>";   
    }
            
    if(errorMsg){
        $('#errorDiv').text(errorMsg);
      return false;
    }else{
         //check in db to see if username/pass exist
     ajaxCall("POST",{method:"loginUser",
                        a:"login",
                        data:{"username":uname,"password":pass}
                       },
                 callbackloginUser);
     }  
    return true;
 }

function ajaxCall(GetPost,d,callback){
    $.ajax({
  		type: GetPost,
  		async: true,
  		cache:false,
  		url: "../mid.php",
  		data: d,  
  		dataType: "json",
  		success: callbackloginUser
    });
}
    
function callbackloginUser(data, status){
    
    if(data.success){
      window.location.href = "../chat/chat.php";
    }else{
        $('#errorDiv').text(data.message);
    }
   
} 
        
    </script>
</head>
<body>
   
    <form id="login-form" method="post" name="login-form">
    Username: <input type="text" name="username" id="username" ><br/>
    Password: <input type="password" name="password" id="password" ><br/>
        <br/>
        <input type="submit" name="submit" value="Submit" onclick="return validateLogin();">
    </form>
    <a href="signup.php">Sign Up</a>
   <div id="errorDiv"></div>
    
</body>
</html>