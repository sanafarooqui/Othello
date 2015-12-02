<!DOCTYPE html>
<html lang="en">
<head>
  <title>checkers (turns working, logic not)</title>
	<style type="text/css">
		#background { fill: #666; stroke: black; stroke-width: 2px; }
		.player0   {fill: #990000; stroke: white; stroke-width: 1px; }
		.player1 {fill: green; stroke: red; stroke-width: 1px; }
		.htmlBlock {position:absolute;top:200px;left:300px;width:200px;height:100px;background:#ffc;padding:10px;display:none;}
		body{padding:0px;margin:0px;}
		.cell_white{fill:white;stroke-width:2px;stroke:red;}
		.cell_black{fill:black;stroke-width:2px;stroke:red;}
		.cell_alert{fill:#336666;stroke-width:2px;stroke:red;}
		.name_black{fill:black;font-size:18px}
		.name_orange{fill:orange;font-size:24px;}
	</style>
	<script src="http://code.jquery.com/jquery-latest.js"></script>
	<script src="../js/Objects/Cell.js" type="text/javascript"></script>
	<script src="../js/Objects/Piece.js" type="text/javascript"></script>
	<script src="../js/gameFunctions.js" type="text/javascript"></script>
	<script src="../js/ajaxFunctions.js" type="text/javascript"></script>
	<script type="text/javascript">
        
       
$(document).ready(function(){
    
            //hardcoding the players and saving them to the db ..should happen when the player is challenged..
           // var current_user = $SESSION("username");
            var player = "san_far";
            var player0 = "san_far";
            var playerID0 = 35;
            var player1 = "saf6857";
            var playerID1 = 34;
            var gameId = 6;
            
   /* ajaxCall("POST",{method:"playGame",
                        a:"game",
                        data:{"player0": player0,
                             "playerID0":playerID0,
                             "player1":player1,  
                             "playerID1":playerID1}
                       },
                 callbackplayGame);
            
            function callbackplayGame(jsonObj){
	
            } */
            
        initGameAjax('start', gameId);
  }); 

	</script>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" 
	version="1.1"  width="900px" height="700px">
	<!-- Make the background -> 800x600 -->
	<rect x="0px" y="0px" width="100%" height="100%" id="background"></rect>
	<text x="20px" y="20px" id="youPlayer">
		You are:
	</text>
	<text x="270px" y="20px" id="nyt" fill="red" display="none">
		NOT YOUR TURN!
	</text>
	<text x="270px" y="20px" id="nyp" fill="red" display="none">
		NOT YOUR PIECE!
	</text>
	<text x="520px" y="20px" id="opponentPlayer">
		Opponent is:
	</text>
	<text x="650px" y="150px" id="output">
		cell id
	</text>
	<text x="650px" y="190px" id="output2">
		piece id
	</text>
</svg>
</body>
</html>