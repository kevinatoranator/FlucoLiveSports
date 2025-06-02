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
var formattedTime = "Top";
var lastPeriod = 1;
var completed = 0;
var playerList;

var liveinfo = ["", "", "", "", "", "", "", "", ""];

//BATBALL VARIABLES
var strikes = 0;
var balls = 0;
var outs = 0;
var pitches = 1;
var batter = "";

const SPORTTYPE = {
	Half: 2,
	Quarter: 4,
	Inning: 7,
	Set: 5
}
var gameType = SPORTTYPE.Inning;

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
			time: formattedTime},
			success: function(data){
				console.log(data);
				console.log(table);
				document.getElementById("timing").classList.remove("hidden");
				liveinfo[1] = 0;
				liveinfo[2] = 0;
				liveinfo[3] = 0;
				document.getElementById("strike").addEventListener('click', strike);
				document.getElementById("ball").addEventListener('click', ball);
				document.getElementById("foul").addEventListener('click', foul);
				if(formattedTime == "Bot"){
					document.getElementById("side").innerHTML= "<select name = 'sides' id = 'sides'><option value='Top'>Top</option><option value='Bot' selected>Bot</option></select>";
				}else{
					document.getElementById("side").innerHTML= "<select name = 'sides' id = 'sides'><option value='Top'>Top</option><option value='Bot'>Bot</option></select>";
				}
				//batballPlay();
				suv.periodSet(lastPeriod, gameType);
				document.getElementById("pitching").innerHTML = `${home} Pitcher:<div id="homepitcher"></div>${away} Pitcher:<div id="awaypitcher"></div>`;
				document.getElementById("pitching").classList.remove("hidden");
				document.getElementById("complete").classList.remove("hidden");
				document.getElementById("completeBtn").addEventListener('click', suv.complete);
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
				suv.soloSelect(home, "homepitcher", playerList);
				suv.soloSelect(away, "awaypitcher", playerList);
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
				console.log(lastPeriod + formattedTime);
				document.getElementById("manager").innerHTML = "<button id='submit'>Submit</button><br><br><div class=\"flex justify-between\">Batter: <div id=\"teamSelect\"></div><div id=\"rosterSelect\"></div><div id=\"play\"></div><div id=\"bonusSelect\"></div></div><br>";
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
	var pitcher = $("#homepitchersel :selected").val();
	if(teamSelect == home){
		pitcher = $("#awaypitchersel :selected").val();
	}
	var assister = $("input[name='assistPlayer']:checked").val();
	var defense = $("input[name='defPlayer']:checked").val();
	if(assister == undefined){
		assister = "";
	}
	if(defense == undefined){
		defense = "";
	}
	

	if(playSelect != " to second" && playSelect != " to third" && playSelect != " scores" && playSelect != " out at first" && playSelect != " out at second" && playSelect != " out at third" && playSelect != " out at home"){
		batter = playerSelect;
	}else{
		batter = liveinfo[5];
	}
	if(home == teamSelect){
		formattedTime = "Bot";
	}
	if(playSelect == " strikes out looking" || playSelect == " strikes out swinging" || playSelect == " flies out" || playSelect == " pops out" || playSelect == " grounds out" || playSelect == " lines out" 
		|| playSelect == " sacrifice fly" || playSelect == " sacrifice bunt" || playSelect == " out at first" || playSelect == " out at second" || playSelect == " out at third" || playSelect == " out at home"){
		outs += 1;
		if(outs > 2){
			if(formattedTime == "Top"){
				formattedTime = "Mid";
			}else if(formattedTime == "Bot"){
				formattedTime = "End";
			}
			outs = 0;
			liveinfo[4] = "";
			liveinfo[5] = "";
			liveinfo[6] = "";
			liveinfo[7] = "";
			liveinfo[8] = "";
			if(formattedTime == "Mid"){
				document.getElementById("side").innerHTML= "<select name = 'sides' id = 'sides'><option value='Top'>Top</option><option value='Bot' selected>Bot</option></select>";
			}else{
				document.getElementById("side").innerHTML= "<select name = 'sides' id = 'sides'><option value='Top'>Top</option><option value='Bot'>Bot</option></select>";
				lastPeriod++;
				suv.periodSet(lastPeriod, gameType);
			}
		}
				
		strikes = 0;
		balls = 0;
		pitches = 0;
				
	}else if(playSelect == " walks" || playSelect == " hit by pitch"){
		if(liveinfo[6] != "" && liveinfo[7] != ""){
			liveinfo[8] = liveinfo[7];
		}
		if(liveinfo[6] != ""){
			liveinfo[7] = liveinfo[6];
		}
		liveinfo[6] = playerSelect;
		strikes = 0;
		balls = 0;
		pitches = 0;
	}else if(playSelect == " to second"){
		if(playerSelect == liveinfo[6]){
			liveinfo[6] = "";
		}
		liveinfo[7] = playerSelect;
	}else if(playSelect == " to third"){
		if(playerSelect == liveinfo[6]){
			liveinfo[6] = "";
		}if(playerSelect == liveinfo[7]){
			liveinfo[7] = "";
		}
		liveinfo[8] = playerSelect;
	}else if(playSelect == " scores"){
		if(playerSelect == liveinfo[6]){
			liveinfo[6] = "";
		}if(playerSelect == liveinfo[7]){
			liveinfo[7] = "";
		}if(playerSelect == liveinfo[8]){
			liveinfo[8] = "";
		}
	}else if(playSelect == " singles"){
		liveinfo[6] = playerSelect;
	}else if(playSelect == " doubles"){
		liveinfo[7] = playerSelect;
	}else if(playSelect == " triples"){
		liveinfo[8] = playerSelect;
	}
	if(playSelect == " out at first" || playSelect == " out at second" || playSelect == " out at third" || playSelect == " out at home"){
		if(playerSelect == liveinfo[6]){
			liveinfo[6] = "";
		}if(playerSelect == liveinfo[7]){
			liveinfo[7] = "";
		}if(playerSelect == liveinfo[8]){
			liveinfo[8] = "";
		}
	}
	liveinfo[0] = "";
	liveinfo[1] = outs;
	liveinfo[2] = strikes;
	liveinfo[3] = balls;
	lastPeriod = $("#periods :selected").val();
	var infoArray = [playSelect, teamSelect, oppTeam, playerSelect, lastPeriod, formattedTime, sportID, pitcher, assister, defense, batter];
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
		time: formattedTime,
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
	var playerList = {[home]: homeRoster, [away]: awayRoster};
	var teamSelect = $("input[name='team']:checked").val();
	var opp = $("input[name='team']:not(:checked)").val();
	var playerSelect = $("input[name='player']:checked").val();
	if(teamSelect != undefined){
		
	}
}


function strike(){
	if(strikes < 3){
		strikes++;
	}
	liveinfo[2] = strikes;
	liveinfo[0] += `P${pitches}: strike,`;
	pitch();
	console.log("Strike: " + strikes);
}
function ball(){
	if(balls < 4){
		balls++;
	}
	liveinfo[3] = balls;
	liveinfo[0] += `P${pitches}: ball,`;
	pitch();
	console.log("Ball: " + balls);
}
function foul(){
	
	if(strikes < 2){
		strikes++;
	}
	liveinfo[2] = strikes;
	liveinfo[0] += `P${pitches}: foul,`;
	pitch();
	console.log("Strike: " + strikes);
}
function pitch(){
	pitches++;
	var teamSelect = $("input[name='team']:checked").val();
	var oppTeam = $("input[name='team']:not(:checked)").val();
	lastPeriod = $("#periods :selected").val();
	formattedTime = $("#sides :selected").val();
	batter = $("input[name='player']:checked").val();
	liveinfo[5] = batter;
	liveinfo[4] = $("#homepitchersel :selected").val();
	if(teamSelect == home){
		liveinfo[4] = $("#awaypitchersel :selected").val();
	}
	var infoArray = ["pitch", teamSelect, oppTeam, "", "", "", sportID, liveinfo[4], "", "", batter];
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
			
	$.ajax({
			url: "submit.php",
			data: {table: table,
			gameID: gameID,
			infoArray: infoArray,
			home: home},
			success: function(data){
				console.log(data);
				}
			});	
}
