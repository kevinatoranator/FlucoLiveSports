import * as suv from "./Utilities.js";

 var interval = 1000;
 var minutes = 40;
 var seconds = minutes * 60;
 var currentSeconds = seconds;
var expected = 0;
var started = false;
var gameID = $(location).attr('href').split("gameID=")[1];
var homeTeam = "";
var awayTeam = "";
var home = "";
var away = "";
var sportID = 0;
var scores = new Array();
var homeRoster = new Array();
var awayRoster = new Array();
var table = "";
var level = "";
var lastPlayTime = 0;
var formattedTime = "";
var lastPeriod = 1;
var completed = 0;
var homeTimeouts = 3;
var awayTimeouts = 3;


var currentYardLine = 40;
var currentPossession = "FLUV";
var fieldSide = "FLUV";
var yardsToGo = 10;
var down = 1;


var liveinfo = ["", "", "", "", "", "", "", "", ""];


const SPORTTYPE = {
	Half: 2,
	Quarter: 4,
	Inning: 7,
	Set: 5
}
var gameType = SPORTTYPE.Quarter;

var liveinfo = ["", "", "", "", "", "", "", "", ""];
var playerList = {[home]: homeRoster, [away]: awayRoster};


  function step(){
	  if(started == false || currentSeconds < 0){
		  return;
	  }
	  var dt = Date.now() - expected;
	  currentSeconds--;
	  setTimer(currentSeconds);
	  
	  if(currentSeconds%60 == 0){
		formattedTime = Math.floor(currentSeconds/60) + ":" + zeroPad(currentSeconds%60, 2);
		  $.ajax({
			url: "livegame.php",
			data: {table: table,
			gameID: gameID,
			time: formattedTime,
			period: lastPeriod,
			liveinfo: liveinfo},
			success: function(data){
				console.log(data);
				}
			});	
	  }
	  
	  //Reset
	  expected += interval;
	  setTimeout(step, Math.max(0, interval - dt));
  }
  
  const zeroPad = (num, places) => String(num).padStart(places, '0');
  const setTimer = (time) => document.getElementById("timer").innerText = Math.floor(time/60) + ":" + zeroPad(time%60, 2);
  const setPeriod = (period) => document.getElementById("period").innerText = period;


window.onload=function(){
	 started = suv.timerSet(minutes, currentSeconds);
	 suv.periodSet(lastPeriod, gameType);
	 document.getElementById("timerControl").addEventListener('click', function(){	
		if(started == false){
			currentSeconds = ($("#minutes :selected").val() * 60) + parseInt($("#seconds :selected").val());
			lastPeriod = $("#periods :selected").val();
			started = true;
			expected = Date.now() + interval;
			document.getElementById("timerControl").innerText = "Stop";
			setTimer(currentSeconds);
			setPeriod(lastPeriod);
		  setTimeout(step, interval);
		}else{
			started = suv.timerSet(minutes, currentSeconds);
			suv.periodSet(lastPeriod, gameType);
		}
	  }, false);
	  
	  document.getElementById("timerReset").addEventListener('click', function(){	
		currentSeconds = seconds;
		if(started == true){
			setTimer(currentSeconds);
		}else{
			started = suv.timerSet(minutes, currentSeconds);
			suv.periodSet(lastPeriod, gameType);
		}
	  }, false);
	  
	  document.getElementById("gameLoad").addEventListener('click', loadGame, false);
	  
	  document.getElementById("manager").innerHTML = "<div class='text-center'><button id='start'>Start Game</button></div>";
	  document.getElementById("start").addEventListener('click', function(){
		var gameInfoText = "";
		$.ajax({
			url: "start.php",
			data: {table: table,
			gameID: gameID,
			time: formattedTime},
			success: function(data){
				console.log(data);
				console.log(table);
				loadGame();	
				document.getElementById("manager").innerHTML = "<div class='text-center'><button id='play'>Add Play</button></div>";
				document.getElementById("play").addEventListener('click', play);
				document.getElementById("timerr").classList.remove("hidden");
				suv.periodSet(lastPeriod, gameType);
				document.getElementById("qb").classList.remove("hidden");
				document.getElementById("complete").classList.remove("hidden");
				document.getElementById("completeBtn").addEventListener('click', function(){
					var infoArray = [sportID, home, away, suv.sumArrayRange(scores, 0, scores.length/2), suv.sumArrayRange(scores, scores.length/2, scores.length)];
					suv.complete(table, gameID, infoArray)});
				qbSelect(home);
				qbSelect(away);
				}
			});		
	  }, false);
	  loadGame();
}

function loadGame(){
	var gameInfoText = "";
		$.ajax({
			url: "load.php",
			data: {gameID: gameID},
			success: function(data){
				var dataArray = $.parseJSON(data);
				console.log(dataArray);
				homeTeam = dataArray.home;
				awayTeam = dataArray.away;
				scores = dataArray.scores;
				home = dataArray.homeKey;
				away = dataArray.awayKey;
				homeRoster = suv.rosterSort(dataArray.homeRoster)
				awayRoster = suv.rosterSort(dataArray.awayRoster)
				sportID = dataArray.sportID;
				table = dataArray.table;
				level = dataArray.level;
				minutes = dataArray.minutes;
				seconds = minutes * 60;
				currentSeconds = seconds;
				started = suv.timerSet(minutes, currentSeconds);
				completed = parseInt(dataArray.completed);
				
				gameInfoText += dataArray.sport + "</br>";
				gameInfoText += "Home: " + dataArray.home + "</br>";
				gameInfoText += "Away: " + dataArray.away + "</br>";
				document.getElementById("gameInfo").innerHTML = gameInfoText;
				if(scores != null){
					document.getElementById("scoreTable").innerHTML = scoreTableText(scores);
					document.getElementById("scoreTableManual").innerHTML = scoreTableManualText(scores);
					eventAdder();
				}
				document.getElementById("homeRoster").innerHTML = "-Home Roster-<br>" + suv.rosterFormat(homeRoster);
				document.getElementById("awayRoster").innerHTML = "-Away Roster-<br>" + suv.rosterFormat(awayRoster);
				formattedTime = Math.floor(currentSeconds/60) + ":" + zeroPad(currentSeconds%60, 2);
				gameType = SPORTTYPE.Quarter;
				document.getElementById("gameLoad").innerText = "Reload Data";
				
				if(completed == 1){
					  document.getElementById("manager").innerHTML = "Game complete";
				  }
				}
			});
		document.getElementById("gameInfo").innerHTML = gameInfoText;
}

function play(){
	console.log("Plays here");
	console.log(Math.floor(currentSeconds/60) + ":" + zeroPad(currentSeconds%60, 2));
	lastPlayTime = currentSeconds;
	$.ajax({
			url: "play.php",
			data: {table: table,
			gameID: gameID},
			success: function(data){
				var dataArray = $.parseJSON(data)
				var manager = document.getElementById("manager");
				manager.innerHTML = "<div class='text-center'><button id='submit'>Submit</button></div><br><br><div class=\"flex justify-between\"><div id=\"play\"></div><div id=\"teamSelect\"></div><div id=\"rosterSelect\"></div><div id=\"yardSelect\"></div><div id=\"bonusSelect\"></div><div id=\"extraInfo\"></div></div><br><div class='text-center'><button id='cancel'>cancel</button></div>";
				dataArray.forEach(function(item){
					document.getElementById("play").innerHTML += item;
				});
				document.getElementById("teamSelect").innerHTML += `<input type="radio" id=${home} name="team" value=${home} ><label for=${home} >${home}</label><br><input type="radio" id=${away} name="team" value=${away} ><label for=${away} >${away}</label><br>`;
				document.getElementById(home).addEventListener('click', teamSelection);
				document.getElementById(away).addEventListener('click', teamSelection);
				document.getElementById("submit").addEventListener('click', submitPlay);
				document.getElementById("cancel").addEventListener('click', cancel);
				
				}
			});
	
}

function cancel(){//Move to utilities?
	console.log("Play cancelled");
	document.getElementById("manager").innerHTML = "<div class='text-center'><button id='play' >Add Play</button></div>";
	document.getElementById("play").addEventListener('click', play);
}

function submitPlay(){
console.log("Plays added");
	var playSelect = $("input[name='plays']:checked").val();
	var teamSelect = $("input[name='team']:checked").val();
	var oppTeam = $("input[name='team']:not(:checked)").val();
	var playerSelect = $("input[name='player']:checked").val();
	var qb = $("#homeqbsel :selected").val();
	if(teamSelect == away){
		qb = $("#awayqbsel :selected").val();
	}
	var yards = $('#yardsel').find(":selected").val();
	var defense = $("input[name='defPlayer']:checked").val();
	if(defense == undefined){
		defense = "";
	}
	var downText = "";
	switch(down){
		case 1:
			downText = "1st";
			break;
		case 2:
			downText = "2nd";
			break;
		case 3:
			downText = "3rd";
			break;
		case 4:
			downText = "4th";
			break;
	}
	
	var playTime = Math.floor(currentSeconds/60) + ":" + zeroPad(currentSeconds%60, 2);
	var infoArray = [playSelect, teamSelect, oppTeam, playerSelect, lastPeriod, playTime, sportID, qb, yards, defense, downText, yardsToGo, currentPossession, fieldSide, currentYardLine];
	
	console.log(infoArray);
	console.log(home);
	$.ajax({
			url: "submit.php",
			data: {table: table,
			gameID: gameID,
			infoArray: infoArray,
			scores: scores,
			home: home},
			success: function(data){
				var dataArray = $.parseJSON(data)
				console.log(dataArray);
				document.getElementById("manager").innerHTML = "<div class='text-center'><button id='play'>Add Play</button></div>";
				document.getElementById("play").addEventListener('click', play);
				scores = dataArray.scores;
				document.getElementById("scoreTable").innerHTML = scoreTableText(scores);
				document.getElementById("scoreTableManual").innerHTML = scoreTableManualText(scores);
				eventAdder();
				}
			});
	
	if(fieldSide == currentPossession){
		currentYardLine = Number(currentYardLine)+ Number(yards);
	}else{
		currentYardLine = Number(currentYardLine)-Number(yards);
	}
	
	if(currentYardLine > 50){//yardline # side swap
		currentYardLine = 100 - currentYardLine;
		if(fieldSide == home){
			fieldSide = away;
		}else{
			fieldSide = home;
		}
	}else if(currentYardLine <= 0){
		currentYardLine = 0;
	}
	
	if(playSelect != "Return by " && yards != undefined && currentYardLine > 0){
		yardsToGo = yardsToGo - yards;
	}
	//Combine fumble and recovery?
	if(playSelect == "Run by " || playSelect== "Incomplete pass to " || playSelect == "Reception by " || playSelect == "Intentional grounding on " || playSelect == "Sack by " || (playSelect == "Recovered by " && team == currentPossession)){
		down += 1;
		if(down > 4 && yardsToGo > yards){
			down = 1;
			if(currentPossession == home){//swap possession
				currentPossession = away;
			}else{
				currentPossession = home;
			}
		}
	}else if(playSelect == "Kickoff by " || playSelect == "Field goal MISS by " || playSelect == "Interception by " || playSelect == "Punt by " || (playSelect == "Recovered by " && team != currentPossession)){
		down = 1;
		yardsToGo = 10;
				
		if(currentPossession == home){
			currentPossession = away;
		}else{
			currentPossession = home;
		}
		
	}
			
			if($action == "Field goal GOOD by " or $action == "Extra point GOOD by " or $action == "Extra point MISS by " or $action == "2-point conversion GOOD by " or $action == "2-point conversion FAIL by "){
				$down = 1;
				$sof = $poss;
				$yardline = 40;
				
			}else if($action == "Safety by " or $action == "Touchback"){
				if($action == "Touchback"){
					if($poss == $homeTeam){
						$poss = $awayTeam;
					}else{
						$poss = $homeTeam;
					}
				}
				$down = 1;
				$sof = $poss;
				$yardline = 20;
			}
			
			if($ytg <= 0){
				$ytg = 10;
				$down = 1;
				if($sof != $poss && $yardline < 10){
					$ytg = $yardline;
				}
			}
	
	liveinfo[0] = currentPossession;
	liveinfo[1] = homeTimeouts;
	liveinfo[2] = awayTimeouts;
	liveinfo[3] = fieldSide;
	liveinfo[4] = currentYardLine;
	liveinfo[5] = yardsToGo;
	liveinfo[6] = down;
	
	$.ajax({
		url: "livegame.php",
		data: {table: table,
		gameID: gameID,
		time: playTime,
		period: lastPeriod,
		liveinfo: liveinfo},
		success: function(data){
			console.log(data);
			}
		});	
}

function scoreTableText(scoreArray){
	var tableText = "<table>";
	for(var row = 0; row < 4; row++){//each row--- Row 1| Labels, Row 2| Top Line, Row 3| Team 1, Row 4| Team 2
		tableText += "<tr>";
		for(var col = -1; col < scoreArray.length; col++){
			if(col < 0){//First column has unique table row labels
				switch (row){
					case 0:
						tableText += "<td>Team</td>";
						break;
					case 1:
						tableText += "<td>----</td>";
						break;
					case 2:
						tableText += `<td>${away}</td>`;
						break;
					case 3:
						tableText += `<td>${home}</td>`;
						break;
				}
			}else{//Every other colums
				if(col % 2){ //Odd columns are info
					if(row == 0){
						if(col == scoreArray.length - 1){//Replace last label as OT rather than #
							tableText += "<td> OT </td>";
						}else{
							var val = (col + 1) / 2;
							tableText += `<td>${val}</td>`;
						}
					}else if(row == 1){
						tableText += "<td>----</td>";
					}else{//Gets correct score value depending on row 
						var val = scoreArray[(col-1)/2 + (Math.abs(row - 3) * (scoreArray.length/2))];
						tableText += `<td>${val}</td>`;
					}
				}else{//Even columns are dividers
					if(row == 1){
						tableText += "<td>-</td>";
					}else{
						tableText += "<td> | </td>";
					}
				}
			}
		}
		if(row == 0){
			tableText += "<td> | </td> <td>Total</td> ";
		}else if(row == 1){
			tableText += "<td>-</td><td>----</td>";
		}else{
			var val = suv.sumArrayRange(scoreArray, Math.abs(row - 3) * (scoreArray.length/2), Math.abs(row - 4) * (scoreArray.length/2)); //Calculates total of scores in that subsection of array
			tableText += `<td> | </td> <td> ${val} </td> `;
		}
		tableText +="</tr>";
	}
	
	tableText +="</table>";
	return tableText;
}

function scoreTableManualText(scoreArray){
	var tableText = "<table>";
	for(var row = 0; row < 4; row++){//each row--- Row 1| Labels, Row 2| Top Line, Row 3| Team 1, Row 4| Team 2
		tableText += "<tr>";
		for(var col = -1; col < scoreArray.length; col++){
			if(col < 0){//First column has unique table row labels
				switch (row){
					case 0:
						tableText += "<td>Team</td>";
						break;
					case 1:
						tableText += "<td>----</td>";
						break;
					case 2:
						tableText += `<td>${away}</td>`;
						break;
					case 3:
						tableText += `<td>${home}</td>`;
						break;
				}
			}else{//Every other colums
				if(col % 2){ //Odd columns are info
					if(row == 0){
						if(col == scoreArray.length - 1){//Replace last label as OT rather than #
							tableText += "<td> OT </td>";
						}else{
							var val = (col + 1) / 2;
							tableText += `<td>${val}</td>`;
						}
					}else if(row == 1){
						tableText += "<td>----</td>";
					}else if(row == 2){//Gets correct score value depending on row 
						var idName = 'awayPeriod' + (col + 1)/2 + 'Score';
						var val = scoreArray[(col-1)/2 + (Math.abs(row - 3) * (scoreArray.length/2))];
						tableText += `<td><input type='number' id=${idName} name = ${idName} min = '0' max = '99' value =${val}></td>`;
					}else{//Gets correct score value depending on row 
						var idName = 'homePeriod' + (col + 1)/2 + 'Score';
						var val = scoreArray[(col-1)/2 + (Math.abs(row - 3) * (scoreArray.length/2))];
						tableText += `<td><input type='number' id=${idName} name = ${idName} min = '0' max = '99' value =${val}></td>`;
					}
				}else{//Even columns are dividers
					if(row == 1){
						tableText += "<td>-</td>";
					}else{
						tableText += "<td> | </td>";
					}
				}
			}
		}
		if(row == 0){
			tableText += "<td> | </td> <td>Total</td> ";
		}else if(row == 1){
			tableText += "<td>-</td><td>----</td>";
		}else{
			var val = suv.sumArrayRange(scoreArray, Math.abs(row - 3) * (scoreArray.length/2), Math.abs(row - 4) * (scoreArray.length/2)); //Calculates total of scores in that subsection of array
			tableText += `<td> | </td> <td> ${val} </td> `;
		}
		tableText +="</tr>";
	}
	
	tableText +="</table>";
	return tableText;
}

function eventAdder(){
	for(var i = 1; i <= gameType; i++){
		var idName = 'awayPeriod' + i + 'Score';
		document.getElementById(idName).addEventListener('input', supdate, false);
	}
	for(var i = 1; i <= gameType; i++){
		var idName = 'homePeriod' + i + 'Score';
		document.getElementById(idName).addEventListener('input', supdate, false);
	}
}

function supdate(){
	suv.updateScore(this, scores, gameType, gameID, table);
	document.getElementById("scoreTable").innerHTML = scoreTableText(scores);
	document.getElementById("scoreTableManual").innerHTML = scoreTableManualText(scores);
	eventAdder();
}

export function teamSelection(){//MAYBE MOVE TO UTILITIES
	var playerList = {[home]: homeRoster, [away]: awayRoster};
	var playSelect = $("input[name='plays']:checked").val();
	document.getElementById("rosterSelect").innerHTML = "";
	//console.log(team.entries);
	for(const [key, value] of Object.entries(playerList[this.value])){
		document.getElementById("rosterSelect").innerHTML += `<input type="radio" id=${key} name="player" value="${key}" ><label for=${key} >${key}</label><br>`;
	}
	bonusAction(playSelect);	
}

function bonusAction(play){
	if(play instanceof Object){
		play = play.value;
	}
	document.getElementById("bonusSelect").innerHTML = "";
	document.getElementById("yardSelect").innerHTML = "";
	document.getElementById("extraInfo").innerHTML = "";
	var playerList = {[home]: homeRoster, [away]: awayRoster};
	var teamSelect = $("input[name='team']:checked").val();
	var opp = $("input[name='team']:not(:checked)").val();
	var playerSelect = $("input[name='player']:checked").val();
	if(teamSelect != undefined){
		if(play == "Run by " || play == "Reception by " || play == "Touchdown by " || play == "Touchdown reception by " || play == "Kick by " || play == "Penalty on " || play == "Holding on " || play == "Offsides on " || 
		play == "Pass interference on " || play == "False start on " || play == "Intentional grounding on " || play == "Sack by " || play == "Punt by " || play == "Return by "){
			var toAdd = " for <select name = 'yardsel' id = 'yardsel'>";
			for(var i = -100; i < 101; i++){
				if(i == 0){
					toAdd += `<option selected = "selected" value="${i}">${i}</option>`;
				}else{
					toAdd += `<option value="${i}">${i}</option>`;
				}
			}
			toAdd += "</select> yards";
			document.getElementById("yardSelect").innerHTML = toAdd;
		}
		
		if(play == "Run by " || play == "Reception by " || play == "Sack by " || play == "Return by "){
			document.getElementById("extraInfo").innerHTML = "tackle by <br>";
			var playerList = {[home]: homeRoster, [away]: awayRoster};
			//console.log(team.entries);
			for(const [key, value] of Object.entries(playerList[opp])){
				document.getElementById("extraInfo").innerHTML += `<input type="radio" id=${key}def name="defPlayer" value="${key}" ><label for=${key}def >${key}</label><br>`;
			}
			document.getElementById("extraInfo").innerHTML += `<input type="radio" id=nonedef name="defPlayer" value="None" ><label for=nonedef >None</label><br>`;
		}
	}
}

function qbSelect(team){
	var playerList = {[home]: homeRoster, [away]: awayRoster};
	var pgid = "";
	if(team == home){
		pgid = "homeqb";
	}else{
		pgid = "awayqb";
	}
	//document.getElementById(pgid).innerHTML = "";
	var toAdd = "";
	toAdd += `<select name = '${pgid}sel' id = '${pgid}sel'>`;
	for(const [key, value] of Object.entries(playerList[team])){
		toAdd += `<option value="${key}">${key}</option>`;
	}
	toAdd += "<option value = 'None'>None</option></select>";
	document.getElementById(pgid).innerHTML = toAdd;	
}

