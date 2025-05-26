<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<?php 
$date = date("Y-m-d", strtotime("today" ));
$fdate = date("l, F d", strtotime("today")); 

$gameID = $_GET['gameID'];
$isDistrict = $_GET['district'];
$phpURL = "bballmanager.php?gameID=".$gameID."&district=".$isDistrict;
$schedule = 'schedule';
$basketball = 'basketball';

function updateScore($hq1, $hq2, $hq3, $hq4, $hOT, $aq1, $aq2, $aq3, $aq4, $aOT, $gameID, $db, $basketball){
		$hTotal = $hq1 + $hq2 + + $hq3 + $hq4 + $hOT;
		$aTotal = $aq1 + $aq2 + $aq3 + $aq4 + $aOT;
		
		$sqls = "UPDATE $basketball SET home_q1 = '$hq1', home_q2 = '$hq2', home_q3 = '$hq3', home_q4 = '$hq4', home_ot = '$hOT', home_total = '$hTotal', away_q1 = '$aq1', away_q2 = '$aq2', away_q3 = '$aq3', away_q4 = '$aq4', away_ot = '$aOT', away_total = '$aTotal' WHERE schedule_id='$gameID'";
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
    
	if($isDistrict == "true"){
		$schedule = 'schedule_other';
		$basketball = 'basketball_other';
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
		
		updateScore($hq1, $hq2, $hq3, $hq4, $hOT, $aq1, $aq2, $aq3, $aq4, $aOT, $gameID, $db, $basketball);
	}
	
	if($_POST && isset($_POST['pbpRemove'])){
		$remove = $_POST['pbpRemove'];
		$sqls = "DELETE FROM basketball_pbp WHERE id='$remove'";
		$query = $db->prepare($sqls);
		$query->execute();
	}
	
	if($_POST && isset($_POST['complete'])){
		$completed = $_POST['complete'];
		$sqls = "UPDATE $basketball SET completed = '$completed' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
	}
	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sport = $row->sport;
	}
	
	//Create new game if doesn't exist
	$sql = "SELECT 1 FROM $basketball AS bb JOIN $schedule AS s ON bb.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	if($query->rowCount() == 0){
		$sql = "INSERT INTO $basketball (home_q1, home_q2, home_q3, home_q4, home_ot, away_q1, away_q2, away_q3, away_q4, away_ot, home_total, away_total, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM $schedule WHERE id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
	}	
	if($sport=="bbball" or $sport=="gbball"){
		$minutes = 12;
		$seconds = 0;
		$maxMin = 12;
	}else if($sport=="jvbbball" or $sport=="jvgbball"){
		$minutes = 8;
		$seconds = 0;
		$maxMin = 8;
	}
	
	$sqlsport = "SELECT s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id, s.team_id, t.formattedName, 
		bb.home_q1 AS hq1, bb.home_q2 AS hq2, bb.home_q3 AS hq3, bb.home_q4 AS hq4, bb.home_ot AS hot, bb.home_total AS ht, 
		bb.away_q1 AS aq1, bb.away_q2 AS aq2, bb.away_q3 AS aq3, bb.away_q4 AS aq4, bb.away_ot AS aot, bb.away_total AS at, bb.completed AS cmp
		FROM $basketball AS bb JOIN $schedule AS s ON bb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	$query = $db->prepare($sqlsport);
	$query->execute();
	while($row = $query->fetchObject()){
		printf("<h3>Game Page Preview</h3>");
		printf("<h5>%s</h5><br>", $row->formattedName);
		
		$homeID = $row->hNum;
			
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
		$sql = "INSERT INTO basketball_pbp (text, quarter, time, game_id) VALUES ('$actionText', '$quarterText', '$displayTime', (SELECT id FROM $schedule where id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
		
		if($action == "Jumper by " or $action == "Layup by " or $action == "Slam Dunk by "){
			if(($homeID == 1 && $team == "FCHS") or ($homeID != 1 && $team != "FCHS")){
				switch($quarter){
					case 1:
						$hq1Score += 2;
						break;
					case 2:
						$hq2Score += 2;
						break;
					case 3:
						$hq3Score += 2;
						break;
					case 4:
						$hq4Score += 2;
						break;
					case 5:
						$hOTScore += 2;
						break;
				}
			}else{
				switch($quarter){
					case 1:
						$aq1Score += 2;
						break;
					case 2:
						$aq2Score += 2;
						break;
					case 3:
						$aq3Score += 2;
						break;
					case 4:
						$aq4Score += 2;
						break;
					case 5:
						$aOTScore += 2;
						break;
				}
			}
			updateScore($hq1Score, $hq2Score, $hq3Score, $hq4Score, $hOTScore, $aq1Score, $aq2Score, $aq3Score, $aq4Score, $aOTScore, $gameID, $db);
		}else if($action == "Free throw by "){
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
			updateScore($hq1Score, $hq2Score, $hq3Score, $hq4Score, $hOTScore, $aq1Score, $aq2Score, $aq3Score, $aq4Score, $aOTScore, $gameID, $db);
		}else if($action == "3 Pointer by "){
			if(($homeID == 1 && $team == "FCHS") or ($homeID != 1 && $team != "FCHS")){
				switch($quarter){
					case 1:
						$hq1Score += 3;
						break;
					case 2:
						$hq2Score += 3;
						break;
					case 3:
						$hq3Score += 3;
						break;
					case 4:
						$hq4Score += 3;
						break;
					case 5:
						$hOTScore += 3;
						break;
				}
			}else{
				switch($quarter){
					case 1:
						$aq1Score += 3;
						break;
					case 2:
						$aq2Score += 3;
						break;
					case 3:
						$aq3Score += 3;
						break;
					case 4:
						$aq4Score += 3;
						break;
					case 5:
						$aOTScore += 3;
						break;
				}
			}
			updateScore($hq1Score, $hq2Score, $hq3Score, $hq4Score, $hOTScore, $aq1Score, $aq2Score, $aq3Score, $aq4Score, $aOTScore, $gameID, $db);
		}
		$minutes = $_POST['minutes'];
		$seconds = $_POST['seconds'];
		$quarter = $_POST['quarter'];
		//echo $pbp;
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
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.quarter AS qrtr, pbp.time AS tme FROM basketball_pbp AS pbp JOIN $schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$text = $row->text;
		$qrtr = $row->qrtr;
		$time = $row->tme;
		$pbpText = $qrtr . " " . $time . " | " . $text;
		if(str_contains($text, "3 Pointer") or str_contains($text, "Free throw") or str_contains($text, "Jumper") or str_contains($text, "Layup") or str_contains($text, "dunk")){
			printf("<b>%s</b><br>", $pbpText);
		}else{
			printf("%s<br>", $pbpText);
		}
		array_push($pbpEntries, [$row->pbpID, $pbpText]);
	}
	
	
	if($comp == 1){
		printf("GAME COMPLETED");
	}
	
	//GAME MANAGER
	printf("<hr><h3>Game Manager</h3>");
?>
<form action = "<?php echo $phpURL?>" method="POST">
<input type="hidden" id="complete" name = "complete" value="1">
<input type ="submit" Value="Complete">
</form>
<br>
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
<br><br>

<form action = "<?php echo $phpURL?>" method="POST">
<table>
<tr><td>Team</td> <td> | </td> <td>Qrtr 1</td> <td> | </td> <td>Qrtr 2</td> <td> | </td> <td>Qrtr 3</td> <td> | </td> <td>Qrtr 4</td> <td> | </td> <td>OT</td> <td> | </td> <td> Total </td></tr>
<tr><td>----</td> <td>-</td> <td>-----</td> <td>-</td> <td>-----</td> <td>-</td>  <td>-----</td> <td>-</td> <td>-----</td> <td>-</td> <td>----</td> <td>-</td> <td>-----</td></tr>
<tr><td><?php echo $homeTeam?></td> <td> | </td> <td><input type="number" id="hq1score" name = "hq1score" min = "0" max = "99" value ='<?php echo $hq1Score?>'><br></td> <td> | </td> <td><input type="number" id="hq2score" name = "hq2score" min = "0" max = "99" value ='<?php echo $hq2Score?>'></td> <td> | </td> <td><input type="number" id="hq3score" name = "hq3score" min = "0" max = "99" value ='<?php echo $hq3Score?>'><br></td> <td> | </td> <td><input type="number" id="hq4score" name = "hq4score" min = "0" max = "99" value ='<?php echo $hq4Score?>'></td> <td> | </td><td><input type="number" id="hOTscore" name = "hOTscore" min = "0" max = "99" value ='<?php echo $hOTScore?>'></td> <td> | </td> <td><b><?php echo $hTotal?></b></td></tr>
<tr><td><?php echo $awayTeam?></td> <td> | </td> <td><input type="number" id="aq1score" name = "aq1score" min = "0" max = "99" value ='<?php echo $aq1Score?>'></td> <td> | </td> <td><input type="number" id="aq2score" name = "aq2score" min = "0" max = "99" value ='<?php echo $aq2Score?>'></td> <td> | </td> <td><input type="number" id="aq3score" name = "aq3score" min = "0" max = "99" value ='<?php echo $aq3Score?>'></td> <td> | </td> <td><input type="number" id="aq4score" name = "aq4score" min = "0" max = "99" value ='<?php echo $aq4Score?>'></td> <td> | </td><td><input type="number" id="aOTscore" name = "aOTscore" min = "0" max = "99" value ='<?php echo $aOTScore?>'></td> <td> | </td> <td><b><?php echo $aTotal?></b></td></tr>
</table>
<input type ="submit" Value="Submit">
</form>
<br>
<h4>Play-By-Play</h4>
<form action = "<?php echo $phpURL?>" method="POST">

<b>Q </b><input type="number" id="quarter" name = "quarter" min = "0" max = '5' value ='<?php echo $quarter?>'>

<input type="number" id="minutes" name = "minutes" min = "0" max = '<?php echo $maxMin?>' value ='<?php echo $minutes?>'>:<input type="number" id="seconds" name = "seconds" min = "0" max = "59" value ='<?php echo $seconds?>'>

<select name = "action">
<option value="Jumper by ">Jumper by </option>
<option value="Layup by ">Layup by </option>
<option value="Slam Dunk by ">Slam Dunk by </option>
<option value="3 Pointer by ">3 Pointer by </option>
<option value="Free throw by ">Free throw by </option>
<option value="Foul on ">Foul on </option>
<option value="Turnover by ">Turnover by </option>
<option value="Steal by ">Steal by </option>
<option value="Defensive rebound by ">Defensive rebound by </option>
<option value="Offensive rebound by ">Offensive rebound by </option>
</select>
<br><br>
<select name = "team">
<option value='<?php echo $homeTeam?>'><?php echo $homeTeam?></option>
<option value='<?php echo $awayTeam?>'><?php echo $awayTeam?></option>
</select>

<select name = "player">
<?php foreach($roster as $opt){
echo("<option value = '$opt'>$opt</option>");
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



</body>