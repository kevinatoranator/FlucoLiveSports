


  
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
const SPORTTYPE = {
	Half: 2,
	Quarter: 4,
	Inning: 7,
	Set: 5
}
var gameType = SPORTTYPE.Half;
var liveinfo = ["", "", "", "", "", "", "", "", ""];

//BATBALL VARIABLES
var strikes = 0;
var balls = 0;
var outs = 0;
var pitches = 1;
var batter = "";

  function step(){
	  if(started == false || currentSeconds < 0){
		  return;
	  }
	  var dt = Date.now() - expected;
	  currentSeconds--;
	  setTimer(currentSeconds);
	  
	  if(currentSeconds%60 == 0){
		  if(table == "soccer"){
				if(level == "jv"){
					formattedTime = lastPeriod * 35 - Math.floor(currentSeconds/60);
				}else{
					formattedTime = lastPeriod * 40 - Math.floor(currentSeconds/60);
				}
			}else{
				formattedTime = Math.floor(currentSeconds/60) + ":" + zeroPad(currentSeconds%60, 2);
			}
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
	 timerSet();
	 periodSet();
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
			timerSet();
			periodSet();
		}
	  }, false);
	  
	  document.getElementById("timerReset").addEventListener('click', function(){	
		currentSeconds = seconds;
		if(started == true){
			setTimer(currentSeconds);
		}else{
			timerSet();
			periodSet();
		}
	  }, false);
	  
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
				document.getElementById("manager").innerHTML = "<button id='play'>Add Play</button>";
				document.getElementById("play").addEventListener('click', play);
				document.getElementById("timerr").classList.remove("hidden");
				if(table == "batball"){
					liveinfo[1] = 0;
					liveinfo[2] = 0;
					liveinfo[3] = 0;
					document.getElementById("timerr").innerHTML = 'Inning: <span id = "period"></span> <span id = "side"></span> | <button id="strike">Strike</button> <button id="ball">Ball</button> <button id="foul">Foul</button>';
					document.getElementById("strike").addEventListener('click', strike);
					document.getElementById("ball").addEventListener('click', ball);
					document.getElementById("foul").addEventListener('click', foul);
					if(formattedTime == "Bot"){
						document.getElementById("side").innerHTML= "<select name = 'sides' id = 'sides'><option value='Top'>Top</option><option value='Bot' selected>Bot</option></select>";
					}else{
						document.getElementById("side").innerHTML= "<select name = 'sides' id = 'sides'><option value='Top'>Top</option><option value='Bot'>Bot</option></select>";
					}
					batballPlay();
				}else if(table == "volleyball"){
					document.getElementById("timerr").innerHTML = 'Set: <span id = "period"></span>';
				}
				periodSet();
				document.getElementById("goalpitch").classList.remove("hidden");
				document.getElementById("complete").classList.remove("hidden");
				document.getElementById("completeBtn").addEventListener('click', complete);
				if(table != "basketball"){
					goalpitchSelect(home);
					goalpitchSelect(away);
					}
					loadGame();	
				}
			});		
	  }, false);
	  loadGame();
	  //manager start button -> creates game -> becomes play button -> gets time/period  -> becomes submit button -> then allows inserting of play -> back to play button
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
				manager.innerHTML = "<button id='submit'>Submit</button><br><br><div class=\"flex justify-between\"><div id=\"play\"></div><div id=\"teamSelect\"></div><div id=\"rosterSelect\"></div><div id=\"bonusSelect\"></div></div><br><button id='cancel'>Cancel</button>";
				dataArray.forEach(function(item){
					document.getElementById("play").innerHTML += item;
				});
				document.getElementById("teamSelect").innerHTML += `<input type="radio" onclick="teamSelection(this)" id=${home} name="team" value=${home} ><label for=${home} >${home}</label><br><input type="radio" onclick="teamSelection(this)" id=${away} name="team" value=${away} ><label for=${away} >${away}</label><br>`;
				
				document.getElementById("submit").addEventListener('click', submitPlay);
				document.getElementById("cancel").addEventListener('click', cancel);
				
				}
			});
}

function batballPlay(){
	$.ajax({
			url: "play.php",
			data: {table: table,
			gameID: gameID},
			success: function(data){
				var dataArray = $.parseJSON(data)
				console.log(lastPeriod + formattedTime);
				document.getElementById("manager").innerHTML = "<button id='submit'>Submit</button><br><br><div class=\"flex justify-between\">Batter: <div id=\"teamSelect\"></div><div id=\"rosterSelect\"></div><div id=\"play\"></div><div id=\"bonusSelect\"></div></div><br>";
				document.getElementById("teamSelect").innerHTML += `<input type="radio" onclick="teamSelection(this)" id=${away} name="team" value=${away} ><label for=${away} >${away}</label><br><input type="radio" onclick="teamSelection(this)" id=${home} name="team" value=${home} ><label for=${home} >${home}</label><br>`;
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
	var goalie = $("#homegoalpitchsel :selected").val();
	if(teamSelect == home){
		goalie = $("#awaygoalpitchsel :selected").val();
	}
	var assister = $("input[name='assistPlayer']:checked").val();
	var defense = $("input[name='defPlayer']:checked").val();
	if(assister == undefined){
		assister = "";
	}
	if(defense == undefined){
		defense = "";
	}
	
	var playTime = Math.floor(currentSeconds/60) + ":" + zeroPad(currentSeconds%60, 2);
	if(table == "soccer"){
		if(level == "jv"){
			playTime = lastPeriod * 35 - Math.floor(currentSeconds/60);
		}else{
			playTime = lastPeriod * 40 - Math.floor(currentSeconds/60);
		}
	}else if(table =="batball"){
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
						periodSet();
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
			playTime = formattedTime;
	}else{
		playTime = Math.floor(currentSeconds/60) + ":" + zeroPad(currentSeconds%60, 2);
	}
	var infoArray = [playSelect, teamSelect, oppTeam, playerSelect, lastPeriod, playTime, sportID, goalie, assister, defense, batter];
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
				document.getElementById("manager").innerHTML = "<button id='play'>Add Play</button>";
				document.getElementById("play").addEventListener('click', play);
				if(table == "batball"){
					batballPlay();
				}
				scores = dataArray.scores;
				document.getElementById("scoreTable").innerHTML = scoreTableText(scores);
				document.getElementById("scoreTableManual").innerHTML = scoreTableManualText(scores);
				}
			});
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
function cancel(){
	console.log("Play cancelled");
	document.getElementById("manager").innerHTML = "<button id='play'>Add Play</button>";
	document.getElementById("play").addEventListener('click', play);
}

function complete(){
	console.log("Game Completed");
	var infoArray = [sportID, home, away, sumArrayRange(scores, 0, scores.length/2), sumArrayRange(scores, scores.length/2, scores.length)];
	$.ajax({
			url: "complete.php",
			data: {table: table,
			gameID: gameID,
			infoArray: infoArray},
			success: function(data){
				var dataArray = $.parseJSON(data)
				console.log(dataArray);
				document.getElementById("manager").innerHTML = "Game Complete";
				document.getElementById("complete").classList.add("hidden");
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
			var val = sumArrayRange(scoreArray, Math.abs(row - 3) * (scoreArray.length/2), Math.abs(row - 4) * (scoreArray.length/2)); //Calculates total of scores in that subsection of array
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
						tableText += `<td><input type='number' oninput='updateScore(this)' id=${idName} name = ${idName} min = '0' max = '99' value =${val}></td>`;
					}else{//Gets correct score value depending on row 
						var idName = 'homePeriod' + (col + 1)/2 + 'Score';
						var val = scoreArray[(col-1)/2 + (Math.abs(row - 3) * (scoreArray.length/2))];
						tableText += `<td><input type='number' oninput='updateScore(this)' id=${idName} name = ${idName} min = '0' max = '99' value =${val}></td>`;
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
			var val = sumArrayRange(scoreArray, Math.abs(row - 3) * (scoreArray.length/2), Math.abs(row - 4) * (scoreArray.length/2)); //Calculates total of scores in that subsection of array
			tableText += `<td> | </td> <td> ${val} </td> `;
		}
		tableText +="</tr>";
	}
	
	tableText +="</table>";
	return tableText;
}

function rosterFormat(roster){
	//roster = rosterSort(roster);	
	var rosterHTML = "";
	var length = 0;
	for(var name in roster){
		length++;
		rosterHTML += `${roster[name]} | ${name}  <br/>`;
	}
	if(length == 0){
		rosterHTML = "No Roster Available";
	}
	return rosterHTML;
}

function rosterSort(obj){
	var entries = Object.entries(obj);
    entries.sort(([, valueA], [, valueB]) => valueA - valueB); // Sort in ascending order

	const sortedObject = {};
	for (const [key, value] of entries) {
		sortedObject[key] = value;
	}
	return sortedObject;
}

function sumArrayRange(array, start, end){
	var sum = 0;
	for(var i = start; i < end; i++){
		sum += parseInt(array[i]);
	}
	return sum;	
}

function timerSet(){
	started = false;
	document.getElementById("timerControl").innerText = "Start";
	var toAdd = "";
	var manMinutes = Math.floor(currentSeconds/60);
	var manSeconds = currentSeconds%60;
	toAdd += "<select name = 'minutes' id = 'minutes'>";
	for(var i = 0; i <= minutes; i++){
		if(i == manMinutes){
			toAdd += `<option value="${i}" selected>${i}</option>`;
		}else{
			toAdd += `<option value="${i}">${i}</option>`;
		}
	}
	toAdd += "</select>:<select name = 'seconds' id = 'seconds'>";
	for(var i = 0; i < 60; i++){
		if(i == manSeconds){
			toAdd += `<option value="${i}" selected>${i}</option>`;
		}else{
			toAdd += `<option value="${i}">${i}</option>`;
		}
	}
	toAdd += "</select>";
	document.getElementById("timer").innerHTML = toAdd;
}
function periodSet(){
	var toAdd = "";
	var manPeriod = lastPeriod;
	console.log(gameType+1);
	toAdd += "<select name = 'periods' id = 'periods'>";
	for(var i = 1; i <= gameType+1; i++){
		if(i == manPeriod){
			toAdd += `<option value="${i}" selected>${i}</option>`;
		}else{
			toAdd += `<option value="${i}">${i}</option>`;
		}
	}
	toAdd += "</select>";
	document.getElementById("period").innerHTML = toAdd;
}




function teamSelection(team){
	var playerList = {[home]: homeRoster, [away]: awayRoster};
	var playSelect = $("input[name='plays']:checked").val();
	document.getElementById("rosterSelect").innerHTML = "";
	for(const [key, value] of Object.entries(playerList[team.value])){
		document.getElementById("rosterSelect").innerHTML += `<input type="radio" id=${key} name="player" value="${key}" ><label for=${key} >${key}</label><br>`;
	}
	bonusAction(playSelect);	
}

function goalpitchSelect(team){
	var playerList = {[home]: homeRoster, [away]: awayRoster};
	var pgid = "";
	if(team == home){
		pgid = "homegoalpitch";
	}else{
		pgid = "awaygoalpitch";
	}
	//document.getElementById(pgid).innerHTML = "";
	var toAdd = "";
	toAdd += `<select name = ${pgid}sel' id = '${pgid}sel'>`;
	for(const [key, value] of Object.entries(playerList[team])){
		toAdd += `<option value="${key}">${key}</option>`;
	}
	toAdd += "<option value = 'None'>None</option></select>";
	document.getElementById(pgid).innerHTML = toAdd;	
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
		if(play == "Goal scored by " || play == "Jumper by " || play == "Layup by " || play == "Dunk by " || play == "3 Pointer by " || play == "Kill by "){
			document.getElementById("bonusSelect").innerHTML += "<b>Assist</b><br>";
			for(const [key, value] of Object.entries(playerList[teamSelect])){
				document.getElementById("bonusSelect").innerHTML += `<input type="radio" id="${key}" name="assistPlayer" value="${key}" ><label for="${key}" >${key}</label><br>`;
			}
		}else if((play == "Shot by " && table == "soccer") || (play == "Shot on goal by " && table == "field_hockey") || play == "Attack error by "){
			document.getElementById("bonusSelect").innerHTML += "<b>Block</b><br>";
			for(const [key, value] of Object.entries(playerList[opp])){
				document.getElementById("bonusSelect").innerHTML += `<input type="radio" id="${key}" name="defPlayer" value="${key}" ><label for="${key}" >${key}</label><br>`;
			}
		}else if(play == "Faceoff won by "){
			document.getElementById("bonusSelect").innerHTML += "<b>Vs</b><br>";
			for(const [key, value] of Object.entries(playerList[opp])){
				document.getElementById("bonusSelect").innerHTML += `<input type="radio" id="${key}" name="defPlayer" value="${key}" ><label for="${key}" >${key}</label><br>`;
			}
		}else if(play == "Turnover by "){
			document.getElementById("bonusSelect").innerHTML += "<b>Opp</b><br>";
			for(const [key, value] of Object.entries(playerList[opp])){
				document.getElementById("bonusSelect").innerHTML += `<input type="radio" id="${key}" name="defPlayer" value="${key}" ><label for="${key}" >${key}</label><br>`;
			}
		}
	}
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
				homeRoster = rosterSort(dataArray.homeRoster)
				awayRoster = rosterSort(dataArray.awayRoster)
				sportID = dataArray.sportID;
				table = dataArray.table;
				level = dataArray.level;
				minutes = dataArray.minutes;
				seconds = minutes * 60;
				currentSeconds = seconds;
				timerSet();
				completed = parseInt(dataArray.completed);
				
				gameInfoText += dataArray.sport + "</br>";
				gameInfoText += "Home: " + dataArray.home + "</br>";
				gameInfoText += "Away: " + dataArray.away + "</br>";
				document.getElementById("gameInfo").innerHTML = gameInfoText;
				if(scores != null){
					document.getElementById("scoreTable").innerHTML = scoreTableText(scores);
					document.getElementById("scoreTableManual").innerHTML = scoreTableManualText(scores);
				}
				document.getElementById("homeRoster").innerHTML = "-Home Roster-<br>" + rosterFormat(homeRoster);
				document.getElementById("awayRoster").innerHTML = "-Away Roster-<br>" + rosterFormat(awayRoster);
				if(table == "soccer"){
					gameType = SPORTTYPE.Half;
				}else if(table == "blax" || table == "glax" || table == "football" || table == "basketball" || table == "field_hockey"){
					formattedTime = Math.floor(currentSeconds/60) + ":" + zeroPad(currentSeconds%60, 2);
					gameType = SPORTTYPE.Quarter;
				}else if(table == "batball"){
					formattedTime = "Top";
					gameType = SPORTTYPE.Inning;
				}
				document.getElementById("gameLoad").innerText = "Reload Data";
				
				if(completed == 1){
					  document.getElementById("manager").innerHTML = "Game Complete";
				  }
				}
			});
		document.getElementById("gameInfo").innerHTML = gameInfoText;
}

function updateScore(score){
	if(gameType == SPORTTYPE.Half){
		if(score.name.includes("home")){
			if(score.name.includes("1")){
				scores[0] = score.value;
			}else if(score.name.includes("2")){
				scores[1] = score.value;
			}else if(score.name.includes("3")){
				scores[2] = score.value;
			}
			
		}else if(score.name.includes("away")){
			if(score.name.includes("1")){
				scores[3] = score.value;
			}else if(score.name.includes("2")){
				scores[4] = score.value;
			}else if(score.name.includes("3")){
				scores[5] = score.value;
			}
		}
	}else if(gameType == SPORTTYPE.Quarter){
		if(score.name.includes("home")){
			if(score.name.includes("1")){
				scores[0] = score.value;
			}else if(score.name.includes("2")){
				scores[1] = score.value;
			}else if(score.name.includes("3")){
				scores[2] = score.value;
			}else if(score.name.includes("4")){
				scores[3] = score.value;
			}else if(score.name.includes("5")){
				scores[4] = score.value;
			}
			
		}else if(score.name.includes("away")){
			if(score.name.includes("1")){
				scores[5] = score.value;
			}else if(score.name.includes("2")){
				scores[6] = score.value;
			}else if(score.name.includes("3")){
				scores[7] = score.value;
			}else if(score.name.includes("4")){
				scores[8] = score.value;
			}else if(score.name.includes("5")){
				scores[9] = score.value;
			}
		}
	}else if(gameType == SPORTTYPE.Inning){
		if(score.name.includes("home")){
			if(score.name.includes("1")){
				scores[0] = score.value;
			}else if(score.name.includes("2")){
				scores[1] = score.value;
			}else if(score.name.includes("3")){
				scores[2] = score.value;
			}else if(score.name.includes("4")){
				scores[3] = score.value;
			}else if(score.name.includes("5")){
				scores[4] = score.value;
			}else if(score.name.includes("6")){
				scores[5] = score.value;
			}else if(score.name.includes("7")){
				scores[6] = score.value;
			}else if(score.name.includes("8")){
				scores[7] = score.value;
			}
			
		}else if(score.name.includes("away")){
			if(score.name.includes("1")){
				scores[8] = score.value;
			}else if(score.name.includes("2")){
				scores[9] = score.value;
			}else if(score.name.includes("3")){
				scores[10] = score.value;
			}else if(score.name.includes("4")){
				scores[11] = score.value;
			}else if(score.name.includes("5")){
				scores[12] = score.value;
			}else if(score.name.includes("6")){
				scores[13] = score.value;
			}else if(score.name.includes("7")){
				scores[14] = score.value;
			}else if(score.name.includes("8")){
				scores[15] = score.value;
			}
		}
	}
	console.log(gameID, table, scores);
	$.ajax({
			url: "update.php",
			data: {gameID: gameID,
			table: table,
			scores: scores},
			success: function(data){
				var dataArray = $.parseJSON(data);
				console.log(dataArray);
				}
			});
	document.getElementById("scoreTable").innerHTML = scoreTableText(scores);
	document.getElementById("scoreTableManual").innerHTML = scoreTableManualText(scores);
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
	teamSelect = $("input[name='team']:checked").val();
	var oppTeam = $("input[name='team']:not(:checked)").val();
	lastPeriod = $("#periods :selected").val();
	formattedTime = $("#sides :selected").val();
	batter = $("input[name='player']:checked").val();
	liveinfo[5] = batter;
	liveinfo[4] = $("#homegoalpitchsel :selected").val();
	if(teamSelect == home){
		liveinfo[4] = $("#awaygoalpitchsel :selected").val();
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
