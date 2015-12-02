 <?php 
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
    ?>
<!doctype html>
<html lang='en'>
<head>
  <meta charset='utf-8' />
  <title>Sign up</title>
 <script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script>
    
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
        
       var fname = document.getElementById("firstname").value,
           lname = document.getElementById("lastname").value,
           uname = document.getElementById("username").value,
           pass1 = document.getElementById("password1").value,
           pass2 = document.getElementById("password2").value;
           
        //validate form data
           if(!fname){
           errorMsg += "Please enter your first name.</br>";   
           
          }else if(!rex_name.test(fname)){
            errorMsg += "First name should 3 to 20 characters.<br/>"
          }
        
        if(!lname){  
           errorMsg += "Please enter your last name.</br>"; 
          }else if(!rex_lastname.test(lname)){
            errorMsg += "Last name should 3 to 30 characters.<br/>"
          }
        
          if(!pass1){  
           errorMsg += "Please enter your password.</br>"; 
          }
         if(!pass2){  
           errorMsg += "Please type password again.</br>"; 
          }
        if(pass1 !== pass2){
            errorMsg += "Passwords should be same.</br>"; 
        }
        
          console.log(fname);
        console.log(lname);
        console.log(uname);
        
        console.log(errorMsg);
        
        if(errorMsg){
             console.log("in save 2");
            document.getElementById("errDiv").innerHTML = errorMsg;
            return false;
        }else{
            console.log("in save 1 ");
              ajaxCall("POST",
                       {method:"saveUser",
                        a:"login",
                        data:{"firstname":fname,"lastname":lname,"username":uname,"password":pass1}
                       },
                 callbackSaveUser);
        }
        return true;
    }
    
function ajaxCall(GetPost,d,callback){
    $.ajax({
        type: GetPost,
        async: true,
        cache:"false",
        url: "../mid.php",
        data:d,  
        dataType: "json",
        success: callback
    });
}
    

function callbackSaveUser(data, status){
  console.log("data =>");
  console.dir(data);
    if(data.success){
       errDiv.innerHTML = "Signup successful!";
        
        var r= $('<input type="button" id="back" value="Go back to login"/>');
        $('#login_redirect').append(r);
        $('#submit').attr('value','Update');
        $('#submit').on('click',function(){
         ajaxCall("POST",
                       {method:"updateUser",
                        a:"login",
                        data:$('form').serialize(),
                       },
                 callbackSaveUser);
    });
        $('#back').on('click',function(){
        // header("location: login.php");
            window.location.href = "login.php";
    });
        
    }else{
         errDiv.innerHTML = data.message;
    }	
}


</script>
</head>
<body>
    
    <form id="signup-form" method="post">
    First Name: <input type="text" name="firstname" id="firstname" ><br/>
    Last Name: <input type="text" name="lastname" id="lastname" ><br/>
    Username: <input type="text" name="username" id="username" ><br/>
    Password: <input type="password" name="password" id="password1" ><br/>
    Re-type Password: <input type="password" name="password2" id="password2" ><br/>
        <br/>
        <input type="submit" name="submit" id="submit" value="Submit" onclick="return validateLogin();">
    </form>
    <div id="login_redirect"></div>
     <div id="errDiv"></div>
      
</body>
</html>