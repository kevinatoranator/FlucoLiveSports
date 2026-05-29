import * as suv from "./Utilities.js";

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
var serve = "";
var lastPeriod = 1;
var completed = 0;
var playerList;
var hTotal = 0;
var aTotal = 0;


var liveinfo = ["", "", "", "", "", "", "", "", ""];


const SPORTTYPE = {
	Half: 2,
	Quarter: 4,
	Inning: 7,
	Set: 5
}
var gameType = SPORTTYPE.Set;

const setPeriod = (period) => document.getElementById("period").innerText = period;


window.onload=function(){
	 suv.periodSet(lastPeriod, gameType);
	  
	  document.getElementById("gameLoad").addEventListener('click', loadGame, false);
	  document.getElementById("manager").innerHTML = "<button id='start'>Start Game</button>";
	  document.getElementById("start").addEventListener('click', function(){
		var gameInfoText = "";
		$.ajax({
			url: "start.php",
			data: {table: table,
			gameID: gameID,
			time: serve},
			success: function(data){
				console.log(data);
				console.log(table);
				document.getElementById("timing").classList.remove("hidden");
				document.getElementById("serveTeam").innerHTML= `<input type="radio" id='${away}service' name="serviceTeam" value=${away} ><label for='${away}service' >${away}</label><br><input type="radio" id='${home}service' name="serviceTeam" value=${home} ><label for='${home}service' >${home}</label>`;
				suv.periodSet(lastPeriod, gameType);
				document.getElementById("server").innerHTML = `Server:<div id="serverList"></div>`;
				document.getElementById("server").classList.remove("hidden");
				document.getElementById("complete").classList.remove("hidden");
				document.getElementById("completeBtn").addEventListener('click', function(){
					var infoArray = [sportID, home, away, suv.sumArrayRange(scores, 0, scores.length/2), suv.sumArrayRange(scores, scores.length/2, scores.length)];
					suv.complete(table, gameID, infoArray)});
				document.getElementById(`${away}service`).addEventListener('click', suv.soloSelect(away, "serverList", playerList), false);
				document.getElementById(`${home}service`).addEventListener('click', suv.soloSelect(home, "serverList", playerList), false);
				loadGame();
				play();				
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
				completed = parseInt(dataArray.completed);
				
				gameInfoText += dataArray.sport + "</br>";
				gameInfoText += `Home: ${dataArray.home}</br>`;
				gameInfoText += `Away: ${dataArray.away}</br>`;
				document.getElementById("gameInfo").innerHTML = gameInfoText;
				if(scores != null){
					document.getElementById("scoreTable").innerHTML = scoreTableText(scores);
					document.getElementById("scoreTableManual").innerHTML = scoreTableManualText(scores);
					eventAdder();
				}
				document.getElementById("homeRoster").innerHTML = "-Home Roster-<br>" + suv.rosterFormat(homeRoster);
				document.getElementById("awayRoster").innerHTML = "-Away Roster-<br>" + suv.rosterFormat(awayRoster);
				document.getElementById("gameLoad").innerText = "Reload Data";
				playerList = {[home]: homeRoster, [away]: awayRoster}
				if(completed == 1){
					  document.getElementById("manager").innerHTML = "Game complete";
				  }
				}
			});
		document.getElementById("gameInfo").innerHTML = gameInfoText;
}

function play(){
	$.ajax({
			url: "play.php",
			data: {table: table,
			gameID: gameID},
			success: function(data){
				var dataArray = $.parseJSON(data)
				console.log(lastPeriod + serve);
				document.getElementById("manager").innerHTML = "<button id='submit'>Submit</button><br><br><div class=\"flex justify-between\">Player: <div id=\"teamSelect\"></div><div id=\"rosterSelect\"></div><div id=\"play\"></div><div id=\"bonusSelect\"></div></div><br>";
				document.getElementById("teamSelect").innerHTML += `<input type="radio" id=${away} name="team" value=${away} ><label for=${away} >${away}</label><br><input type="radio" id=${home} name="team" value=${home} ><label for=${home} >${home}</label><br>`;
				document.getElementById(home).addEventListener('click', teamSelection);
				document.getElementById(away).addEventListener('click', teamSelection);
				dataArray.forEach(function(item){
						document.getElementById("play").innerHTML += item;
				});
				
				document.getElementById("submit").addEventListener('click', submitPlay);
							
				}
			});
	
}

function submitPlay(){
	console.log("Plays added");
	var playSelect = $("input[name='plays']:checked").val();
	var teamSelect = $("input[name='team']:checked").val();
	var oppTeam = $("input[name='team']:not(:checked)").val();
	var playerSelect = $("input[name='player']:checked").val();
	var assister = $("input[name='assistPlayer']:checked").val();
	var defense = $("input[name='defPlayer']:checked").val();
	if(assister == undefined){
		assister = "";
	}
	if(defense == undefined){
		defense = "";
	}
	
	lastPeriod = $("#periods :selected").val();
	var infoArray = [playSelect, teamSelect, oppTeam, playerSelect, lastPeriod, serve, sportID, "", assister, defense, level, "", "", "", ""];
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
				play();
				scores = dataArray.scores;
				document.getElementById("scoreTable").innerHTML = scoreTableText(scores);
				document.getElementById("scoreTableManual").innerHTML = scoreTableManualText(scores);
				eventAdder();
				}
			});
	$.ajax({
		url: "livegame.php",
		data: {table: table,
		gameID: gameID,
		time: serve,
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
						tableText += "<td>--</td>";
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
						var val = (col + 1) / 2;
						tableText += `<td>${val}</td>`;
					}else if(row == 1){
						tableText += "<td>--</td>";
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
			tableText += "<td>-</td><td>--</td>";
		}else{
			var val = 0;
			if(row == 2){
				val = aTotal;
			}else if (row == 3){
				val = hTotal;
			}
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
						tableText += "<td>--</td>";
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
						var val = (col + 1) / 2;
						tableText += `<td>${val}</td>`;
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
			var val = 0;
			if(row == 2){
				val = aTotal;
			}else if (row == 3){
				val = hTotal;
			}
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
	suv.updateScore(this, scores, gameType, gameID, table, hTotal, aTotal);
	hTotal = 0;
	aTotal = 0;
	if(parseInt(scores[0]) >= 25 && parseInt(scores[0]) > parseInt(scores[5]) + 1){
			hTotal += 1;
		}else if(parseInt(scores[5]) >= 25 && parseInt(scores[5]) > parseInt(scores[0]) + 1){
			aTotal += 1;
		}
		if(parseInt(scores[1]) >= 25 && parseInt(scores[1]) > parseInt(scores[6]) + 1){
			hTotal += 1;
		}else if(parseInt(scores[6]) >= 25 && parseInt(scores[6]) > parseInt(scores[1]) + 1){
			aTotal += 1;
		}
		if((parseInt(scores[2]) >= 25 && parseInt(scores[2]) > parseInt(scores[7]) + 1) || ((parseInt(scores[2]) >= 15 && parseInt(scores[2]) > parseInt(scores[7]) + 1) && level == "jv")){
			hTotal += 1;
		}else if(parseInt(scores[7]) >= 25 && parseInt(scores[7]) > parseInt(scores[2]) + 1 || ((parseInt(scores[7]) >= 15 && parseInt(scores[7]) > parseInt(scores[2]) + 1) && level == "jv")){
			aTotal += 1;
		}
		if(parseInt(scores[3]) >= 25 && parseInt(scores[3]) > parseInt(scores[8]) + 1){
			hTotal += 1;
		}else if(parseInt(scores[8]) >= 25 && parseInt(scores[8]) > parseInt(scores[3]) + 1){
			aTotal += 1;
		}
		if(parseInt(scores[4]) >= 15 && parseInt(scores[4]) > parseInt(scores[9]) + 1){
			hTotal += 1;
		}else if(parseInt(scores[9]) >= 15 && parseInt(scores[9]) > parseInt(scores[4]) + 1){
			aTotal += 1;
		}
		suv.updateScore(this, scores, gameType, gameID, table, hTotal, aTotal);
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
	var playerList = {[home]: homeRoster, [away]: awayRoster};
	var teamSelect = $("input[name='team']:checked").val();
	var opp = $("input[name='team']:not(:checked)").val();
	var playerSelect = $("input[name='player']:checked").val();
	if(teamSelect != undefined){
		if(play == "Kill by "){
			document.getElementById("bonusSelect").innerHTML += "<b>Assist</b><br>";
			for(const [key, value] of Object.entries(playerList[teamSelect])){
				document.getElementById("bonusSelect").innerHTML += `<input type="radio" id="${key}" name="assistPlayer" value="${key}" ><label for="${key}" >${key}</label><br>`;
			}
		}
	}
}

