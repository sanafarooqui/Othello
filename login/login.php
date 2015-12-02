<?php 
ob_start();
session_start();
?>
<!doctype html>
<html lang='en'>
<head>
  <meta charset='utf-8' />
  <title>Login Page</title>

<script type="text/javascript">
    
    function validateLogin(){
     var rex_name = /^[A-Za-z ]{3,20}$/,
     rex_lastname = /^[A-Za-z ]{3,30}$/;
        console.log("in validatee");
     var errorMsg="";    
     var uname = document.getElementById("username").value,
         pass = document.getElementById("password").value;
            
    if(!uname){
     errorMsg += "Please enter your username.</br>";   
     }
            
    if(!pass){
     errorMsg += "Please enter your password.</br>";   
    }
            
    if(errorMsg){
        document.getElementById("errorDiv").innerHTML = errorMsg;
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
    console.log("in ajax call");
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
  console.log("data =>");
  console.dir(data);
    
    if(data.success){
        //save the login details in login table
        //open chat
        
    }else{
        $("errorDiv").html = data.message;
    }
    
    //redirect_to("login.php");		
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
    <a href="signup.html">Sign Up</a>
   <div id="errorDiv"></div>
    
   <script src="http://code.jquery.com/jquery-latest.min.js"></script>
</body>
</html>