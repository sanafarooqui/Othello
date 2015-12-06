<?php
  session_start();
    if(!isset($_SESSION['userID']))
    {
        die("You are not logged in!");
    }
    $userID = $_SESSION['userID'];
    $username = $_SESSION['username'];

?> 

<!doctype html>
<html lang='en'>
<head>
  <meta charset='utf-8' />
  <title>Chat Page</title>
  <link href="../css/chat.css" rel="stylesheet"/>
    <link href="../css/jquery-ui.min.css" rel="stylesheet"/>
    <script src="http://code.jquery.com/jquery-latest.min.js"></script>
    <!--<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css"> -->
    <script src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    
    
<script type="text/javascript">
    var currentUsername = <?php echo json_encode($username); ?>;
    var currentUserID = <?php echo json_encode($userID); ?>;

   
$(document).ready(function(){
    
   // showDialog();
    getChatData();
    
     //get the users logged in to chat
    
     ajaxCall("POST",{method:"chatUsers",
                        a:"chat",
                        data:{"username": currentUsername}
                       },
                 callbackChatUsers);
    
    //check if anyone has challenged for a game
    checkChallenge();
    
	$("#chatUsers").on('click','li',function (){
     
        $("#chatUsers li").css({ 'color': 'blue'});
        console.dir($(this));
        //$(this).css({ 'color': 'red'});
        $(this).addClass("redClass");

    });
});
    
function challengeUser(){
    var liEle = $("li.redClass");
    var toName = liEle.text();
    var toID = parseInt(liEle.attr('id'));

        console.log("challengeUser "+toName);
        console.log("challengeUserID "+toID);

        ajaxCall("POST",{method:"challengeUser",
                            a:"chat",
                            data:{"fromName": currentUsername,
                                 "toName":toName,
                                 "toID":toID,
                                 "fromID":currentUserID}
                           },
                     callbackChallenge);
}
    
   
//check if the user has accepted the challenge
function callbackChallenge(data, status){
    if(data.success){
        checkChallengeAccepted();
    }
}
    
var checkChallengeAcceptedTimeout; 
function checkChallengeAccepted(){
 ajaxCall("POST",{method:"checkChallengeAccepted",
                        a:"chat",
                        data:{"username": currentUsername,
                             "userID":currentUserID}
                       },
                 callbackCheckChallengeAccepted);
      checkChallengeAcceptedTimeout = setTimeout(checkChallengeAccepted,4000);
}

    
function callbackCheckChallengeAccepted(data,status){
    console.log("callbackCheckChallengeAccepted");
      console.dir(data);
    //if true show dialog
      if(data.success){
          var arr= data.responseJSON;
          for(var i=0,len=arr.length;i<len;i++){
           
    $("#dialogText").text(arr[i].toName+" has accepted your challenge.");
              var cid = arr[i].challengeID;
     $(function() {
        $( "#dialog-confirm" ).dialog({
          dialogClass:"no-close",
          resizable: false,
          height:200,
          modal: false,
          buttons: {
            "Play": function() {
              $( this ).dialog( "close" );
                //findGameToJoin
                ajaxCall("POST",{method:"joinGame",
                        a:"game",
                        data:{"challengeId":cid,
                             "fromID":currentUserID}
                       },
                 callbackJoinGame);
               
                //clearTimeout(checkChallengeAcceptedTimeout);
                 //clearTimeout(checkChallengeTimeout);
            }
          }
        });
      });  
    } 
}
}
    
    function callbackJoinGame(data,status){
    console.log("callbackOpenGameScreen");
       console.log(data);
    var arr = data.response;
   if(data.success){
       //open play screen for the fromID
     window.location.href = "../game/game.php?gameID="+arr[0].gameID;
   }
}

var checkChallengeTimeout;
//check if there are any challenges from other users
function checkChallenge(){
     ajaxCall("POST",{method:"checkChallenge",
                        a:"chat",
                        data:{"username": currentUsername,
                             "userID":currentUserID}
                       },
                 callbackCheckChallenge);
    //should be in Timeout
    checkChallengeTimeout = setTimeout(checkChallenge,4000);
}
//Show dialog for challenge
function callbackCheckChallenge(data, status){
  console.log("callbackCheckChallenge =>");
  console.dir(data);
    var arr = data.responseJSON;
    var text="";
    if(data.success){
        for(var i=0,len=arr.length;i<len;i++){
            if(arr[i].toName === currentUsername){
                text = arr[i].fromName + " has challenged you for a game of Othello!";
                $("#dialogText").text(text);
                
                showDialog(arr[i].fromID,arr[i].toID,arr[i].fromName,arr[i].toName,arr[i].challengeID);
            }
        }
    }else{
    }
} 

function showDialog(fromID,toID,fromName,toName,cID){
     $(function() {
        $( "#dialog-confirm" ).dialog({
          dialogClass:"no-close",
          resizable: false,
          height:200,
          modal: false,
          buttons: {
            "Play": function() {
              $( this ).dialog( "close" );
                 ajaxCall("POST",{method:"saveChallengeStatus",
                        a:"chat",
                        data:{"fromID": fromID,
                             "toID":toID,
                             "fromName":fromName,
                             "toName":toName,
                             "accepted":true,
                             "challengeID":cID}
                       },
                 callbackInitializeGame);
                clearTimeout(checkChallengeTimeout);
                
            },
            Cancel: function() {
              $( this ).dialog( "close" );
                  ajaxCall("POST",{method:"saveChallengeStatus",
                        a:"chat",
                        data:{"fromID": fromID,
                             "toID":toID,
                             "accepted":false,
                             "challengeID":cID}
                       });
                //clearTimeout(checkChallengeTimeout);
            }
          }
        });
      });    
}
    
function callbackInitializeGame(data,status){
   
    var res = data.responseText
   if(data.success && data.accepted){
       
          ajaxCall("POST",{method:"startGame",
                        a:"game",
                        data:{"fromID": res.fromID,
                             "toID":res.toID,
                             "challengeId":res.challengeID,
                             "fromName":res.fromName,
                             "toName":res.toName,
                             "challengeId":res.challengeID}
                       },
                 callbackOpenGameScreen);
   }
}
    
function callbackOpenGameScreen(data,status){
    console.log("callbackOpenGameScreen");
       console.log(data);
    var  gameID = data.gameID;
   if(data.success){
       //open play screen for the fromID
      
     window.location.href = "../game/game.php?gameID="+gameID;
   }
}
    
 function addChat(){
     var chatText = $("#chatText").val();
    
         //get the users logged in to chat
     ajaxCall("POST",{method:"addChat",
                        a:"chat",
                        data:{"username": currentUsername,
                              "userID":currentUserID,
                              "chatText":chatText}
                       },
                 callbackAddChat);
     
     }
    
  
function callbackAddChat(data, status){
  console.log("data =>");
  console.dir(data);
     var txt = $("#section").html();
    if(!data.success){
        $("#errorDiv").text(data.message);
    }else{
      txt += "You:"+ $("#chatText").val() + "</br>"; 
    $("#section").html(txt);
    
    }
    $("#chatText").val("");
} 
    
    
 function getChatData(){
    
      //get the chatdata
     ajaxCall("POST",{method:"chatData",
                        a:"chat",
                        data:{"username": currentUsername}
                       },
                 callbackChatData);
     
    // setTimeout(getChatData,3000);
     } 

// var userArray=array();
function callbackChatUsers(data, status){
  console.log("data users=>");
  console.dir(data.responseJSON);
    var arr = data.responseJSON;
   

    if(data.success){
        //TODO update user list 
      /*  for(var i=0,len=arr.length;i<len;i++){
            if(userArray.length == 0){
                userArray.push(arr[i].userID);
            }else{
                for(j=0;j<userArray.length;j++){
                    
                }
            } 
        }*/
        for(var i=0,len=arr.length;i<len;i++){
            $('<li/>', {html: arr[i].username}).appendTo('#chatUserList').attr('id',arr[i].userID+"_uid");
        }
        
    }else{
        $("#chatUsers").text(data.message);
    }
   
} 


function callbackChatData(data, status){
  console.log("data chat=>");
  console.dir(data);
    var txt="";
     var arr = data.responseJSON;
    if(data.success){
        for(var i=0,len=arr.length;i<len;i++){
            if(arr[i].username === currentUsername){
                arr[i].username = "You";
            }
            txt+= arr[i].username+":"+arr[i].message + "</br>"; 
        }
        $("#section").html(txt);
    }else{
         $("#section").text(data.message);
    }
    
  //  setTimeout('getChatData()',2000);	
} 
    
function logOut(){
     console.log("in logOut");
     
      //get the chatdata
     ajaxCall("POST",{method:"logOut",
                        a:"login",
                        data:{"userID": currentUserID}
                       },
                 callbackLogOut);
}
    
 
function ajaxCall(GetPost,d,callback){
    $.ajax({
  		type: GetPost,
  		async: true,
  		cache:false,
  		url: "../mid.php",
  		data: d,  
  		dataType: "json",
  		success: callback
    });
}
     
</script>
</head>
<body>
   
   <div id="header">
<h1>Othello</h1>
<input type="button" style="align:right" value="logOut" onClick="logOut();" />
</div>

<div id="chatUsers">
     <ul id="chatUserList" class=""></ul>
    <input type="button" value="Play" onClick="playGame();" />
    <input type="button" value="Challenge" onClick="challengeUser();" />
</div>

<div id="section">
</div>
    
<div id="article">
    <textarea id="chatText" style="width:600px"></textarea>
     <input type="button" value="Send" onclick="addChat();"/>
</div>
<div id="dialog-confirm" title="Challenge">
  <p id="dialogText"></p>
</div>
<div id="footer">

</div>
    
</body>
</html>
