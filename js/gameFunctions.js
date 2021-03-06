var xhtmlns = "http://www.w3.org/1999/xhtml";
var svgns = "http://www.w3.org/2000/svg";
var BOARDX = 50;				//starting pos of board
var BOARDY = 50;				//look above
var boardArr = new Array();		//2d array [row][col]
var pieceArr = new Array();		//2d array [player][piece] (player is either 0 or 1)
var BOARDWIDTH = 8;				//how many squares across
var BOARDHEIGHT = 8;			//how many squares down
//the problem of dragging....
var myX;						//hold my last pos.
var myY;						//hold my last pos.
var mover='';					//hold the id of the thing I'm moving
var turn;						//hold whose turn it is (0 or 1)

function gameInit(){
    	console.log("in game init");
	//create a parent to stick board in...
	var gEle=document.createElementNS(svgns,'g');
	gEle.setAttributeNS(null,'transform','translate('+BOARDX+','+BOARDY+')');
	gEle.setAttributeNS(null,'id','game_'+gameId);
	//stick g on board
	document.getElementsByTagName('svg')[0].insertBefore(gEle,document.getElementsByTagName('svg')[0].childNodes[5]);
	//create the board...
	//var x = new Cell(document.getElementById('someIDsetByTheServer'),'cell_00',75,0,0);
	for(i=0;i<BOARDWIDTH;i++){
		boardArr[i]=new Array();
		for(j=0;j<BOARDHEIGHT;j++){
			boardArr[i][j]=new Cell(document.getElementById('game_'+gameId),'cell_'+j+i,75,j,i);
		}
	}
	console.log("in game init");
	//new Piece(board,player,cellRow,cellCol,type,num)
	//create red
	pieceArr[0]=new Array();
	var idCount=0;
	for(i=0;i<5;i++){
		for(j=0;j<5;j++){
			if((i==3 && j==3) || (i==4 && j==4)){
				pieceArr[0][idCount]=new Piece('game_'+gameId,0,j,i,'Checker',idCount);
				idCount++;
			}
		}
	}
				
	//create green
	pieceArr[1]=new Array();
	idCount=0
	for(i=0;i<5;i++){
		for(j=0;j<5;j++){
			if((i==3 && j==4) || (i==4 && j==3)){
				pieceArr[1][idCount]=new Piece('game_'+gameId,1,j,i,'Checker',idCount);
				idCount++;
			}
		}
	}

	//put the drop code on the document...
	document.getElementsByTagName('svg')[0].addEventListener('mouseup',releaseMove,false);
	//put the go() method on the svg doc.
	document.getElementsByTagName('svg')[0].addEventListener('mousemove',go,false);
	//put the player in the text
	document.getElementById('youPlayer').firstChild.data+=currentPlayer +",current playerid:"+playerId+"1=green,0=red";
	document.getElementById('opponentPlayer').firstChild.data+=opponentPlayer;
	
	//set the colors of whose turn it is
	if(turn==playerId){
		document.getElementById('youPlayer').setAttributeNS(null,'fill',"orange");
		document.getElementById('opponentPlayer').setAttributeNS(null,'fill',"black");
	}else{
		document.getElementById('youPlayer').setAttributeNS(null,'fill',"black");
		document.getElementById('opponentPlayer').setAttributeNS(null,'fill',"orange");
	}
	
	checkTurnAjax('checkTurn',gameId);
}
			
///////////////////////Dragging code/////////////////////////


////setMove/////
//	set the id of the thing I'm moving...
////////////////
function setMove(which){		
	mover = which;
	//get the last position of the thing... (NOW through the transform=translate(x,y))
	xy=getTransform(which);

	myX=xy[0];
	myY=xy[1];
	//get the object then re-append it to the document so it is on top!
	getPiece(which).putOnTop(which);
}
			
			
////releaseMove/////
//	clear the id of the thing I'm moving...
////////////////
function releaseMove(evt){
	//alert(evt);
	//check hit (need the current coords)
	// get the x and y of the mouse
	if(mover != ''){
		//is it YOUR turn?
		if(turn == playerId){
			var hit=checkHit(evt.layerX,evt.layerY,mover);
		}else{
			var hit=false;
			nytwarning();
		}
		if(hit==true){
			//I'm on the square...
			//send the move to the server!!!
		}else{
			//move back
			setTransform(mover,myX,myY)
		}
		mover = '';	
	}
}
			
			
////go/////
//	move the thing I'm moving...
////////////////
function go(evt){
	if(mover != ''){
		setTransform(mover,evt.layerX,evt.layerY);
	}
}
			
////checkHit/////
//	did I land on anything important...
////////////////
function checkHit(x,y,which){
	//lets change the x and y coords (mouse) to match the transform
	x=x-BOARDX;
	y=y-BOARDY;	
	//go through ALL of the board
	for(i=0;i<BOARDWIDTH;i++){
		for(j=0;j<BOARDHEIGHT;j++){
			var drop = boardArr[i][j].myBBox;
			//document.getElementById('output2').firstChild.nodeValue+=x +":"+drop.x+"|";
			if(x>drop.x && x<(drop.x+drop.width) && y>drop.y && y<(drop.y+drop.height) && boardArr[i][j].droppable && boardArr[i][j].occupied == ''){
				
				//NEED - check is it a legal move???
				//if it is - then
				//put me to the center....
				setTransform(which,boardArr[i][j].getCenterX(),boardArr[i][j].getCenterY());
				//fill the new cell
				//alert(parseInt(which.substring((which.search(/\|/)+1),which.length)));
				getPiece(which).changeCell(boardArr[i][j].id,i,j);
				//change other's board
				changeBoardAjax(which,i,j,'changeBoard',gameId);
				
				//change who's turn it is
				changeTurn();
				return true;
			}
		}	
	}
	return false;
}

function checkValidCell(cellid){
    console.log("checkValidCell"+cellid);
    //get i,j from cell id
    var col = parseInt(cellid.charAt(5));
    var row = parseInt(cellid.charAt(6));
    var cellObj = boardArr[row][col];
    
    console.log(boardArr[row][col]);
    if(cellObj.droppable && cellObj.occupied == ''){
        //determining if surrounding cells have opponents piece
        for(var x=col-1;x<=col+1;x++){
             
            for(var y=row-1;y<=row+1;y++){
               
                 console.log("boardArr");
                 console.log(boardArr[y][x]);
                 //not considering the current cell
                if(!(x==col && y==row)){
                     console.log("x:"+x);
                console.log("y:"+y);
                    if(boardArr[y][x].occupied != ''){
                      
                        var pieceID = boardArr[y][x].occupied;
                         console.log("pieceID");
                        console.log(pieceID);
                        if(playerId != getPiece(pieceID).player){
                             console.log("found oppnent piece!");
                             
                            var flipPieceArr = new Array();
                            flipPieceArr.push(getPiece(pieceID));
                            //keep checking the same row/col/diag to find '' or your piece
                            var i = col-x;
                            var j = row-y;
                            var nextCelli = x-i;
                            var nextCellj = y-j;
                            //save pieces to be flipped in an array
                           
                             console.log("nextCelli :"+nextCelli);
                             console.log("nextCellj :"+nextCellj);
                            while(boardArr[nextCellj][nextCelli].occupied != ''){
                            //find the next cell to check your own piece 
                                pieceID = boardArr[nextCellj][nextCelli].occupied;
                                
                                if(playerId != getPiece(pieceID).player){
                                    console.log("pieceID");
                                    console.log(pieceID);
                                    flipPieceArr.push(getPiece(pieceID));
                                    //its opponents piece ..keep looping till you hit empty cell or your player
                                    nextCelli-=i;
                                    nextCellj-=j;
                                    continue;
                                }else{
                                    console.log("found the same piece!");
                                    //call flipping method
                                    placePieceAndFlip(col,row,flipPieceArr);
                                    break;
                                }
                        
                             }
                        }
                     }
                  }
             }
          }
    }
}

function placePieceAndFlip(col,row,flipPieceArr){
    console.log("placePieceAndFlip : "+col+" "+row);
    console.dir(flipPieceArr);
    console.log("pieceArr.length++ : "+pieceArr.length);
    var count = pieceArr[playerId].length++;
    
 pieceArr[playerId][count]=new Piece('game_'+gameId,playerId,row,col,'Checker',count);
    
    //flip
    for(var x=0;x<flipPieceArr.length;x++){
        flipPieceArr[x].current_cell.notOccupied();
        var newCount = count++;
        pieceArr[playerId][newCount]=new Piece('game_'+gameId,playerId,flipPieceArr[x].current_cell.row,flipPieceArr[x].current_cell.col,'Checker',newCount);
       
        //remove it from pieceArr
         for(var y=0,len=pieceArr[Math.abs(playerId-1)].length;y<len;y++){
            if(pieceArr[Math.abs(playerId-1)].id == flipPieceArr[x].id){
             //remove
                 pieceArr[Math.abs(playerId-1)].splice(x,1);
            }
         }
    }

}

///////////////////////////////Utilities////////////////////////////////////////
////get Piece/////
//	get the piece (object) from the id and return it...
////////////////
function getPiece(which){
	return pieceArr[parseInt(which.substr((which.search(/\_/)+1),1))][parseInt(which.substring((which.search(/\|/)+1),which.length))];
}
			
////get Transform/////
//	look at the id of the piece sent in and work on it's transform
////////////////
function getTransform(which){
	var hold=document.getElementById(which).getAttributeNS(null,'transform');
	var retVal=new Array();
	retVal[0]=hold.substring((hold.search(/\(/) + 1),hold.search(/,/));			//x value
	retVal[1]=hold.substring((hold.search(/,/) + 1),hold.search(/\)/));;		//y value
	return retVal;
}
			
////set Transform/////
//	look at the id, x, y of the piece sent in and set it's translate
////////////////
function setTransform(which,x,y){
	document.getElementById(which).setAttributeNS(null,'transform','translate('+x+','+y+')');
}

////change turn////
//	change who's turn it is
//////////////////
function changeTurn(){
	//locally
	turn=Math.abs(turn-1);
	//how about for the server (and other player)?
	//send JSON message to server, have both clients monitor server to know who's turn it is...
	//document.getElementById('output2').firstChild.data='playerId '+playerId+ ' turn '+turn;
	changeServerTurnAjax('changeTurn',gameId);
}


/////////////////////////////////Messages to user/////////////////////////////////
////nytwarning (not your turn)/////
//	tell player it isn't his turn!
////////////////
function nytwarning(){
	if(document.getElementById('nyt').getAttributeNS(null,'display') == 'none'){
		document.getElementById('nyt').setAttributeNS(null,'display','inline');
		setTimeout('nytwarning()',2000);
	}else{
		document.getElementById('nyt').setAttributeNS(null,'display','none');
	}
}

////nypwarning (not your piece)/////
//	tell player it isn't his piece!
////////////////
function nypwarning(){
	if(document.getElementById('nyp').getAttributeNS(null,'display') == 'none'){
		document.getElementById('nyp').setAttributeNS(null,'display','inline');
		setTimeout('nypwarning()',2000);
	}else{
		document.getElementById('nyp').setAttributeNS(null,'display','none');
	}
}