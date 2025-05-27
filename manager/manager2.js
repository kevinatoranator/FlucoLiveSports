


  
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
const SPORTTYPE = {
	Half: 1,
	Quarter: 2,
	Inning: 3,
	Set: 4
}
var gameType = SPORTTYPE.Half;

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
			time: formattedTime},
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
  
  
  
 window.onload=function(){
	 timerSet();
	 document.getElementById("timerControl").addEventListener('click', function(){	
		if(started == false){
			currentSeconds = ($("#minutes :selected").val() * 60) + parseInt($("#seconds :selected").val());
			started = true;
			expected = Date.now() + interval;
			document.getElementById("timerControl").innerText = "Stop";
			setTimer(currentSeconds);
		  setTimeout(step, interval);
		}else{
			timerSet();
		}
	  }, false);
	  
	  document.getElementById("timerReset").addEventListener('click', function(){	
		currentSeconds = seconds;
		if(started == true){
			setTimer(currentSeconds);
		}else{
			timerSet();
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
				document.getElementById("manager").innerHTML = "<button id='play'>Add Play</button>";
				document.getElementById("play").addEventListener('click', play);
				document.getElementById("timerr").classList.remove("hidden");
				document.getElementById("goalpitch").classList.remove("hidden");
				document.getElementById("complete").classList.remove("hidden");
				document.getElementById("completeBtn").addEventListener('click', complete);
				//document.getElementById("goalpitch").innerText = "Goalie:";
				goalpitchSelect(home);
				goalpitchSelect(away);
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
				document.getElementById("teamSelect").innerHTML += `<input type="radio" onclick="teamSelect(this)" id=${home} name="team" value=${home} ><label for=${home} >${home}</label><br><input type="radio" onclick="teamSelect(this)" id=${away} name="team" value=${away} ><label for=${away} >${away}</label><br>`;
				
				document.getElementById("submit").addEventListener('click', submitPlay);
				document.getElementById("cancel").addEventListener('click', cancel);
				
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
	
	var playPeriod = 1;//TEMP FOR NOW
	var playTime = Math.floor(currentSeconds/60) + ":" + zeroPad(currentSeconds%60, 2);
	if(table == "soccer"){
		if(level == "jv"){
			playTime = playPeriod * 35 - Math.floor(currentSeconds/60);
		}else{
			playTime = playPeriod * 40 - Math.floor(currentSeconds/60);
		}
	}
	var infoArray = [playSelect, teamSelect, oppTeam, playerSelect, playPeriod, playTime, sportID, goalie];
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
				scores = dataArray.scores;
				document.getElementById("scoreTable").innerHTML = scoreTableText(scores);
				document.getElementById("scoreTableManual").innerHTML = scoreTableManualText(scores);
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
	var rosterHTML = "";
	for(var name in roster){
		rosterHTML += `${roster[name]} | ${name} <br/>`;
	}
	if(roster.length == 0){
		rosterHTML = "No Roster Available";
	}
	return rosterHTML;
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




function teamSelect(team){
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
		if(play == "Goal scored by "){
			document.getElementById("bonusSelect").innerHTML += "<b>Assist</b><br>";
			for(const [key, value] of Object.entries(playerList[teamSelect])){
				document.getElementById("bonusSelect").innerHTML += `<input type="radio" id="${key}" name="bonusPlayer" value="${key}" ><label for="${key}" >${key}</label><br>`;
			}
		}else if(play == "Faceoff won by "){
			document.getElementById("bonusSelect").innerHTML += "<b>Vs</b><br>";
			for(const [key, value] of Object.entries(playerList[opp])){
				document.getElementById("bonusSelect").innerHTML += `<input type="radio" id="${key}" name="bonusPlayer" value="${key}" ><label for="${key}" >${key}</label><br>`;
			}
		}else if(play == "Turnover by "){
			document.getElementById("bonusSelect").innerHTML += "<b>Opp</b><br>";
			for(const [key, value] of Object.entries(playerList[opp])){
				document.getElementById("bonusSelect").innerHTML += `<input type="radio" id="${key}" name="bonusPlayer" value="${key}" ><label for="${key}" >${key}</label><br>`;
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
				homeRoster = dataArray.homeRoster;
				awayRoster = dataArray.awayRoster;
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
				//gameInfoText += "Scores: " + dataArray.scores + "</br>";
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
