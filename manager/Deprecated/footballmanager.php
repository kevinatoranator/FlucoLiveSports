<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<?php 
$date = date("Y-m-d", strtotime("today" ));
$fdate = date("l, F d", strtotime("today")); 

$gameID = $_GET['gameID'];
$isDistrict = $_GET['district'];
$phpURL = "footballmanager.php?gameID=".$gameID."&district=".$isDistrict;
$schedule = 'schedule';
$table = 'football';//GET SPORT FOR TABLE
$season = 2024;


function createGame($table, $db, $isDistrict){

}

function updateScoreNew($table, $scoreArray, $gameID, $db, $isDistrict){

	if($table == "football" or $table == "field_hockey"){//Sports with quarters 
		$hTotal = $scoreArray[0] + $scoreArray[1] + $scoreArray[2] + $scoreArray[3] + $scoreArray[4];
		$aTotal = $scoreArray[5] + $scoreArray[6] + $scoreArray[7] + $scoreArray[8] + $scoreArray[9];
		
		$sqls = "UPDATE $table SET home_q1 = '$scoreArray[0]', home_q2 = '$scoreArray[1]', home_q3 = '$scoreArray[2]', home_q4 = '$scoreArray[3]', home_ot = '$scoreArray[4]', home_total = '$hTotal', 
			away_q1 = '$scoreArray[5]', away_q2 = '$scoreArray[6]', away_q3 = '$scoreArray[7]', away_q4 = '$scoreArray[8]', away_ot = '$scoreArray[9]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
		
	}else if($table == "volleyball"){//volleyball (unique with sets)
		$hTotal = 0;
		$aTotal = 0;
		if($scoreArray[0] >= 25 and $scoreArray[0] > $scoreArray[5] + 1){
			$hTotal += 1;
		}else if($scoreArray[5] >= 25 and $scoreArray[5] > $scoreArray[0] + 1){
			$aTotal += 1;
		}
		if($scoreArray[1] >= 25 and $scoreArray[1] > $scoreArray[6] + 1){
			$hTotal += 1;
		}else if($scoreArray[6] >= 25 and $scoreArray[6] > $scoreArray[1] + 1){
			$aTotal += 1;
		}
		if($scoreArray[2] >= 25 and $scoreArray[2] > $scoreArray[7] + 1){
			$hTotal += 1;
		}else if($scoreArray[7] >= 25 and $scoreArray[7] > $scoreArray[2] + 1){
			$aTotal += 1;
		}
		if($scoreArray[3] >= 25 and $scoreArray[3] > $scoreArray[8] + 1){
			$hTotal += 1;
		}else if($scoreArray[8] >= 25 and $scoreArray[8] > $scoreArray[3] + 1){
			$aTotal += 1;
		}
		if($scoreArray[4] >= 25 and $scoreArray[4] > $scoreArray[9] + 1){
			$hTotal += 1;
		}else if($scoreArray[9] >= 25 and $scoreArray[9] > $scoreArray[4] + 1){
			$aTotal += 1;
		}
		
		$sqls = "UPDATE $table SET home_set1 = '$scoreArray[0]', home_set2 = '$scoreArray[1]', home_set3 = '$scoreArray[2]', home_set4 = '$scoreArray[3]', home_set5 = '$scoreArray[4]', home_total = '$hTotal',
		away_set1 = '$scoreArray[5]', away_set2 = '$scoreArray[6]', away_set3 = '$scoreArray[7]', away_set4 = '$scoreArray[8]', away_set5 = '$scoreArray[9]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
	}

}

function updateLiveGame($qrtr, $time, $poss, $homeTimeOuts, $awayTimeOuts, $sof, $yardline, $ytg, $gameID, $db){

	$sqls = "UPDATE live_games SET period = '$qrtr', game_time = '$time', info_1 = '$poss', info_2 = '$tohome', info_3 = '$toaway', info_4 = '$sof', info_5 = '$yardline', info_6 = '$ytg' WHERE schedule_id='$gameID'";
	$query = $db->prepare($sqls);
	$query->execute();

}

function completeGame($table, $homeID, $awayID, $sportID, $gameID, $db){
		$homeWins = 0;
		$homeLosses = 0;
		$homeTies = 0;
		$awayWins = 0;
		$awayLosses = 0;
		$awayTies = 0;
		
		$sqls = "UPDATE $table SET completed = 1 WHERE schedule_id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
		#select home team standing info
		$sqls = "SELECT wins AS w, losses AS l, ties AS t, school_id AS school, sport_id AS sport FROM standings AS st JOIN roster_teams AS t on st.sport_id=t.id WHERE st.school_id='$homeID' AND st.sport_id = '$sportID' AND st.season='$season'";
		$query = $db->prepare($sqls);
		$query->execute();
		while($row = $query->fetchObject()){
			$homeWins = $row->w;
			$homeLosses = $row->l;
			$homeTies = $row->t;
		}
		#select away team standing info
		$sqls = "SELECT wins AS w, losses AS l, ties AS t, school_id AS school, sport_id AS sport FROM standings AS st JOIN roster_teams AS t on st.sport_id=t.id WHERE st.school_id='$awayID' AND st.sport_id = '$sportID' AND st.season='$season'";
		$query = $db->prepare($sqls);
		$query->execute();
		while($row = $query->fetchObject()){
			$awayWins = $row->w;
			$awayLosses = $row->l;
			$awayTies = $row->t;
		}
		#update standings
		if($hTotal > $aTotal){
			$homeWins += 1;
			$awayLosses += 1;
		}else if($aTotal > $hTotal){
			$homeLosses += 1;
			$awayWins += 1;
		}else{
			$homeTies += 1;
			$awayTies += 1;
		}
		$sqls = "UPDATE standings SET wins = '$homeWins', losses = '$homeLosses', ties = '$homeTies' WHERE school_id='$homeID' AND sport_id = '$sportID' AND season='$season' ";
		$query = $db->prepare($sqls);
		$query->execute();
		$sqls = "UPDATE standings SET wins = '$awayWins', losses = '$awayLosses', ties = '$awayTies' WHERE school_id='$awayID' AND sport_id = '$sportID' AND season='$season' ";
		$query = $db->prepare($sqls);
		$query->execute();
		
		#remove from livegame
		$sql = "DELETE FROM live_games WHERE schedule_id='$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
}

?>


<div class="flex justify-between">
        <a href="../gmindex.php">Return to Home</a>
        <a href='<?php echo $phpURL?>'>Reload</a>
    </div>
<h1>Today's Games</h1>


<!--Schedule Body-->

<?php
	include '../include/database.php';


	//include default variables?
	//ALL SPORTS
	$sport = "";
	$home = "";
	$away = "";
	$roster = array();
	$pbpEntries = array();
	$homeID = 0;
	$completed = 0;
	
	//MOST SPORTS (Not bat or vball)
	$minutes = 0;
	$seconds = 0;
	$maximumMinutes = 99;//Maximum minutes for selection
	
	$quarter = 1;
	
	//Football
	$homeTimeOuts = 3;
	$awayTimeOuts = 3;
	
	//temp for test of live info fb
	$startTime = "15:00";
	$sof = "own";
	$yardline = 25;
	$ytg = 10;
	$poss = "";
	
	//live info volley
	$set = 1;
	$serve = "FCHS";
	//$homeTO = 3;
	//$awayTO = 3;
	
	
	if($isDistrict=="true"){
		$schedule = 'schedule_other';
		$table = '_other';
	}
    
	$sql = "SELECT t.urlName AS sport FROM $schedule AS s JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id = '$gameID'";
	
	try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	
	if($_POST && isset($_POST['hq1score'])){
		
		$scoreArray = [$_POST['hq1score'], $_POST['hq2score'], $_POST['hq3score'], $_POST['hq4score'], $_POST['hOTscore'],
					$_POST['aq1score'], $_POST['aq2score'], $_POST['aq3score'], $_POST['aq4score'], $_POST['aOTscore']];
		
		updateScoreNew("football", $scoreArray, $gameID, $db, $isDistrict);
	}
	
	if($_POST && isset($_POST['pbpRemove'])){
		$remove = $_POST['pbpRemove'];
		$sqls = "DELETE FROM football_pbp WHERE id='$remove'";
		$query = $db->prepare($sqls);
		$query->execute();
	}
	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sport = $row->sport;
	}
	
	if($sport == "football" or $sport == "jvfootball"){
		$table = "football" + $table;
	}else if($sport == "fhockey" or $sport == "jvfhockey"){
		$table = "field_hockey" + $table;
	}else if($sport == "vball" or "jvvball"){
		$table = "volleyball" + $table;
	}
	
	//Create new game if doesn't exist
	$sql = "SELECT 1 FROM $table AS fb JOIN $schedule AS s ON fb.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	if($query->rowCount() == 0){
		$sql = "INSERT INTO $table (home_q1, home_q2, home_q3, home_q4, home_ot, away_q1, away_q2, away_q3, away_q4, away_ot, home_total, away_total, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM $schedule WHERE id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
	}
		
	if($sport=="football"){
		$minutes = 15;
		$seconds = 0;
		$maximumMinutes = 15;
	}else if($sport=="jvfootball"){
		$minutes = 12;
		$seconds = 0;
		$maximumMinutes = 12;
	}
	
	$sqlsport = "SELECT s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id as aNum, s.team_id AS team, t.formattedName, 
		fb.home_q1 AS hq1, fb.home_q2 AS hq2, fb.home_q3 AS hq3, fb.home_q4 AS hq4, fb.home_ot AS hot, fb.home_total AS ht, 
		fb.away_q1 AS aq1, fb.away_q2 AS aq2, fb.away_q3 AS aq3, fb.away_q4 AS aq4, fb.away_ot AS aot, fb.away_total AS at, fb.completed AS cmp
		FROM $table AS fb JOIN $schedule AS s ON fb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	$query = $db->prepare($sqlsport);
	$query->execute();
	while($row = $query->fetchObject()){
		printf("<h3>Game Page Preview</h3>");
		printf("<hr>");
		printf("<h5>%s</h5><br>", $row->formattedName);
		
		$homeID = $row->hNum;
		$awayID = $row->aNum;
		$sportID = $row->team;
			
		$homeTeam = $row->home;
		$awayTeam = $row->away;

		
		$scoreArray = [$row->hq1, $row->hq2, $row->hq3, $row->hq4, $row->hot, $row->aq1, $row->aq2, $row->aq3, $row->aq4, $row->aot];
		
		$hTotal = $row->ht;
		$aTotal = $row->at;
		
		$completed = $row->cmp;
	}
	//Create live game
	$sql = "SELECT * FROM live_games AS game JOIN $schedule AS s ON game.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();

	if($query->rowCount() == 0){
		//game_time = time, period = qrtr, info1 = poss, info2 = tohome, info3 = toaway, info4 = sof, info5 = yardline, info6 = ytg
		$sql = "INSERT INTO live_games (period, game_time, info_1, info_2, info_3, info_4, info_5, info_6, schedule_id) VALUES (1, '$startTime', '$poss', '$tohome', '$toaway', '$sof', '$yardline', '$ytg', (SELECT id FROM $schedule WHERE id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
	}else{
		while($row = $query->fetchObject()){
			$period = $row->period;
			$poss = $row->info_1;
			$homeTimeOuts = $row->info_2;
			$awayTimeOuts = $row->info_3;
			$sof = $row->info_4;
			$yardline = $row->info_5;
			$ytg = $row->info_6;
			//$displayTime = $row->game_time; proably don't need to pull this
		}
	}
	
	if($_POST && isset($_POST['action'])){
		$quarterText = $_POST['quarter'];
		$action = $_POST['action'];
		$team = $_POST['team'];
		$quarter = $_POST['quarter'];
		$minutes = $_POST['minutes'];
		$seconds = $_POST['seconds'];
		$yards = $_POST['yards'];
		
		//live game yards update
		if($sof == "own"){
			$yardline = $yardline+$yards;
		}else{
			$yardline = $yardline-$yards;
		}
		if($yardline > 50){
			$oppoyards = $yardline - 50;
			if($sof == "own"){
				$sof = "opp";
			}else{
				$sof = "own";
			}
		}else if($yardline <= 0){
			$yardline = 0;
		}
		
		$ytg = $ytg - $yards;
		if($ytg <= 0){
			$ytg = 10;
		}
		
		if(strlen($seconds) < 2){
			$seconds = "0" . $seconds;
		}
		
		$displayTime = $minutes . ":" . $seconds;
		
		$actionText = "";
		if($quarter == 5){
			$quarterText = "OT";
		}else{
			$quarterText = "Q" . $quarterText;
		}
		if($_POST['team'] == "FCHS"){
			$pbp = $quarterText . " " . $displayTime . " | " . $action . $_POST['player'] . " (" . $team . ")";
			$actionText = $action . $_POST['player'] . " (" . $team . ")";
		}else{
			$pbp = $quarterText . " " . $displayTime . " | " . $action . $team;
			$actionText = $action . $team;
		}
		$actionText = $actionText . " for " .  $yards . " yards";
		//POST PBP SQL
		$sql = "INSERT INTO football_pbp (text, quarter, time, game_id) VALUES ('$actionText', '$quarterText', '$displayTime', (SELECT id FROM $schedule where id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
		
		$scoreAmount = 0;
		
		switch($action){
			case "Touchdown by ":
				$scoreAmount = 6;
				break;
			case "Extra point GOOD by ":
				$scoreAmount = 1;
				break;
			case "2-point conversion GOOD by ":
				$scoreAmount = 2;
				break;
			case "Field goal GOOD by ":
				$scoreAmount = 3;
				break;
			case "Safety by ":
				$scoreAmount = 2;
				break;
			case "Timeout by ":
				if($team == $homeTeam){
					$homeTimeOuts = $homeTimeOuts - 1;
				}else{
					$awayTimeOuts = $awayTimeOuts - 1;
				}
				break;
		}
		
		if($scoreAmount != 0){
			if(($homeID == 1 && $team == "FCHS") or ($homeID != 1 && $team != "FCHS")){
				switch($quarter){
					case 1:
						$scoreArray[0] += $scoreAmount;
						break;
					case 2:
						$scoreArray[1] += $scoreAmount;
						break;
					case 3:
						$scoreArray[2] += $scoreAmount;
						break;
					case 4:
						$scoreArray[3] += $scoreAmount;
						break;
					case 5:
						$scoreArray[4] += $scoreAmount;
						break;
				}
			}else{
				switch($quarter){
					case 1:
						$scoreArray[5] += $scoreAmount;
						break;
					case 2:
						$scoreArray[6] += $scoreAmount;
						break;
					case 3:
						$scoreArray[7] += $scoreAmount;
						break;
					case 4:
						$scoreArray[8] += $scoreAmount;
						break;
					case 5:
						$scoreArray[9] += $scoreAmount;
						break;
				}
			}
			updateScoreNew("football", $scoreArray, $gameID, $db, $isDistrict);
		}
		$minutes = $_POST['minutes'];
		$seconds = $_POST['seconds'];
		$quarter = $_POST['quarter'];
		//echo $pbp;
		
		//possesssion not necessarily team if sack or penalty
		//not sure how to do own/opp side of field without prompting user, don't want user to have to put in the yard line each time
		updateLiveGame($quarter, $displayTime, $team, $homeTimeOuts, $awayTimeOuts, $sof, $yardline, $ytg, $gameID, $db);
	}
	
	if($_POST && isset($_POST['complete'])){
		completeGame($sport, $homeID, $awayID, $sportID, $gameID, $db);
		
	}
	
	/*
	#########################
	#						#
	#		SCORE TABLE		#
	#						#
	#########################
	*/
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>Qrtr 1</td> <td> | </td> <td>Qrtr 2</td> <td> | </td> <td>Qrtr 3</td> <td> | </td> <td>Qrtr 4</td> <td> | </td> <td>OT</td> <td> | </td> <td> Total </td></tr>");
	printf("<tr>	<td>----</td> <td>-</td> <td>------</td> <td>-</td> <td>------</td> <td>-</td>  <td>------</td> <td>-</td> <td>------</td> <td>-</td> <td>--</td> <td>-</td> <td>------</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr>",$homeTeam, $scoreArray[0], $scoreArray[1], $scoreArray[2], $scoreArray[3], $scoreArray[4], $hTotal);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr></table><br><br>", $awayTeam, $scoreArray[5], $scoreArray[6], $scoreArray[7], $scoreArray[8], $scoreArray[9], $aTotal);
	
	
	
	
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.quarter AS qrtr, pbp.time AS tme FROM football_pbp AS pbp JOIN $schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$text = $row->text;
		$qrtr = $row->qrtr;
		$time = $row->tme;
		$pbpText = $qrtr . " " . $time . " | " . $text;
		if(str_contains($text, "Touchdown") or str_contains($text, "Field goal GOOD") or str_contains($text, "Safety") or str_contains($text, "Extra point GOOD") or str_contains($text, "2-point conversion GOOD")){
			printf("<b>%s</b><br>", $pbpText);
		}else{
			printf("%s<br>", $pbpText);
		}
		array_push($pbpEntries, [$row->pbpID, $pbpText]);
	}
	
	
	if($comp == 1){
		printf("<br>-END OF GAME-");
	}
	
	//GAME MANAGER
	printf("<hr><h3>Game Manager</h3>");
	
if($comp == 0){
?>
<form action = "<?php echo $phpURL?>" method="POST">
<input type="hidden" id="complete" name = "complete" value="1">
<input type ="submit" Value="Complete">
</form>

<?php
}
?>
<br>
<p>
<?php

	$sql = "SELECT r.name AS player, r.number AS number FROM roster_player AS r JOIN roster_teams AS t ON r.team_id=t.id WHERE r.season = '$season' AND t.urlName='$sport'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$player = $row->player;
		$number = $row->number;
		array_push($roster, $player);
		printf("%s | %s<br>", $row->number, $row->player);
	}
?>
</p>

<br>
<h3>-Automatic-</h3>

<h4>Play-By-Play</h4>
<form action = "<?php echo $phpURL?>" method="POST">


<!-- QUARTER SELECT -->
<b>Q </b>
<select name = "quarter">
<?php for($i = 1; $i < 6; $i++){
if($i == $quarter){
	echo("<option value = '$i' selected>$i</option>");
}else{
	echo("<option value = '$i'>$i</option>");
}
}?>
</select>

<!-- TIME SELECT -->
<select name = "minutes">
<?php for($i = 0; $i < $maximumMinutes+1; $i++){
if($i == $minutes){
	echo("<option value = '$i' selected>$i</option>");
}else{
	echo("<option value = '$i'>$i</option>");
}
}?>
</select>:<select name = "seconds">
<?php for($i = 0; $i < 60; $i++){
if($i == $seconds){
	echo("<option value = '$i' selected>$i</option>");
}else{
	echo("<option value = '$i'>$i</option>");
}
}?>
</select>



<select name = "action">
<option value="Pass by ">Pass by </option>
<option value="Run by ">Run by </option>
<option value="Incomplete pass by ">Incomplete pass by </option>
<option value="Reception by ">Reception by </option>
<option value="Kick by ">Kick by </option>
<option value="Touchdown by ">Touchdown by </option>
<option value="Extra point GOOD by ">Extra point GOOD by </option>
<option value="Extra point MISS by ">Extra point MISS by </option>
<option value="Field goal GOOD by ">Field goal GOOD by </option>
<option value="Field goal MISS by ">Field goal MISS by </option>
<option value="2-point conversion GOOD by ">2-point conversion GOOD by </option>
<option value="2-point conversion FAIL by ">2-point conversion FAIL by </option>
<option value="Safety by ">Safety by </option>
<option value="Penalty on ">Penalty on </option>
<option value="Holding on ">Holding on </option>
<option value="Offsides on ">Offsides on </option>
<option value="Pass interference on ">Offsides on </option>
<option value="False start on ">False start on </option>
<option value="Intentional grounding on ">Intentional grounding on </option>
<option value="Sack by ">Sack by </option>
<option value="Fumble by ">Fumble by </option>
<option value="Recovered by ">Recovered by </option>
<option value="Interception by ">Interception by </option>
<option value="Punt by ">Punt by </option>
<option value="Return by ">Return by </option>
<option value="Timeout by ">Timeout by </option>
</select>
<br><br>


<select name = "team">
	<?php if ($team == $homeTeam){
	?>
	<option value='<?php echo $homeTeam?>' selected><?php echo $homeTeam?></option>
	<option value='<?php echo $awayTeam?>'><?php echo $awayTeam?></option>
	<?php
	}else{
	?>
	<option value='<?php echo $homeTeam?>'><?php echo $homeTeam?></option>
	<option value='<?php echo $awayTeam?>' selected><?php echo $awayTeam?></option>
	<?php
	}
	?>
</select>

<select name = "player">
<?php foreach($roster as $opt){
	if($opt == $_POST['player']){
		echo("<option value = '$opt' selected>$opt</option>");
	}else{
		echo("<option value = '$opt'>$opt</option>");
	}
}
?>
</select>
for 
<select name = "yards">
<?php  
for($i = -100; $i < 100; $i++){
	if($i == 0){
		echo("<option value = '$i' selected>$i</option>");
	}else{
		echo("<option value = '$i'>$i</option>");
	}
}
?>
</select> yards

<input type ="submit" Value="Submit">
</form>

<br>

<h4>Remove entries</h4>

<form action = "<?php echo $phpURL?>" method="POST">
<select name = "pbpRemove">
<?php foreach($pbpEntries as list($rmvID, $rmvText)){
echo("<option value = '$rmvID'>$rmvText</option>");
}
?>
</select>

<input type ="submit" Value="Remove">
</form>

<br>
<h3>-Manual-</h3>
<br>

<form action = "<?php echo $phpURL?>" method="POST">
<table>
<tr><td>Team</td> <td> | </td> <td>Qrtr 1</td> <td> | </td> <td>Qrtr 2</td> <td> | </td> <td>Qrtr 3</td> <td> | </td> <td>Qrtr 4</td> <td> | </td> <td>OT</td> <td> | </td> <td> Total </td></tr>
<tr><td>----</td> <td>-</td> <td>-----</td> <td>-</td> <td>-----</td> <td>-</td>  <td>-----</td> <td>-</td> <td>-----</td> <td>-</td> <td>----</td> <td>-</td> <td>-----</td></tr>
<tr><td><?php echo $homeTeam?></td> <td> | </td> <td><input type="number" id="hq1score" name = "hq1score" min = "0" max = "99" value ='<?php echo $scoreArray[0]?>'><br></td> <td> | </td> <td><input type="number" id="hq2score" name = "hq2score" min = "0" max = "99" value ='<?php echo $scoreArray[1]?>'></td> <td> | </td> <td><input type="number" id="hq3score" name = "hq3score" min = "0" max = "99" value ='<?php echo $scoreArray[2]?>'><br></td> <td> | </td> <td><input type="number" id="hq4score" name = "hq4score" min = "0" max = "99" value ='<?php echo $scoreArray[3]?>'></td> <td> | </td><td><input type="number" id="hOTscore" name = "hOTscore" min = "0" max = "99" value ='<?php echo $scoreArray[4]?>'></td> <td> | </td> <td><b><?php echo $hTotal?></b></td></tr>
<tr><td><?php echo $awayTeam?></td> <td> | </td> <td><input type="number" id="aq1score" name = "aq1score" min = "0" max = "99" value ='<?php echo $scoreArray[5]?>'></td> <td> | </td> <td><input type="number" id="aq2score" name = "aq2score" min = "0" max = "99" value ='<?php echo $scoreArray[6]?>'></td> <td> | </td> <td><input type="number" id="aq3score" name = "aq3score" min = "0" max = "99" value ='<?php echo $scoreArray[7]?>'></td> <td> | </td> <td><input type="number" id="aq4score" name = "aq4score" min = "0" max = "99" value ='<?php echo $scoreArray[8]?>'></td> <td> | </td><td><input type="number" id="aOTscore" name = "aOTscore" min = "0" max = "99" value ='<?php echo $scoreArray[9]?>'></td> <td> | </td> <td><b><?php echo $aTotal?></b></td></tr>
</table>
<input type ="submit" Value="Submit">
</form>

</body>