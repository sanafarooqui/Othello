<?php
   /* session_start();
    if(!isset($_SESSION['username']))
    {
        die("You are not logged in!");
    }
    $username = $_SESSION['username'];
    echo "Hai " . $username;
    echo "<a href='logout.php'>Logout</a>"; */
$username = "san_far";
$userID = 35;
?> 

<!doctype html>
<html lang='en'>
<head>
  <meta charset='utf-8' />
  <title>Chat Page</title>
  <link href="chat.css" rel="stylesheet">
  <script src="http://code.jquery.com/jquery-latest.js"></script>
    
<script type="text/javascript">
    var username = <?php echo json_encode($username); ?>;
    var userID = <?php echo json_encode($userID); ?>;
     
$(document).ready(function(){
    
    getChatData();
    
	$("#chatUsers").on('click','li',function (){
     
    $("#chatUsers li").css({ 'color': 'blue'});
    console.dir($(this));
    $(this).css({ 'color': 'red'});
     
    var challengeUser = $(this).text();
    var challengeUserID = parseInt($(this).id);
        
    console.log("challengeUser "+challengeUser);
    console.log("challengeUserID "+challengeUserID);
        
    ajaxCall("POST",{method:"challengeUser",
                        a:"chat",
                        data:{"username": username,
                             "challengeUser":challengeUser,
                             "challengeUserID":challengeUserID}
                       },
                 callbackChallenge);
  }); 
});
    
 function addChat(){
     var chatText = $("chatText").val();
         //get the users logged in to chat
     ajaxCall("POST",{method:"addChat",
                        a:"chat",
                        data:{"username": username,
                              "userID": userID,
                              "chatText":chatText}
                       },
                 callbackAddChat);
     
     }
    
  
function callbackAddChat(data, status){
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
    
    
 function getChatData(){
         //get the users logged in to chat
     ajaxCall("POST",{method:"chatUsers",
                        a:"chat",
                        data:{"username": username}
                       },
                 callbackChatUsers);
     
      //get the chatdata
     ajaxCall("POST",{method:"chatData",
                        a:"chat"
                       },
                 callbackChatData);
     } 

 
function callbackChatUsers(data, status){
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


function callbackChatData(data, status){
  console.log("data =>");
  console.dir(data);
    
    if(data.success){
        //save the login details in login table
        //open chat
        
    }else{
        $("errorDiv").html = data.message;
    }
    
    setTimeout('getChatData()',2000);
    //redirect_to("login.php");		
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
  		success: callbackChatUsers
    });
}
     
</script>
</head>
<body>
   
   <div id="header">
<h1>Othello</h1>
</div>

<div id="chatUsers">
     <ul class="">
            <li onclick="" id="user_22">sana</li>
            <li onclick="" id="user_35">Saima</li>
            <li onclick="" id="user_30">Fariha</li>
          </ul>
    <input type="button" value="Play" onClick="playGame();" />
    <input type="button" value="Challenge"/>
</div>

<div id="section">
skldjljfsljfsljdsj <br/>
    ieurowiurwouroruw
    <br/>
</div>
    
<div id="article">
    <textarea id="chatText" style="width:600px"></textarea>
     <input type="button" value="Send" onclick="addChat();"/>
</div>

<div id="footer">

</div>
    <script src="http://code.jquery.com/jquery-latest.min.js"></script>
</body>
</html>
