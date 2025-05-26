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
$phpURL = "vballmanager.php?gameID=".$gameID."&district=".$isDistrict;
$schedule = 'schedule';
$vball = 'volleyball';

function updateScore($hs1, $hs2, $hs3, $hs4, $hs5, $as1, $as2, $as3, $as4, $as5, $gameID, $db, $vball){
		$hTotal = 0;
		$aTotal = 0;
		if($hs1 >= 25 and $hs1 > $as1 + 1){
			$hTotal += 1;
		}else if($as1 >= 25 and $as1 > $hs1 + 1){
			$aTotal += 1;
		}
		if($hs2 >= 25 and $hs2 > $as2 + 1){
			$hTotal += 1;
		}else if($as2 >= 25 and $as2 > $hs2 + 1){
			$aTotal += 1;
		}
		if($hs3 >= 25 and $hs3 > $as3 + 1){
			$hTotal += 1;
		}else if($as3 >= 25 and $as3 > $hs3 + 1){
			$aTotal += 1;
		}
		if($hs4 >= 25 and $hs4 > $as4 + 1){
			$hTotal += 1;
		}else if($as4 >= 25 and $as4 > $hs4 + 1){
			$aTotal += 1;
		}
		if($hs5 >= 25 and $hs5 > $as5 + 1){
			$hTotal += 1;
		}else if($as5 >= 25 and $as5 > $hs5 + 1){
			$aTotal += 1;
		}
		
		$sqls = "UPDATE $vball SET home_set1 = '$hs1', home_set2 = '$hs2', home_set3 = '$hs3', home_set4 = '$hs4', home_set5 = '$hs5', home_total = '$hTotal', away_set1 = '$as1', away_set2 = '$as2', away_set3 = '$as3', away_set4 = '$as4', away_set5 = '$as5', away_total = '$aTotal' WHERE schedule_id='$gameID'";
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

	$set = 1;
	$homeID = 0;
	$comp = 0;
	
	//live game vars
	$live_set = 1;
	$serve = "FCHS";
	$homeTO = 3;
	$awayTO = 3;
	
	
	
	if($isDistrict=="true"){
		$schedule = 'schedule_other';
		$vball = 'volleyball_other';
	}
	
    
	$sql = "SELECT t.urlName AS sport FROM $schedule AS s JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id = '$gameID'";
	
	try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	
	if($_POST && isset($_POST['hs1score'])){
		$hs1 = $_POST['hs1score'];
		$hs2 = $_POST['hs2score'];
		$hs3 = $_POST['hs3score'];
		$hs4 = $_POST['hs4score'];
		$hs5 = $_POST['hs5score'];
		
		$as1 = $_POST['as1score'];
		$as2 = $_POST['as2score'];
		$as3 = $_POST['as3score'];
		$as4 = $_POST['as4score'];
		$as5 = $_POST['as5score'];

		
		updateScore($hs1, $hs2, $hs3, $hs4, $hs5, $as1, $as2, $as3, $as4, $as5, $gameID, $db, $vball);
	}
	
	if($_POST && isset($_POST['pbpRemove'])){
		$remove = $_POST['pbpRemove'];
		$sqls = "DELETE FROM volleyball_pbp WHERE id='$remove'";
		$query = $db->prepare($sqls);
		$query->execute();
	}
	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sport = $row->sport;
	}
	
	//Create new game if doesn't exist
	$sql = "SELECT 1 FROM $vball AS vb JOIN $schedule AS s ON vb.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	if($query->rowCount() == 0){
		$sql = "INSERT INTO $vball (home_total, away_total, home_set1, home_set2, home_set3, home_set4, home_set5, away_set1, away_set2, away_set3, away_set4, away_set5, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,(SELECT id FROM $schedule WHERE id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
	}
	
	$sqlsport = "SELECT s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id AS aNum, s.team_id AS team, t.formattedName, 
		vb.home_set1 AS hs1, vb.home_set2 AS hs2, vb.home_set3 AS hs3, vb.home_set4 AS hs4, vb.home_set5 AS hs5, vb.home_total AS ht,
		vb.away_set1 AS as1, vb.away_set2 AS as2, vb.away_set3 AS as3, vb.away_set4 AS as4, vb.away_set5 AS as5, vb.away_total AS at, vb.completed AS cmp
		FROM $vball AS vb JOIN $schedule AS s ON vb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	
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
			
		$hs1score = $row->hs1;
		$hs2score = $row->hs2;
		$hs3score = $row->hs3;
		$hs4score = $row->hs4;
		$hs5score = $row->hs5;
			
		$as1score = $row->as1;
		$as2score = $row->as2;
		$as3score = $row->as3;
		$as4score = $row->as4;
		$as5score = $row->as5;

		$hTotal = $row->ht;
		$aTotal = $row->at;
		
		$comp = $row->cmp;
	}
	
	//Create live game
	$sql = "SELECT * FROM live_games AS game JOIN $schedule AS s ON game.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();

	if($query->rowCount() == 0){
		//period = set, info1 = serve, info2 = tohome, info3 = toaway, info? = players on court?
		$sql = "INSERT INTO live_games (period, info_1, info_2, info_3, schedule_id) VALUES ('$live_set', '$serve', '$homeTO', '$awayTO', (SELECT id FROM $schedule WHERE id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
	}else{
		while($row = $query->fetchObject()){
			$live_set = $row->period;
			$serve = $row->info_1;
			$homeTO = $row->info_2;
			$awayTO = $row->info_3;
		}
	}
	
	if($_POST && isset($_POST['action'])){
		$action = $_POST['action'];
		$team = $_POST['team'];
		$set = $_POST['set'];
		$setText = $set;
		
		$actionText = "";

		if($_POST['team'] == "FCHS"){
			$actionText = $_POST['player'] . " (" . $team . ") " . $action;
		}else{
			$actionText = $team . $action;
		}
		
		//POST PBP SQL

		$sql = "INSERT INTO volleyball_pbp (text, set_, game_id) VALUES ('$actionText', '$setText', (SELECT id FROM $schedule where id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
			
		if($action == "Kill by "){
			if(($homeID == 1 && $team == "FCHS") or ($homeID != 1 && $team != "FCHS")){
				switch($set){
					case 1:
						$hs1score += 1;
						break;
					case 2:
						$hs2score += 1;
						break;
					case 3:
						$hs3score += 1;
						break;
					case 4:
						$hs4score += 1;
						break;
					case 5:
						$hs5score += 1;
						break;

				}
			}else{
				switch($set){
					case 1:
						$as1score += 1;
						break;
					case 2:
						$as2score += 1;
						break;
					case 3:
						$as3score += 1;
						break;
					case 4:
						$as4score += 1;
						break;
					case 5:
						$as5score += 1;
						break;
				}
			}
		}

		updateScore($hs1score, $hs2score, $hs3score, $hs4score, $hs5score, $as1score, $as2score, $as3score, $as4score, $as5score, $gameID, $db, $vball);
		
		$set = $_POST['set'];
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
		
		$sqls = "UPDATE $vball SET completed = '$comp' WHERE schedule_id='$gameID'";
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
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>1</td> <td> | </td> <td>2</td> <td> | </td> <td>3</td> <td> | </td> <td>4</td> <td> | </td> <td>5</td> <td> || </td> <td> S </td></tr>");
	printf("<tr>	<td>----</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td></tr>",$homeTeam, $hs1score, $hs2score, $hs3score, $hs4score, $hs5score, $hTotal);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td></tr></table><br><br>", $awayTeam, $as1score, $as2score, $as3score, $as4score, $as5score, $aTotal);
	
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.set_ AS set_ FROM volleyball_pbp AS pbp JOIN $schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$text = $row->text;
		$inn = $row->set_;
		$pbpText = $inn . " | " . $text;
		if(str_contains($text, "Kill")){
			printf("<b>%s</b><br>", $pbpText);
		}else{
			printf("%s<br>", $pbpText);
		}
		array_push($pbpEntries, [$row->pbpID, $pbpText]);
	}
	
	
	if($comp == 1){
		printf("<br> -END OF GAME-");
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


<!-- set SELECT -->
<b>set: </b><select name = "set">
<?php for($i = 1; $i < 6; $i++){
if($i == $set){
	echo("<option value = '$i' selected>$i</option>");
}else{
	echo("<option value = '$i'>$i</option>");
}
}?>
</select>

<!-- TEAM SELECT -->
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

<!-- PLAYER SELECT -->
<select name = "player" value = '<?php echo $_POST['player']?>'>
<?php foreach($roster as $opt){
	if($opt == $_POST['player']){
		echo("<option value = '$opt' selected>$opt</option>");
	}else{
		echo("<option value = '$opt'>$opt</option>");
	}
}
?>
<option value = ''> </option> 
</select>

<br><br>

<select name = "action">
<option value="Kill by ">Kill by </option>
<option value="Attack error by ">Attack error by </option>
<option value="Service ace by ">Service ace by </option>
<option value="Service error by ">Service error by </option>
<option value="Block by ">Block by </option>
<option value="Sub: ">Sub: </option>
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
<tr>	<td>Team</td> <td> | </td> 	<td>1</td> 	<td> | </td> 	<td>2</td> <td> | </td> <td>3</td> 	<td> | </td> 	<td>4</td> 	<td> | </td> 	<td>5</td> <td> || </td> <td> S </td></tr>
<tr>	<td>----</td> <td>-</td> 	<td>----</td> <td>-</td> 		<td>----</td> <td>-</td> 	<td>----</td> <td>-</td> <td>----</td> <td>-</td> <td>----</td> <td>-</td> <td>-</td></tr>
<tr><td><?php echo $homeTeam?></td> <td> | </td> <td><input type="number" id="hs1score" name = "hs1score" min = "0" max = "99" value ='<?php echo $hs1score?>'><br></td> <td> | </td> <td><input type="number" id="hs2score" name = "hs2score" min = "0" max = "99" value ='<?php echo $hs2score?>'></td> <td> | </td> <td><input type="number" id="hs3score" name = "hs3score" min = "0" max = "99" value ='<?php echo $hs3score?>'></td> <td> | </td> <td><input type="number" id="hs4score" name = "hs4score" min = "0" max = "99" value ='<?php echo $hs4score?>'></td> <td> | </td> <td><input type="number" id="hs5score" name = "hs5score" min = "0" max = "99" value ='<?php echo $hs5score?>'></td> <td> || </td> <td><b><?php echo $hTotal?></b></td></tr>
<tr><td><?php echo $awayTeam?></td> <td> | </td> <td><input type="number" id="as1score" name = "as1score" min = "0" max = "99" value ='<?php echo $as1score?>'><br></td> <td> | </td> <td><input type="number" id="as2score" name = "as2score" min = "0" max = "99" value ='<?php echo $as2score?>'></td> <td> | </td> <td><input type="number" id="as3score" name = "as3score" min = "0" max = "99" value ='<?php echo $as3score?>'></td> <td> | </td> <td><input type="number" id="as4score" name = "as4score" min = "0" max = "99" value ='<?php echo $as4score?>'></td> <td> | </td> <td><input type="number" id="as5score" name = "as5score" min = "0" max = "99" value ='<?php echo $as5score?>'></td> <td> || </td> <td><b><?php echo $aTotal?></b></td></tr>
</table>
<input type ="submit" Value="Submit">
</form>


</body>