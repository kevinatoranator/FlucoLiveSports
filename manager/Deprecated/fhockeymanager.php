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
$phpURL = "fhockeymanager.php?gameID=".$gameID."&district=".$isDistrict;
$schedule = 'schedule';
$fhockey = 'field_hockey';

function updateScore($hq1, $hq2, $hq3, $hq4, $hOT, $aq1, $aq2, $aq3, $aq4, $aOT, $gameID, $db, $fhockey){
		$hTotal = $hq1 + $hq2 + + $hq3 + $hq4 + $hOT;
		$aTotal = $aq1 + $aq2 + $aq3 + $aq4 + $aOT;
		
		$sqls = "UPDATE $fhockey SET home_q1 = '$hq1', home_q2 = '$hq2', home_q3 = '$hq3', home_q4 = '$hq4', home_ot = '$hOT', home_total = '$hTotal', away_q1 = '$aq1', away_q2 = '$aq2', away_q3 = '$aq3', away_q4 = '$aq4', away_ot = '$aOT', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sqls);
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

	$sport = "";
	$home = "";
	$away = "";
	$roster = array();
	$pbpEntries = array();
	$minutes = 0;
	$seconds = 0;
	$maxMin = 99;
	$quarter = 1;
	$homeID = 0;
	$comp = 0;
	
	//live game vars
	$game_time = "12:00";
	$goalie = "";
	
	if($isDistrict=="true"){
		$schedule = 'schedule_other';
		$fhockey = 'field_hockey_other';
	}
    
	$sql = "SELECT t.urlName AS sport FROM $schedule AS s JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id = '$gameID'";
	
	try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	
	if($_POST && isset($_POST['hq1score'])){
		$hq1 = $_POST['hq1score'];
		$hq2 = $_POST['hq2score'];
		$hq3 = $_POST['hq3score'];
		$hq4 = $_POST['hq4score'];
		$hOT = $_POST['hOTscore'];
		
		$aq1 = $_POST['aq1score'];
		$aq2 = $_POST['aq2score'];
		$aq3 = $_POST['aq3score'];
		$aq4 = $_POST['aq4score'];
		$aOT = $_POST['aOTscore'];
		
		updateScore($hq1, $hq2, $hq3, $hq4, $hOT, $aq1, $aq2, $aq3, $aq4, $aOT, $gameID, $db, $fhockey);
	}
	
	if($_POST && isset($_POST['pbpRemove'])){
		$remove = $_POST['pbpRemove'];
		$sqls = "DELETE FROM field_hockey_pbp WHERE id='$remove'";
		$query = $db->prepare($sqls);
		$query->execute();
	}
	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sport = $row->sport;
	}
	
	//Create new game if doesn't exist
	$sql = "SELECT 1 FROM $fhockey AS fh JOIN $schedule AS s ON fh.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	if($query->rowCount() == 0){
		$sql = "INSERT INTO $fhockey (home_q1, home_q2, home_q3, home_q4, home_ot, away_q1, away_q2, away_q3, away_q4, away_ot, home_total, away_total, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM $schedule WHERE id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
	}	
	if($sport=="fhockey"){
		$minutes = 15;
		$seconds = 0;
		$maxMin = 15;
	}else if($sport=="jvfhockey"){
		$minutes = 12;
		$seconds = 0;
		$maxMin = 12;
	}
	
	$sqlsport = "SELECT s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id as aNum, s.team_id AS team, t.formattedName, 
		fh.home_q1 AS hq1, fh.home_q2 AS hq2, fh.home_q3 AS hq3, fh.home_q4 AS hq4, fh.home_ot AS hot, fh.home_total AS ht, 
		fh.away_q1 AS aq1, fh.away_q2 AS aq2, fh.away_q3 AS aq3, fh.away_q4 AS aq4, fh.away_ot AS aot, fh.away_total AS at, fh.completed AS cmp
		FROM $fhockey AS fh JOIN $schedule AS s ON fh.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
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
			
		$hq1Score = $row->hq1;
		$hq2Score = $row->hq2;
		$hq3Score = $row->hq3;
		$hq4Score = $row->hq4;
		$hOTScore = $row->hot;
			
		$aq1Score = $row->aq1;
		$aq2Score = $row->aq2;
		$aq3Score = $row->aq3;
		$aq4Score = $row->aq4;
		$aOTScore = $row->aot;
		
		$hTotal = $row->ht;
		$aTotal = $row->at;
		
		$comp = $row->cmp;
	}
	
	//Create live game
	$sql = "SELECT * FROM live_games AS game JOIN $schedule AS s ON game.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();

	if($query->rowCount() == 0){
		//period = quarter, game_time = time, info1 = goalie
		$sql = "INSERT INTO live_games (period, game_time, info_1, schedule_id) VALUES ('$quarter', '$game_time', '$goalie', (SELECT id FROM $schedule WHERE id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
	}else{
		while($row = $query->fetchObject()){
			$quarter = $row->period;
			$game_time = $row->game_time;
			$goalie = $row->info_1;

		}
	}
	
	if($_POST && isset($_POST['action'])){
		$quarterText = $_POST['quarter'];
		$action = $_POST['action'];
		$team = $_POST['team'];
		$quarter = $_POST['quarter'];
		$minutes = $_POST['minutes'];
		$seconds = $_POST['seconds'];
		
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
		//POST PBP SQL
		$sql = "INSERT INTO field_hockey_pbp (text, quarter, time, game_id) VALUES ('$actionText', '$quarterText', '$displayTime', (SELECT id FROM $schedule where id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
		
		if($action == "Goal scored by "){
			if(($homeID == 1 && $team == "FCHS") or ($homeID != 1 && $team != "FCHS")){
				switch($quarter){
					case 1:
						$hq1Score += 1;
						break;
					case 2:
						$hq2Score += 1;
						break;
					case 3:
						$hq3Score += 1;
						break;
					case 4:
						$hq4Score += 1;
						break;
					case 5:
						$hOTScore += 1;
						break;
				}
			}else{
				switch($quarter){
					case 1:
						$aq1Score += 1;
						break;
					case 2:
						$aq2Score += 1;
						break;
					case 3:
						$aq3Score += 1;
						break;
					case 4:
						$aq4Score += 1;
						break;
					case 5:
						$aOTScore += 1;
						break;
				}
			}
			updateScore($hq1Score, $hq2Score, $hq3Score, $hq4Score, $hOTScore, $aq1Score, $aq2Score, $aq3Score, $aq4Score, $aOTScore, $gameID, $db, $fhockey);
		}
		$minutes = $_POST['minutes'];
		$seconds = $_POST['seconds'];
		$quarter = $_POST['quarter'];
		//echo $pbp;
	}
	
	if($_POST && isset($_POST['complete'])){
		$comp = $_POST['complete'];
		$homeWins = 0;
		$homeLosses = 0;
		$homeTies = 0;
		$awayWins = 0;
		$awayLosses = 0;
		$awayTies = 0;
		
		$sqls = "UPDATE $fhockey SET completed = '$comp' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
		#select home team standing info
		$sqls = "SELECT wins AS w, losses AS l, ties AS t, school_id AS school, sport_id AS sport FROM standings AS st JOIN roster_teams AS t on st.sport_id=t.id WHERE st.school_id='$homeID' AND st.sport_id = '$sportID' AND st.season='2023'";
		$query = $db->prepare($sqls);
		$query->execute();
		while($row = $query->fetchObject()){
			$homeWins = $row->w;
			$homeLosses = $row->l;
			$homeTies = $row->t;
		}
		#select away team standing info
		$sqls = "SELECT wins AS w, losses AS l, ties AS t, school_id AS school, sport_id AS sport FROM standings AS st JOIN roster_teams AS t on st.sport_id=t.id WHERE st.school_id='$awayID' AND st.sport_id = '$sportID' AND st.season='2023'";
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
		$sqls = "UPDATE standings SET wins = '$homeWins', losses = '$homeLosses', ties = '$homeTies' WHERE school_id='$homeID' AND sport_id = '$sportID' AND season='2023' ";
		$query = $db->prepare($sqls);
		$query->execute();
		$sqls = "UPDATE standings SET wins = '$awayWins', losses = '$awayLosses', ties = '$awayTies' WHERE school_id='$awayID' AND sport_id = '$sportID' AND season='2023' ";
		$query = $db->prepare($sqls);
		$query->execute();
		
		#remove from livegame
		$sql = "DELETE FROM live_games WHERE schedule_id='$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
		
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
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr>",$homeTeam, $hq1Score, $hq2Score, $hq3Score, $hq4Score, $hOTScore, $hTotal);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr></table><br><br>", $awayTeam, $aq1Score, $aq2Score, $aq3Score, $aq4Score, $aOTScore, $aTotal);
	
	
	
	
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.quarter AS qrtr, pbp.time AS tme FROM field_hockey_pbp AS pbp JOIN $schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$text = $row->text;
		$qrtr = $row->qrtr;
		$time = $row->tme;
		$pbpText = $qrtr . " " . $time . " | " . $text;
		if(str_contains($text, "Goal")){
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

	$sql = "SELECT r.name AS player, r.number AS number FROM roster_player AS r JOIN roster_teams AS t ON r.team_id=t.id WHERE r.season = '2023' AND t.urlName='$sport'";
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
<?php for($i = 0; $i < $maxMin+1; $i++){
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
<option value="Goal scored by ">Goal scored by </option>
<option value="Assist by ">Assist by </option>
<option value="Penalty corner by ">Penalty corner by </option>
<option value="Save by ">Save by </option>
<option value="Shot by ">Shot by </option>
<option value="Penalty on ">Penalty on </option>
<option value="Green card on ">Green card on </option>
<option value="Yellow card on ">Yellow card on </option>
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
<tr><td><?php echo $homeTeam?></td> <td> | </td> <td><input type="number" id="hq1score" name = "hq1score" min = "0" max = "99" value ='<?php echo $hq1Score?>'><br></td> <td> | </td> <td><input type="number" id="hq2score" name = "hq2score" min = "0" max = "99" value ='<?php echo $hq2Score?>'></td> <td> | </td> <td><input type="number" id="hq3score" name = "hq3score" min = "0" max = "99" value ='<?php echo $hq3Score?>'><br></td> <td> | </td> <td><input type="number" id="hq4score" name = "hq4score" min = "0" max = "99" value ='<?php echo $hq4Score?>'></td> <td> | </td><td><input type="number" id="hOTscore" name = "hOTscore" min = "0" max = "99" value ='<?php echo $hOTScore?>'></td> <td> | </td> <td><b><?php echo $hTotal?></b></td></tr>
<tr><td><?php echo $awayTeam?></td> <td> | </td> <td><input type="number" id="aq1score" name = "aq1score" min = "0" max = "99" value ='<?php echo $aq1Score?>'></td> <td> | </td> <td><input type="number" id="aq2score" name = "aq2score" min = "0" max = "99" value ='<?php echo $aq2Score?>'></td> <td> | </td> <td><input type="number" id="aq3score" name = "aq3score" min = "0" max = "99" value ='<?php echo $aq3Score?>'></td> <td> | </td> <td><input type="number" id="aq4score" name = "aq4score" min = "0" max = "99" value ='<?php echo $aq4Score?>'></td> <td> | </td><td><input type="number" id="aOTscore" name = "aOTscore" min = "0" max = "99" value ='<?php echo $aOTScore?>'></td> <td> | </td> <td><b><?php echo $aTotal?></b></td></tr>
</table>
<input type ="submit" Value="Submit">
</form>

</body>