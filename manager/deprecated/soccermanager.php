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
$schedule = 'schedule';
$soccer = 'soccer';
$phpURL = "soccermanager.php?gameID=".$gameID."&district=".$isDistrict;


function updateScore($hh1, $hh2, $hOT, $ah1, $ah2, $aOT, $gameID, $db, $soccer){
		$hTotal = $hh1 + $hh2 + $hOT;
		$aTotal = $ah1 + $ah2 + $aOT;
		
		$sqls = "UPDATE $soccer SET home_half1 = '$hh1', home_half2 = '$hh2', home_OT = '$hOT', home_total = '$hTotal', away_half1 = '$ah1', away_half2 = '$ah2', away_OT = '$aOT', away_total = '$aTotal' WHERE schedule_id='$gameID'";
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
	$half = 1;
	$homeID = 0;
	$comp = 0;
	if($isDistrict == "true"){
		$schedule = 'schedule_other';
		$soccer = 'soccer_other';
	}
    
	$sql = "SELECT t.urlName AS sport FROM $schedule AS s JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id = '$gameID'";
	
	try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	
	if($_POST && isset($_POST['hh1score'])){
		$hh1 = $_POST['hh1score'];
		$hh2 = $_POST['hh2score'];
		$hOT = $_POST['hOTscore'];
		
		$ah1 = $_POST['ah1score'];
		$ah2 = $_POST['ah2score'];
		$aOT = $_POST['aOTscore'];
		
		updateScore($hh1, $hh2, $hOT, $ah1, $ah2, $aOT, $gameID, $db, $soccer);
	}
	
	if($_POST && isset($_POST['pbpRemove'])){
		$remove = $_POST['pbpRemove'];
		$sqls = "DELETE FROM soccer_pbp WHERE id='$remove'";
		$query = $db->prepare($sqls);
		$query->execute();
	}
	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sport = $row->sport;
	}
	
	if($sport=="gsoccer" or  $sport=="bsoccer"){
		$minutes = 40;
		$seconds = 0;
		$maxMin = 40;
	}else if($sport=="jvgsoccer" or $sport=="jvbsoccer"){
		$minutes = 35;
		$seconds = 0;
		$maxMin = 35;
	}
	
	//Create new game if doesn't exist
	$sql = "SELECT 1 FROM $soccer AS sc JOIN $schedule AS s ON sc.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	if($query->rowCount() == 0){
		$sql = "INSERT INTO $soccer (home_half1, home_half2, home_OT, away_half1, away_half2, away_OT, home_total, away_total, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM $schedule WHERE id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
	}
	
	$sqlsport = "SELECT s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id AS aNum, s.team_id AS team, t.formattedName, 
		sc.home_half1 AS hh1, sc.home_half2 AS hh2, sc.home_OT AS hot, sc.home_total AS ht, sc.away_half1 AS ah1, sc.away_half2 AS ah2, sc.away_OT AS aot, sc.away_total AS at, sc.completed AS cmp
		FROM $soccer AS sc JOIN $schedule AS s ON sc.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
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
			
		$hh1Score = $row->hh1;
		$hh2Score = $row->hh2;
		$hOTScore = $row->hot;
			
		$ah1Score = $row->ah1;
		$ah2Score = $row->ah2;
		$aOTScore = $row->aot;
		
		$hTotal = $row->ht;
		$aTotal = $row->at;
		
		$comp = $row->cmp;
	}
	
	if($_POST && isset($_POST['action'])){
		$halfText = $_POST['half'];
		$action = $_POST['action'];
		$team = $_POST['team'];
		$half = $_POST['half'];
		$timePassed = $_POST['minutes'] * 60 + $_POST['seconds'];
		$displayMinutes = floor(($maxMin * 60 - $timePassed) / 60);
		$displaySeconds = ($maxMin * 60 - $timePassed) % 60;
		if($halfText == 2){
			$displayMinutes += $maxMin;
		}
		if(strlen($displaySeconds) < 2){
			$displaySeconds = "0" . $displaySeconds;
		}
		$displayTime = $displayMinutes . ":" . $displaySeconds;
		
		$actionText = "";
		if($half == 3){
			$halfText = "OT";
		}else{
			$halfText = "H" . $halfText;
		}
		if($_POST['team'] == "FCHS"){
			$pbp = $halfText . " " . $displayTime . " | " . $action . $_POST['player'] . " (" . $team . ")";
			$actionText = $action . $_POST['player'] . " (" . $team . ")";
		}else{
			$pbp = $halfText . " " . $displayTime . " | " . $action . $team;
			$actionText = $action . $team;
		}
		//POST PBP SQL
		$sql = "INSERT INTO soccer_pbp (text, half, time, game_id) VALUES ('$actionText', '$halfText', '$displayTime', (SELECT id FROM $schedule where id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
		
		if($action == "Goal scored by "){
			if(($homeID == 1 && $team == "FCHS") or ($homeID != 1 && $team != "FCHS")){
				if($half == 1){
					$hh1Score += 1;
				}else if($half == 2){
					$hh2Score += 1;
				}if($half == 3){
					$hOTScore += 1;
				}
			}else{
				if($half == 1){
					$ah1Score += 1;
				}else if($half == 2){
					$ah2Score += 1;
				}if($half == 3){
					$aOTScore += 1;
				}
			}
			updateScore($hh1Score, $hh2Score, $hOTScore, $ah1Score, $ah2Score, $aOTScore, $gameID, $db);
		}
		$minutes = $_POST['minutes'];
		$seconds = $_POST['seconds'];
		$half = $_POST['half'];
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
		
		$sqls = "UPDATE $soccer SET completed = '$comp' WHERE schedule_id='$gameID'";
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
	}
	
	/*
	#########################
	#						#
	#		SCORE TABLE		#
	#						#
	#########################
	*/
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>Half 1</td> <td> | </td> <td>Half 2</td> <td> | </td> <td>OT</td> <td> | </td> <td> Total </td></tr>");
	printf("<tr>	<td>----</td> <td>---</td> <td>------</td> <td>---</td> <td>------</td> <td>---</td> <td>--</td> <td>---</td> <td>------</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr>",$homeTeam, $hh1Score, $hh2Score, $hOTScore, $hTotal);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr></table><br><br>", $awayTeam, $ah1Score, $ah2Score, $aOTScore, $aTotal);
	
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.half AS half, pbp.time AS tme FROM soccer_pbp AS pbp JOIN $schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$text = $row->text;
		$halft = $row->half;
		$time = $row->tme;
		$pbpText = $halft . " " . $time . " | " . $text;
		if(str_contains($text, "Goal")){
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
<br>

<h3>-Automatic-</h3>
<h4>Play-By-Play</h4>
<form action = "<?php echo $phpURL?>" method="POST">

<!-- HALF SELECT -->
<b>H</b><select name = "half">
<?php for($i = 1; $i < 4; $i++){
if($i == $half){
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
<option value="Corner kick by ">Corner kick by </option>
<option value="Save by ">Save by </option>
<option value="Shot by ">Shot by </option>
<option value="Foul on ">Foul on </option>
<option value="Offside ">Offside </option>
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
<tr><td>Team</td> <td> | </td> <td>Half 1</td> <td> | </td> <td>Half 2</td> <td> | </td> <td>OT</td> <td> | </td> <td> Total </td></tr>
<tr><td>----</td><td>---</td><td>------</td><td>---</td><td>------</td><td>---</td><td>-----</td><td>---</td><td>------</td></tr>
<tr><td><?php echo $homeTeam?></td> <td> | </td> <td><input type="number" id="hh1score" name = "hh1score" min = "0" max = "99" value ='<?php echo $hh1Score?>'><br></td> <td> | </td> <td><input type="number" id="hh2score" name = "hh2score" min = "0" max = "99" value ='<?php echo $hh2Score?>'></td> <td> | </td> <td><input type="number" id="hOTscore" name = "hOTscore" min = "0" max = "99" value ='<?php echo $hOTScore?>'></td> <td> | </td> <td><b><?php echo $hTotal?></b></td></tr>
<tr><td><?php echo $awayTeam?></td> <td> | </td> <td><input type="number" id="ah1score" name = "ah1score" min = "0" max = "99" value ='<?php echo $ah1Score?>'></td> <td> | </td> <td><input type="number" id="ah2score" name = "ah2score" min = "0" max = "99" value ='<?php echo $ah2Score?>'></td> <td> | </td> <td><input type="number" id="aOTscore" name = "aOTscore" min = "0" max = "99" value ='<?php echo $aOTScore?>'></td> <td> | </td> <td><b><?php echo $aTotal?></b></td></tr>
</table>
<input type ="submit" Value="Submit">
</form>

</body>
