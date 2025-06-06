const SPORTTYPE = {
	Half: 2,
	Quarter: 4,
	Inning: 7,
	Set: 5
}

export function updateScore(score, scores, gameType, gameID, table){
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
}

export function periodSet(lastPeriod, gameType){
	var toAdd = "";
	console.log(gameType+1);
	toAdd += "<select name = 'periods' id = 'periods'>";
	for(var i = 1; i <= gameType+1; i++){
		if(i == lastPeriod){
			toAdd += `<option value="${i}" selected>${i}</option>`;
		}else{
			toAdd += `<option value="${i}">${i}</option>`;
		}
	}
	toAdd += "</select>";
	document.getElementById("period").innerHTML = toAdd;
}


export function timerSet(minutes, currentSeconds){
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
	return false;
}



export function rosterFormat(roster){
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

export function rosterSort(obj){
	var entries = Object.entries(obj);
    entries.sort(([, valueA], [, valueB]) => valueA - valueB); // Sort in ascending order

	const sortedObject = {};
	for (const [key, value] of entries) {
		sortedObject[key] = value;
	}
	return sortedObject;
}

export function sumArrayRange(array, start, end){
	var sum = 0;
	for(var i = start; i < end; i++){
		sum += parseInt(array[i]);
	}
	return sum;	
}


export function cancel(){
	console.log("Play cancelled");
	document.getElementById("manager").innerHTML = "<button id='play'>Add Play</button>";
	document.getElementById("play").addEventListener('click', play);
}

export function complete(table, gameID, infoArray){
	console.log("Game Completed");
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

export function soloSelect(team, pgid, playerList){
	var toAdd = "";
	toAdd += `<select name = ${pgid}sel' id = '${pgid}sel'>`;
	for(const [key, value] of Object.entries(playerList[team])){
		toAdd += `<option value="${key}">${key}</option>`;
	}
	toAdd += "<option value = 'None'>None</option></select>";
	if(document.getElementById(pgid) != null){
		document.getElementById(pgid).innerHTML = toAdd;	
	}
}
