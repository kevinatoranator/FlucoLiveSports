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
$phpURL = "ballmanager.php?gameID=".$gameID;


function updateScore($hi1, $hi2, $hi3, $hi4, $hi5, $hi6, $hi7, $hex, $ai1, $ai2, $ai3, $ai4, $ai5, $ai6, $ai7, $aex, $hhits, $herrs, $ahits, $aerrs, $gameID, $db){
		$hTotal = $hi1 + $hi2 + $hi3 + $hi4 + $hi5 + $hi6 + $hi7 + $hex;
		$aTotal = $ai1 + $ai2 + $ai3 + $ai4 + $ai5 + $ai6 + $ai7 + $aex;
		
		$sqls = "UPDATE batball SET home_i1 = '$hi1', home_i2 = '$hi2', home_i3 = '$hi3', home_i4 = '$hi4', home_i5 = '$hi5', home_i6 = '$hi6', home_i7 = '$hi7', home_ex = '$hex', home_total = '$hTotal', away_i1 = '$ai1', away_i2 = '$ai2', away_i3 = '$ai3', away_i4 = '$ai4', away_i5 = '$ai5', away_i6 = '$ai6', away_i7 = '$ai7', away_ex = '$aex', away_total = '$aTotal',  home_hits = '$hhits', home_errors = '$herrs', away_hits = '$ahits', away_errors = '$aerrs' WHERE schedule_id='$gameID'";
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


	$sport = "";
	$home = "";
	$away = "";
	$roster = array();
	$pbpEntries = array();
	$maxMin = 7;
	$inning = 1;
	$homeID = 0;
	$comp = 0;
    
	$sql = "SELECT t.urlName AS sport FROM schedule AS s JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id = '$gameID'";
	
	try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	
	if($_POST && isset($_POST['hi1score'])){
		$hi1 = $_POST['hi1score'];
		$hi2 = $_POST['hi2score'];
		$hi3 = $_POST['hi3score'];
		$hi4 = $_POST['hi4score'];
		$hi5 = $_POST['hi5score'];
		$hi6 = $_POST['hi6score'];
		$hi7 = $_POST['hi7score'];
		$hex = $_POST['hexscore'];
		
		$hhits = $_POST['hhits'];
		$herrs = $_POST['herr'];
		
		$ai1 = $_POST['ai1score'];
		$ai2 = $_POST['ai2score'];
		$ai3 = $_POST['ai3score'];
		$ai4 = $_POST['ai4score'];
		$ai5 = $_POST['ai5score'];
		$ai6 = $_POST['ai6score'];
		$ai7 = $_POST['ai7score'];
		$aex = $_POST['aexscore'];
		
		$ahits = $_POST['ahits'];
		$aerrs = $_POST['aerr'];
		
		updateScore($hi1, $hi2, $hi3, $hi4, $hi5, $hi6, $hi7, $hex, $ai1, $ai2, $ai3, $ai4, $ai5, $ai6, $ai7, $aex, $hhits, $herrs, $ahits, $aerrs, $gameID, $db);
	}
	
	if($_POST && isset($_POST['pbpRemove'])){
		$remove = $_POST['pbpRemove'];
		$sqls = "DELETE FROM batball_pbp WHERE id='$remove'";
		$query = $db->prepare($sqls);
		$query->execute();
	}
	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sport = $row->sport;
	}
	
	//Create new game if doesn't exist
	$sql = "SELECT 1 FROM batball AS bat JOIN schedule AS s ON bat.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	if($query->rowCount() == 0){
		$sql = "INSERT INTO batball (home_total, away_total, home_i1, home_i2, home_i3, home_i4, home_i5, home_i6, home_i7, home_ex, away_i1, away_i2, away_i3, away_i4, away_i5, away_i6, away_i7, away_ex, home_hits, away_hits, home_errors, away_errors, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,(SELECT id FROM schedule WHERE id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
	}
	
	if($sport=="softball" or  $sport=="baseball" or $sport=="jvsoftball" or  $sport=="jvbaseball"){
		$sqlsport = "SELECT s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id AS aNum, s.team_id AS team, t.formattedName, 
		bb.home_i1 AS hi1, bb.home_i2 AS hi2, bb.home_i3 AS hi3, bb.home_i4 AS hi4, bb.home_i5 AS hi5, bb.home_i6 AS hi6, bb.home_i7 AS hi7, bb.home_ex AS hex, bb.home_total AS ht, bb.home_hits AS hh, bb.home_errors AS herr,
		bb.away_i1 AS ai1, bb.away_i2 AS ai2, bb.away_i3 AS ai3, bb.away_i4 AS ai4, bb.away_i5 AS ai5, bb.away_i6 AS ai6, bb.away_i7 AS ai7, bb.away_ex AS aex, bb.away_total AS at, bb.away_hits AS ah, bb.away_errors AS aerr, bb.completed AS cmp
		FROM batball AS bb JOIN schedule AS s ON bb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	}
	
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
			
		$hi1Score = $row->hi1;
		$hi2Score = $row->hi2;
		$hi3Score = $row->hi3;
		$hi4Score = $row->hi4;
		$hi5Score = $row->hi5;
		$hi6Score = $row->hi6;
		$hi7Score = $row->hi7;
		$hexScore = $row->hex;	
		
		$hhits = $row->hh;
		$herr = $row->herr;
			
		$ai1Score = $row->ai1;
		$ai2Score = $row->ai2;
		$ai3Score = $row->ai3;
		$ai4Score = $row->ai4;
		$ai5Score = $row->ai5;
		$ai6Score = $row->ai6;
		$ai7Score = $row->ai7;
		$aexScore = $row->aex;
		
		$ahits = $row->ah;
		$aerr = $row->aerr;
		
		$hTotal = $row->ht;
		$aTotal = $row->at;
		
		$comp = $row->cmp;
	}
	
	if($_POST && isset($_POST['action'])){
		$action = $_POST['action'];
		$team = $_POST['team'];
		$inning = $_POST['inning'];
		$inningText = $inning;
		
		$actionText = "";
		if($inning == 8){
			$inningText = "EX";
		}
		
		if($_POST['team'] == "FCHS"){
			$actionText = $_POST['player'] . " (" . $team . ") " . $action;
		}else{
			$actionText = $team . $action;
		}
		//POST PBP SQL
		$sql = "INSERT INTO batball_pbp (text, inning, game_id) VALUES ('$actionText', '$inningText', (SELECT id FROM schedule where id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
		
		if($action == " scores" or $action == " homers"){
			if(($homeID == 1 && $team == "FCHS") or ($homeID != 1 && $team != "FCHS")){
				switch($inning){
					case 1:
						$hi1Score += 1;
						break;
					case 2:
						$hi2Score += 1;
						break;
					case 3:
						$hi3Score += 1;
						break;
					case 4:
						$hi4Score += 1;
						break;
					case 5:
						$hi5Score += 1;
						break;
					case 6:
						$hi6Score += 1;
						break;
					case 7:
						$hi7Score += 1;
						break;
					case 8:
						$hexScore += 1;
						break;
				}
			}else{
				switch($inning){
					case 1:
						$ai1Score += 1;
						break;
					case 2:
						$ai2Score += 1;
						break;
					case 3:
						$ai3Score += 1;
						break;
					case 4:
						$ai4Score += 1;
						break;
					case 5:
						$ai5Score += 1;
						break;
					case 6:
						$ai6Score += 1;
						break;
					case 7:
						$ai7Score += 1;
						break;
					case 8:
						$aexScore += 1;
						break;
				}
			}
		}
		if($action == " singles" or $action == " doubles" or $action == " triples" or $action == " homers"){
			if(($homeID == 1 && $team == "FCHS") or ($homeID != 1 && $team != "FCHS")){
				$hhits += 1;
			}else{
				$ahits += 1;
			}
		}else if($action == " wild pitch"){
			if(($homeID == 1 && $team == "FCHS") or ($homeID != 1 && $team != "FCHS")){
				$herr += 1;
			}else{
				$aerr += 1;
			}
		}
			
		updateScore($hi1Score, $hi2Score, $hi3Score, $hi4Score, $hi5Score, $hi6Score, $hi7Score, $hexScore, $ai1Score, $ai2Score, $ai3Score, $ai4Score, $ai5Score, $ai6Score, $ai7Score, $aexScore, $hhits, $herr, $ahits, $aerr, $gameID, $db);
		$inning = $_POST['inning'];
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
		
		$sqls = "UPDATE batball SET completed = '$comp' WHERE schedule_id='$gameID'";
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
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>1</td> <td> | </td> <td>2</td> <td> | </td> <td>3</td> <td> | </td> <td>4</td> <td> | </td> <td>5</td> <td> | </td> <td>6</td> <td> | </td> <td>7</td> <td> | </td> <td>Ex</td> <td> || </td> <td> R </td><td> | </td> <td> H </td><td> | </td> <td> E </td></tr>");
	printf("<tr>	<td>----</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr>",$homeTeam, $hi1Score, $hi2Score, $hi3Score, $hi4Score, $hi5Score, $hi6Score, $hi7Score, $hexScore, $hTotal, $hhits, $herr);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr></table><br><br>", $awayTeam, $ai1Score, $ai2Score, $ai3Score, $ai4Score, $ai5Score, $ai6Score, $ai7Score, $aexScore, $aTotal, $ahits, $aerr);
	
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.inning AS inning FROM batball_pbp AS pbp JOIN schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$text = $row->text;
		$inn = $row->inning;
		$pbpText = $inn . " | " . $text;
		if(str_contains($text, "scores")){
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


<!-- INNING SELECT -->
<b>Inning: </b><select name = "inning">
<?php for($i = 1; $i < 9; $i++){
if($i == $inning){
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
<option value=" strikes out looking"> strikes out looking</option>
<option value=" strikes out swinging"> strikes out swinging</option>
<option value=" flies out"> flies out</option>
<option value=" pops out"> pops out</option>
<option value=" grounds out"> grounds out</option>
<option value=" out at first"> out at first</option>
<option value=" out at second"> out at second</option>
<option value=" out at third"> out at third</option>
<option value=" out at home"> out at home</option>
<option value=" singles"> singles</option>
<option value=" doubles"> doubles</option>
<option value=" triples"> triples</option>
<option value=" homers"> homers</option>
<option value=" scores"> scores</option>
<option value=" to first"> to first</option>
<option value=" to second"> to second</option>
<option value=" to third"> to third</option>
<option value=" walks"> walks</option>
<option value=" hit by pitch"> hit by pitch</option>
<option value=" wild pitch"> wild pitch</option>
<!--Add stealing/errors/wild pitch -->
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
<tr>	<td>Team</td> <td> | </td> 	<td>1</td> 	<td> | </td> 	<td>2</td> <td> | </td> <td>3</td> 	<td> | </td> 	<td>4</td> 	<td> | </td> 	<td>5</td> <td> | </td> <td>6</td> <td> | </td> <td>7</td> <td> | </td> <td>Ex</td> <td> || </td> <td> R </td><td> | </td> <td> H </td><td> | </td> <td> E </td></tr>
<tr>	<td>----</td> <td>-</td> 	<td>----</td> <td>-</td> 		<td>----</td> <td>-</td> 	<td>----</td> <td>-</td> 		<td>----</td> <td>-</td> 		<td>----</td> <td>-</td> <td>----</td> <td>-</td> <td>----</td> 	<td>-</td>	 <td>----</td> <td>-</td> <td>-</td> <td>-</td> <td>----</td> <td>-</td> <td>----</td></tr>
<tr><td><?php echo $homeTeam?></td> <td> | </td> <td><input type="number" id="hi1score" name = "hi1score" min = "0" max = "99" value ='<?php echo $hi1Score?>'><br></td> <td> | </td> <td><input type="number" id="hi2score" name = "hi2score" min = "0" max = "99" value ='<?php echo $hi2Score?>'></td> <td> | </td> <td><input type="number" id="hi3score" name = "hi3score" min = "0" max = "99" value ='<?php echo $hi3Score?>'></td> <td> | </td> <td><input type="number" id="hi4score" name = "hi4score" min = "0" max = "99" value ='<?php echo $hi4Score?>'></td> <td> | </td> <td><input type="number" id="hi5score" name = "hi5score" min = "0" max = "99" value ='<?php echo $hi5Score?>'></td> <td> | </td> <td><input type="number" id="hi6score" name = "hi6score" min = "0" max = "99" value ='<?php echo $hi6Score?>'></td> <td> | </td> <td><input type="number" id="hi7score" name = "hi7score" min = "0" max = "99" value ='<?php echo $hi7Score?>'></td> <td> | </td> <td><input type="number" id="hexscore" name = "hexscore" min = "0" max = "99" value ='<?php echo $hexScore?>'></td> <td> || </td> <td><b><?php echo $hTotal?></b></td> <td> | </td> <td><input type="number" id="hhits" name = "hhits" min = "0" max = "99" value ='<?php echo $hhits?>'></td> <td> | </td> <td><input type="number" id="herr" name = "herr" min = "0" max = "99" value ='<?php echo $herr?>'></td></tr>
<tr><td><?php echo $awayTeam?></td> <td> | </td> <td><input type="number" id="ai1score" name = "ai1score" min = "0" max = "99" value ='<?php echo $ai1Score?>'><br></td> <td> | </td> <td><input type="number" id="ai2score" name = "ai2score" min = "0" max = "99" value ='<?php echo $ai2Score?>'></td> <td> | </td> <td><input type="number" id="ai3score" name = "ai3score" min = "0" max = "99" value ='<?php echo $ai3Score?>'></td> <td> | </td> <td><input type="number" id="ai4score" name = "ai4score" min = "0" max = "99" value ='<?php echo $ai4Score?>'></td> <td> | </td> <td><input type="number" id="ai5score" name = "ai5score" min = "0" max = "99" value ='<?php echo $ai5Score?>'></td> <td> | </td> <td><input type="number" id="ai6score" name = "ai6score" min = "0" max = "99" value ='<?php echo $ai6Score?>'></td> <td> | </td> <td><input type="number" id="ai7score" name = "ai7score" min = "0" max = "99" value ='<?php echo $ai7Score?>'></td> <td> | </td> <td><input type="number" id="aexscore" name = "aexscore" min = "0" max = "99" value ='<?php echo $aexScore?>'></td> <td> || </td> <td><b><?php echo $aTotal?></b></td> <td> | </td> <td><input type="number" id="ahits" name = "ahits" min = "0" max = "99" value ='<?php echo $ahits?>'></td> <td> | </td> <td><input type="number" id="aerr" name = "aerr" min = "0" max = "99" value ='<?php echo $aerr?>'></td></tr>
</table>
<input type ="submit" Value="Submit">
</form>


</body>