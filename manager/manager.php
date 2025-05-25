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
$phpURL = "manager.php?gameID=".$gameID;
$schedule = 'schedule';
$table = '';//default
$season = 2024;


function createGame($table, $db, $schedule, $gameID){
	if($table == "football" or $table == "field_hockey" or $table == "basketball"  or $table == "glax" or $table == "blax"){
		$sql = "INSERT INTO $table (home_quarter1, home_quarter2, home_quarter3, home_quarter4, home_ot, away_quarter1, away_quarter2, away_quarter3, away_quarter4, away_ot, home_total, away_total, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM $schedule WHERE id='$gameID'))";
	}
	else if($table == "volleyball"){
		$sql = "INSERT INTO $table (home_set1, home_set2, home_set3, home_set4, home_set5, away_set1, away_set2, away_set3, away_set4, away_set5, home_total, away_total, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM $schedule WHERE id='$gameID'))";
	}
	else if($table == "soccer"){
		$sql = "INSERT INTO $table (home_half1, home_half2, home_OT, home_total, away_half1, away_half2, away_OT, away_total, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM $schedule WHERE id='$gameID'))";
	}
	else if($table == "batball"){
		$sql = "INSERT INTO $table (home_i1, home_i2, home_i3, home_i4, home_i5, home_i6, home_i7, home_ex, home_total, away_i1, away_i2, away_i3, away_i4, away_i5, away_i6, away_i7, away_ex, away_total, home_hits, away_hits, home_errors, away_errors, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM $schedule WHERE id='$gameID'))";
	}
	$query = $db->prepare($sql);
	$query->execute();
}

function updateScoreNew($table, $scoreArray, $gameID, $db){

	if($table == "football" or $table == "field_hockey" or $table == "basketball" or $table == "glax" or $table == "blax"){//Sports with quarters 
		$hTotal = $scoreArray[0] + $scoreArray[1] + $scoreArray[2] + $scoreArray[3] + $scoreArray[4];
		$aTotal = $scoreArray[5] + $scoreArray[6] + $scoreArray[7] + $scoreArray[8] + $scoreArray[9];
		
		$sqls = "UPDATE $table SET home_quarter1 = '$scoreArray[0]', home_quarter2 = '$scoreArray[1]', home_quarter3 = '$scoreArray[2]', home_quarter4 = '$scoreArray[3]', home_ot = '$scoreArray[4]', home_total = '$hTotal', 
			away_quarter1 = '$scoreArray[5]', away_quarter2 = '$scoreArray[6]', away_quarter3 = '$scoreArray[7]', away_quarter4 = '$scoreArray[8]', away_ot = '$scoreArray[9]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
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
		if($scoreArray[4] >= 15 and $scoreArray[4] > $scoreArray[9] + 1){
			$hTotal += 1;
		}else if($scoreArray[9] >= 15 and $scoreArray[9] > $scoreArray[4] + 1){
			$aTotal += 1;
		}
		
		$sqls = "UPDATE $table SET home_set1 = '$scoreArray[0]', home_set2 = '$scoreArray[1]', home_set3 = '$scoreArray[2]', home_set4 = '$scoreArray[3]', home_set5 = '$scoreArray[4]', home_total = '$hTotal',
		away_set1 = '$scoreArray[5]', away_set2 = '$scoreArray[6]', away_set3 = '$scoreArray[7]', away_set4 = '$scoreArray[8]', away_set5 = '$scoreArray[9]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
	}else if($table == "soccer"){//half sports
		$hTotal = $scoreArray[0] + $scoreArray[1] + $scoreArray[2];
		$aTotal = $scoreArray[3] + $scoreArray[4] + $scoreArray[5];
		
		$sqls = "UPDATE $table SET home_half1 = '$scoreArray[0]', home_half2 = '$scoreArray[1]', home_OT = '$scoreArray[2]', home_total = '$hTotal', 
			away_half1 = '$scoreArray[3]', away_half2 = '$scoreArray[4]', away_OT = '$scoreArray[5]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
	}else if($table == "batball"){//inning sports
		
		$hTotal = $scoreArray[0] + $scoreArray[1] + $scoreArray[2] + $scoreArray[3] + $scoreArray[4] + $scoreArray[5] + $scoreArray[6] + $scoreArray[7];
		$aTotal = $scoreArray[8] + $scoreArray[9] + $scoreArray[10] + $scoreArray[11] + $scoreArray[12] + $scoreArray[13] + $scoreArray[14]+ $scoreArray[15];
		
		$sqls = "UPDATE $table SET home_i1 = '$scoreArray[0]', home_i2 = '$scoreArray[1]', home_i3 = '$scoreArray[2]', home_i4 = '$scoreArray[3]', home_i5 = '$scoreArray[4]', home_i6 = '$scoreArray[5]', home_i7 = '$scoreArray[6]', home_ex = '$scoreArray[7]', home_total = '$hTotal', 
			away_i1 = '$scoreArray[8]', away_i2 = '$scoreArray[9]', away_i3 = '$scoreArray[10]', away_i4 = '$scoreArray[11]', away_i5 = '$scoreArray[12]', away_i6 = '$scoreArray[13]', away_i7 = '$scoreArray[14]', away_ex = '$scoreArray[15]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
	}

}

function updateLiveGame($db, $gameID, $period, $time, $info_1 = "", $info_2 = "", $info_3 = "", $info_4 = "", $info_5 = "", $info_6 = "", $info_7 = "", $info_8 = "", $info_9 = ""){
	
	$sqls = "UPDATE live_games SET period = '$period', game_time = '$time', info_1 = '$info_1', info_2 = '$info_2', info_3 = '$info_3', info_4 = '$info_4', info_5 = '$info_5', info_6 = '$info_6', info_7 = '$info_7', info_8 = '$info_8', info_9 = '$info_9', last_data = NOW() WHERE schedule_id='$gameID'";
	$query = $db->prepare($sqls);
	$query->execute();

}

function getPlayerID($db, $team, $player, $sport, $season){
	
	$playerID = 0;
	//echo '<script>console.log('.$team. ", " . $player. ", " . $sport . ", " . $season.');</script>';
	$sqls = "SELECT roster_player.id AS id FROM roster_player INNER JOIN roster_schools ON roster_player.school=roster_schools.id JOIN roster_teams AS sport ON sport.id=roster_player.team_id WHERE name='$player' AND team_id='$sport' AND roster_player.season='$season' AND roster_schools.short_name ='$team'";
	$query = $db->prepare($sqls);
	$query->execute();
	
	while($row = $query->fetchObject()){
		$playerID = $row->id;
	}
	return $playerID;
}

function completeGame($table, $season, $homeID, $awayID, $sportID, $gameID, $db, $hTotal, $aTotal){
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

function sumArrayRange($array, $start, $end){
	$sum = 0;
	for($i = $start; $i < $end; $i++){
		$sum += $array[$i];
	}
	return $sum;
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
	$homeRoster = array();
	$awayRoster = array();
	$pbpEntries = array();
	$homeID = 0;
	$completed = 0;
	$pbpTable = "";
	
	$sportType = ""; //quarter, half, set
	
	//MOST SPORTS (Not bat or vball)
	$minutes = 0;//currently selected minute
	$seconds = 0;//currently selected second
	$maximumMinutes = 99;//Maximum minutes for selection
	
	$period = 1;

	//temp for test of live info game
	$game_time = "12:00";
	$info_1 = "";
	$info_2 = "";
	$info_3 = "";
	$info_4 = "";
	$info_5 = "";
	$info_6 = "";
	$info_7 = "";
	$info_8 = "";
	$info_9 = "";
	
	
	//live info volley
	$set = 1;
	$serve = "FCHS";
	//$homeTO = 3;
	//$awayTO = 3;
	
	$team = "";
	$player = "";
	$jsplayer = "";
    
	
	//Get sport and set sport related variables
	$sql = "SELECT t.urlName AS sport FROM $schedule AS s JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sport = $row->sport;
	}
	
	if($sport == "football"){
		$table = "football";
		$minutes = 12;
		$seconds = 0;
		$maximumMinutes = 12;
		$info_1 = ""; //Possession
		$info_2 = 3; //Home Tos
		$info_3 = 3; //Away Tos
		$info_4 = "FCHS"; //Side of field
		$info_5 = 40; //Starting yard line
		$info_6 = 10; //yards to go
		$info_7 = 1; //down
		$sportType = "quarter";
		include './football.php'; //Import all football variables
	}else if($sport == "jvfootball"){
		$table = "football";
		$minutes = 10;
		$seconds = 0;
		$maximumMinutes = 10;
		$info_1 = ""; //Possession
		$info_2 = 3; //Home Tos
		$info_3 = 3; //Away Tos
		$info_4 = "FCHS"; //Side of field
		$info_5 = 40; //Starting yard line
		$info_6 = 10; //yards to go
		$info_7 = 1; //down
		$sportType = "quarter";
		include './football.php'; //Import all football variables
	}else if($sport == "fhockey"){
		$table = "field_hockey";
		$minutes = 15;
		$seconds = 0;
		$maximumMinutes = 15;
		$sportType = "quarter";
		include './fieldhockey.php'; //Import all field hockey variables
	}else if($sport == "jvfhockey"){
		$table = "field_hockey";
		$minutes = 12;
		$seconds = 0;
		$maximumMinutes = 12;
		$sportType = "quarter";
		include './fieldhockey.php'; //Import all field hockey variables
	}else if($sport == "vball" or $sport == "jvvball"){
		$table = "volleyball";
		$sportType = "set";
		include './volleyball.php'; //Import all volleyball variables
	}else if($sport == "bbball" or $sport == "jvbbball" or $sport == "gbball" or $sport == "jvgbball"){
		$table = "basketball";
		$minutes = 12;
		$seconds = 0;
		$maximumMinutes = 12;
		$sportType = "quarter";
		include './basketball.php'; //Import all basketball variables
	}else if($sport == "gsoccer" or $sport == "bsoccer"){
		$table = "soccer";
		$minutes = 0;
		$seconds = 0;
		$maximumMinutes = 40;
		$sportType = "half";
		include './soccer.php'; //Import all soccer variables
	}else if($sport == "jvgsoccer" or $sport == "jvbsoccer"){
		$table = "soccer";
		$minutes = 0;
		$seconds = 0;
		$maximumMinutes = 35;
		$sportType = "half";
		include './soccer.php'; //Import all soccer variables
	}else if($sport == "glax" or $sport == "jvglax"){
		$table = "glax";
		$minutes = 0;
		$seconds = 0;
		$maximumMinutes = 12;
		$sportType = "quarter";
		include './glax.php'; //Import all glax variables
	}else if($sport == "blax" or $sport == "jvblax"){
		$table = "blax";
		$minutes = 0;
		$seconds = 0;
		$maximumMinutes = 12;
		$sportType = "quarter";
		include './blax.php'; //Import all glax variables
	}else if($sport == "baseball" or $sport == "softball" or $sport == "jvbaseball" or $sport == "jvsoftball"){
		$table = "batball";
		$minutes = 0;
		$seconds = 0;
		$maximumMinutes = 0;
		$strikes = 0;
		$balls = 0;
		$outs = 0;
		$sportType = "inning";
		$game_time = "Top";
		include './batball.php'; //Import all glax variables
	}
	$pbpTable = $table . "_pbp";
	$statTable = $table . "_stats";
	//Create new game if doesn't exist
	$sql = "SELECT 1 FROM $table AS game JOIN schedule AS s ON game.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	if($query->rowCount() == 0){
		createGame($table, $db, $schedule, $gameID);
		
	}
	
	//Set defaults from database
	if($sport == "football" or $sport == "jvfootball" or $sport == "fhockey" or $sport == "jvfhockey" or $sport == "bbball" or $sport == "jvbbball" or $sport == "gbball" or $sport == "jvgbball" or $sport == "glax" or $sport == "jvglax" or $sport == "blax" or $sport == "jvblax"){//quarter sports
		$sqlsport = "SELECT s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id as aNum, s.team_id AS team, t.formattedName, 
			game.home_quarter1 AS hq1, game.home_quarter2 AS hq2, game.home_quarter3 AS hq3, game.home_quarter4 AS hq4, game.home_ot AS hot, game.home_total AS ht, 
			game.away_quarter1 AS aq1, game.away_quarter2 AS aq2, game.away_quarter3 AS aq3, game.away_quarter4 AS aq4, game.away_ot AS aot, game.away_total AS at, game.completed AS cmp
			FROM $table AS game JOIN $schedule AS s ON game.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	}else if($sport == "gsoccer" or $sport == "bsoccer" or $sport == "jvgsoccer" or $sport == "jvbsoccer"){//half sports
		$sqlsport = "SELECT s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id as aNum, s.team_id AS team, t.formattedName, 
			game.home_half1 AS hh1, game.home_half2 AS hh2, game.home_OT AS hot, game.home_total AS ht, 
			game.away_half1 AS ah1, game.away_half2 AS ah2, game.away_OT AS aot, game.away_total AS at, game.completed AS cmp
			FROM $table AS game JOIN $schedule AS s ON game.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	}else if($sport == "baseball" or $sport == "softball" or $sport == "jvbaseball" or $sport == "jvsoftball"){//inning sports
		$sqlsport = "SELECT s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id as aNum, s.team_id AS team, t.formattedName, 
			game.home_i1 AS hi1, game.home_i2 AS hi2, game.home_i3 AS hi3, game.home_i4 AS hi4, game.home_i5 AS hi5, game.home_i6 AS hi6, game.home_i7 AS hi7, game.home_ex AS hex, game.home_total AS ht, game.home_hits AS hh, game.home_errors AS he,
			game.away_i1 AS ai1, game.away_i2 AS ai2, game.away_i3 AS ai3, game.away_i4 AS ai4, game.away_i5 AS ai5, game.away_i6 AS ai6, game.away_i7 AS ai7, game.away_ex AS aex, game.away_total AS at, game.away_hits AS ah, game.away_errors AS ae, game.completed AS cmp
			FROM $table AS game JOIN $schedule AS s ON game.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	}else{//volleyball
		$sqlsport = "SELECT s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id AS aNum, s.team_id AS team, t.formattedName, 
		vb.home_set1 AS hs1, vb.home_set2 AS hs2, vb.home_set3 AS hs3, vb.home_set4 AS hs4, vb.home_set5 AS hs5, vb.home_total AS ht,
		vb.away_set1 AS as1, vb.away_set2 AS as2, vb.away_set3 AS as3, vb.away_set4 AS as4, vb.away_set5 AS as5, vb.away_total AS at, vb.completed AS cmp
		FROM $table AS vb JOIN $schedule AS s ON vb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
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

		$scoreArray = [];
		
		if($sportType == "quarter"){
			$scoreArray = [$row->hq1, $row->hq2, $row->hq3, $row->hq4, $row->hot, $row->aq1, $row->aq2, $row->aq3, $row->aq4, $row->aot];
		}else if($sport == "gsoccer" or $sport == "bsoccer" or $sport == "jvgsoccer" or $sport == "jvbsoccer"){
			$scoreArray = [$row->hh1, $row->hh2, $row->hot, $row->ah1, $row->ah2, $row->aot];
		}else if($sport == "baseball" or $sport == "softball" or $sport == "jvbaseball" or $sport == "jvsoftball"){
			$scoreArray = [$row->hi1, $row->hi2,$row->hi3, $row->hi4, $row->hi5, $row->hi6, $row->hi7, $row->hex, $row->ai1, $row->ai2,$row->ai3, $row->ai4, $row->ai5, $row->ai6, $row->ai7, $row->aex];
			$homeHits = $row->hh;
			$awayHits = $row->ah;
			$homeErrors = $row->he;
			$awayErrors = $row->ae;
		}else{
			$scoreArray = [$row->hs1, $row->hs2, $row->hs3, $row->hs4, $row->hs5, $row->as1, $row->as2, $row->as3, $row->as4, $row->as5];
		}
		
		$hTotal = $row->ht;
		$aTotal = $row->at;
		
		$completed = $row->cmp;
	}
	//Create live game
	$sql = "SELECT * FROM live_games AS game JOIN $schedule AS s ON game.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	
	if($query->rowCount() == 0 && $completed == 0 && ($homeTeam =="FCHS" or $awayTeam =="FCHS")){//TODO REFACTOR var names
		$sql = "INSERT INTO live_games (period, game_time, info_1, info_2, info_3, info_4, info_5, info_6, info_7, info_8, info_9, schedule_id) VALUES (1, '$game_time', '$info_1', '$info_2', '$info_3', '$info_4', '$info_5', '$info_6', '$info_7', '$info_8', '$info_9', (SELECT id FROM $schedule WHERE id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
	}else{
		while($row = $query->fetchObject()){
			//just football
			if($table == "football"){
				$period = $row->period;
				$poss = $row->info_1;
				$homeTimeOuts = $row->info_2;
				$awayTimeOuts = $row->info_3;
				$sof = $row->info_4;
				$yardline = $row->info_5;
				$ytg = $row->info_6;
				$down = $row->info_7;
				$displayTime = $row->game_time;
			}else{
				$period = $row->period;
				$game_time = $row->game_time;
				$info_1 = $row->info_1;
				$info_2 = $row->info_2;
				$info_3 = $row->info_3;
				$info_4 = $row->info_4;
				$info_5 = $row->info_5;
				$info_6 = $row->info_6;
				$info_7 = $row->info_7;
				$info_8 = $row->info_8;
				$info_9 = $row->info_9;
			}
		}
	}
	
	if($_POST && isset($_POST['homePeriod1Score'])){
		
		if($sport == "football" or $sport == "jvfootball" or $sport == "fhockey" or $sport == "jvfhockey" or $sport == "vball" or $sport == "jvvball" or $sport == "bbball" or $sport == "jvbbball" or $sport == "gbball" or $sport == "jvgbball" or $sport == "glax" or $sport == "jvglax" or $sport == "blax" or $sport == "jvblax"){
			$scoreArray = [$_POST['homePeriod1Score'], $_POST['homePeriod2Score'], $_POST['homePeriod3Score'], $_POST['homePeriod4Score'], $_POST['homePeriod5Score'],
					$_POST['awayPeriod1Score'], $_POST['awayPeriod2Score'], $_POST['awayPeriod3Score'], $_POST['awayPeriod4Score'], $_POST['awayPeriod5Score']];
		}else if($sport == "gsoccer" or $sport == "bsoccer" or $sport == "jvgsoccer" or $sport == "jvbsoccer"){
			$scoreArray = [$_POST['homePeriod1Score'], $_POST['homePeriod2Score'], $_POST['homePeriod3Score'],
					$_POST['awayPeriod1Score'], $_POST['awayPeriod2Score'], $_POST['awayPeriod3Score']];
		}else if($sport == "baseball" or $sport == "softball" or $sport == "jvbaseball" or $sport == "jvsoftball"){
			$scoreArray = [$_POST['homePeriod1Score'], $_POST['homePeriod2Score'], $_POST['homePeriod3Score'], $_POST['homePeriod4Score'], $_POST['homePeriod5Score'], $_POST['homePeriod6Score'], $_POST['homePeriod7Score'], $_POST['homePeriod8Score'],
					$_POST['awayPeriod1Score'], $_POST['awayPeriod2Score'], $_POST['awayPeriod3Score'], $_POST['awayPeriod4Score'], $_POST['awayPeriod5Score'], $_POST['awayPeriod6Score'], $_POST['awayPeriod7Score'], $_POST['awayPeriod8Score']];
		}
		
		updateScoreNew($table, $scoreArray, $gameID, $db);
		
		if($sport == "football" or $sport == "jvfootball"){
			$period = $_POST['quarter'];
			$seconds = $_POST['seconds'];
			if(strlen($seconds) < 2){
				$seconds = "0" . $seconds;
			}
			$displayTime = $_POST['minutes'] . ":" . $seconds;
			$team = $_POST['team'];
			$homeTimeOuts = $_POST['tohome'];
			$awayTimeOuts = $_POST['toaway'];
			$yardline = $_POST['yardLine'];
			$sof = $_POST['manSide'];
			$ytg = $_POST['ytg'];
			$down = $_POST['down'];
			updateLiveGame($db, $gameID, $period, $displayTime, $team, $homeTimeOuts, $awayTimeOuts, $sof, $yardline, $ytg, $down);
		}else if($table == "basketball"){
			$poss = $_POST['team'];
			$homeTimeOuts = $_POST['hto'];
			$awayTimeOuts = $_POST['ato'];
			updateLiveGame($db, $gameID, $period, $displayTime, $poss, $homeTimeOuts, $awayTimeOuts);
		}
	}
	
	if($_POST && isset($_POST['pbpRemove'])){
		$remove = $_POST['pbpRemove'];
		$sqls = "DELETE FROM $pbpTable WHERE id='$remove'";
		$query = $db->prepare($sqls);
		$query->execute();
	}
	
	if($_POST && isset($_POST['action'])){
		$periodLabel = $_POST['period'];
		$action = $_POST['action'];
		$team = $_POST['team'];
		$period = $_POST['period'];
		$player = $_POST['player'];
		$jsplayer = $player;
		
		if($action != "Penalty on " and $action != "Holding on " and $action != "Offsides on " and $action != "Pass interference on " and $action != "Timeout" and $table != "basketball"){
			$poss = $team;
		}
		
		$displayTime = "";
		
		
		
		if($sport != "vball" and $sport != "jvvball" and $sport != "baseball" and $sport != "jvbaseball" and $sport != "softball" and $sport != "jvsoftball"){//Sports with time
			$minutes = $_POST['minutes'];
			if($sport != "gsoccer" and $sport != "bsoccer" and $sport != "jvgsoccer" and $sport != "jvbsoccer"){
				$seconds = $_POST['seconds'];
				if(strlen($seconds) < 2){
					$seconds = "0" . $seconds;
				}
			
				$displayTime = $minutes . ":" . $seconds;
			}else{
				if($period == "1"){
					$displayTime = $minutes;
				}else if($period == "2"){
					$displayTime = $minutes + $maximumMinutes;
				}else if($period == "3"){
					$displayTime = $minutes + $maximumMinutes*2;
				}
			}
		}
		if($sport == "football" or $sport == "jvfootball"){//Football specific
			switch($down){
				case 1:
					$downText = "1st";
					break;
				case 2:
					$downText = "2nd";
					break;
				case 3:
					$downText = "3rd";
					break;
				case 4:
					$downText = "4th";
					break;
			}
			$yards = $_POST['yards'];
			$qb = $_POST['qb'];
			$driveCount = $downText . " & " . $ytg . " @ " . $sof . " " . $yardline;
			
			
			if($action == "Sack by "){//sack by opposite team so swap
				
				if($poss == $homeTeam){
					$poss = $awayTeam;
				}else{
					$poss = $homeTeam;
				}
			}
			//live game yards update
			if($sof == $poss){
				$yardline = $yardline+$yards;
			}else{
				$yardline = $yardline-$yards;
			}
			
			
			
			if($yardline > 50){//yardline # side swap
				$yardline = 100 - $yardline;
				if($sof == $homeTeam){
					$sof = $awayTeam;
				}else{
					$sof = $homeTeam;
				}
			}else if($yardline <= 0){
				$yardline = 0;
			}			
			if($action != "Return by "){
				$ytg = $ytg - $yards;
			}
			
			if($action == "Run by " or $action == "Incomplete pass to " or $action == "Reception by " or $action == "Intentional grounding on " or $action == "Sack by " or  $action == "Fumble by "){
				$down += 1;
				if($down > 4 and $ytg > $yards){
					$down = 1;
					if($poss == $homeTeam){//swap possession
						$poss = $awayTeam;
					}else{
						$poss = $homeTeam;
					}
				}
			}else if($action == "Kickoff by " or $action == "Field goal MISS by " or $action == "Interception by " or $action == "Punt by "){
				$down = 1;
				$ytg = 10;
				
				if($action != "Interception by "){
					if($poss == $homeTeam){
						$poss = $awayTeam;
					}else{
						$poss = $homeTeam;
					}
				}
			}else if($action == "Recovered by "){
				
			}
			
			if($action == "Field goal GOOD by " or $action == "Extra point GOOD by " or $action == "Extra point MISS by " or $action == "2-point conversion GOOD by " or $action == "2-point conversion FAIL by "){
				$down = 1;
				$sof = $poss;
				$yardline = 40;
				
			}else if($action == "Safety by " or $action == "Touchback"){
				if($action == "Touchback"){
					if($poss == $homeTeam){
						$poss = $awayTeam;
					}else{
						$poss = $homeTeam;
					}
				}
				$down = 1;
				$sof = $poss;
				$yardline = 20;
			}
			
			if($ytg <= 0){
				$ytg = 10;
				$down = 1;
				if($sof != $poss && $yardline < 10){
					$ytg = $yardline;
				}
			}
			
		}else if($sport == "fhockey" or $sport =="jvfhockey" or $sport == "gsoccer" or $sport == "bsoccer" or $sport == "jvgsoccer" or $sport == "jvbsoccer" or $sport == "glax" or $sport == "jvglax" or $sport == "blax" or $sport == "jvblax"){
			$hgoalie = $_POST['hgoalie'];
			$agoalie = $_POST['agoalie'];
			$assister = $_POST['assister'];
			$defense = $_POST['defense'];
			
		}else if($sport == "baseball" or $sport == "softball" or $sport == "jvbaseball" or $sport == "jvsoftball"){
			$hpitcher = $_POST['hpitcher'];
			$apitcher = $_POST['apitcher'];
			$game_time = 'Top';
			$outs = intval($info_2);
			$strikes = intval($info_3);
			$balls = intval($info_4);
			$lob = 0;
			if($action != " to second" and $action != " to third" and $action != " scores" and $action != " out at first" and $action != " out at second" and $action != " out at third" and $action != " out at home"){
				$batter = $player;
			}else{
				$batter = $info_6;
			}
			if($homeTeam == $_POST['team']){
				$game_time = 'Bot';
			}
			if($action == " strike"){
				if($strikes < 3){
					$strikes += 1;
				}
			}else if($action == " ball"){
				if($balls < 4){
					$balls += 1;
				}
			}else if($action ==" foul"){
				if($strikes < 2){
					$strikes += 1;
				}
			}else if($action == " singles"){
				$info_7 = $player;
				if($team == $homeTeam){
					$homeHits += 1;
				}else{
					$awayHits += 1;
				}
				$strikes = 0;
				$balls = 0;
			}else if($action == " doubles"){
				$info_8 = $player;
				if($team == $homeTeam){
					$homeHits += 1;
				}else{
					$awayHits += 1;
				}
				$strikes = 0;
				$balls = 0;
			}else if($action == " triples"){
				$info_9 = $player;
				if($team == $homeTeam){
					$homeHits += 1;
				}else{
					$awayHits += 1;
				}
				$strikes = 0;
				$balls = 0;
			}else if($action == " homers"){
				$info_7 = "";
				$info_8 = "";
				$info_9 = "";
				if($team == $homeTeam){
					$homeHits += 1;
				}else{
					$awayHits += 1;
				}
				$strikes = 0;
				$balls = 0;
			}else if($action == " wild pitch"){
				if($team == $homeTeam){
					$homeErrors += 1;
				}else{
					$awayErrors += 1;
				}
				$balls = $info_4;
				if($balls < 4){
					$balls += 1;
				}
			}else if($action == " strikes out looking" or $action == " strikes out swinging" or $action == " flies out" or $action == " pops out" or $action == " grounds out" or $action == " lines out" 
			or $action == " sacrifice fly" or $action == " sacrifice bunt" or $action == " out at first" or $action == " out at second" or $action == " out at third" or $action == " out at home"){
				$outs += 1;
				if($outs > 2){
					if($info_7 != ""){
						$lob += 1;
					}if($info_8 != ""){
						$lob += 1;
					}if($info_9 != ""){
						$lob += 1;
					}
					$info_7 = "";
					$info_8 = "";
					$info_9 = "";
					if($game_time == 'Top'){
						$game_time = 'Mid';
					}else if($game_time == 'Bot'){
						$game_time = 'End';
					}
					$outs = 0;
				}
				$strikes = 0;
				$balls = 0;
			}else if($action == " walks" or $action == " hit by pitch"){
				if($info_8 != "" and $info_7 != ""){
					$info_9 = $info_8;
				}
				if($info_7 != ""){
					$info_8 = $info_7;
				}
				$info_7 = $player;
				$strikes = 0;
				$balls = 0;
			}else if($action == " to second"){
				if($player == $info_7){
					$info_7 = "";
				}
				$info_8 = $player;
			}else if($action == " to third"){
				if($player == $info_7){
					$info_7 = "";
				}if($player == $info_8){
					$info_8 = "";
				}
				$info_9 = $player;
			}else if($action == " scores"){
				if($player == $info_7){
					$info_7 = "";
				}if($player == $info_8){
					$info_8 = "";
				}if($player == $info_9){
					$info_9 = "";
				}
			}
			if($action == " out at first" or $action == " out at second" or $action == " out at third" or $action == " out at home"){
				if($player == $info_7){
					$info_7 = "";
				}if($player == $info_8){
					$info_8 = "";
				}if($player == $info_9){
					$info_9 = "";
				}
			}
		}
		
		$actionText = "";
		if($sportType =="quarter"){
			if($period == 5){
				$periodLabel = "OT";
			}else{
				$periodLabel = "Q" . $periodLabel;
			}
			$sql = "INSERT INTO $pbpTable (text, quarter, time, game_id) VALUES ('$actionText', '$period', '$displayTime', (SELECT id FROM $schedule where id='$gameID'))";
		}else if($sportType =="half"){
			if($period == 3){
				$periodLabel = "OT";
			}else{
				$periodLabel = "H" . $periodLabel;
			}
			$sql = "INSERT INTO $pbpTable (text, half, time, game_id) VALUES ('$actionText', '$period', '$displayTime', (SELECT id FROM $schedule where id='$gameID'))";
		}else if($sportType =="set"){
			$periodLabel = "S" . $periodLabel;
			$sql = "INSERT INTO $pbpTable (text, set_, game_id) VALUES ('$actionText', '$period', (SELECT id FROM $schedule where id='$gameID'))";
		}else if($sportType =="inning"){
			//insert top/bot in half column
			$sql = "INSERT INTO $pbpTable (text, inning, game_id) VALUES ('$actionText', '$period', (SELECT id FROM $schedule where id='$gameID'))";
		}
		
		
		$actionText = $team . " " . $action . $player;
		
		if($sport == "football" or $sport == "jvfootball"){
			if($action == "Reception by " and $_POST['team'] == "FCHS"){
				$actionText = "Pass by " . $qb . " to " . $player . " for " .  $yards . " yards";
			}else if($action == "Extra point GOOD by " or $action == "Extra point MISS by " or $action == "2-point conversion GOOD by " or $action == "2-point conversion FAIL by "){
				$actionText = $actionText;
			}else if(($action == "Run by " or $action == "Reception by ") and $team != "FCHS" and $qb != "None"){
				$actionText = $actionText . " for " .  $yards . " yards" . " (Tackle made by " . $qb . ")";
			}else if($action == "Fumble by " and $team != "FCHS" and $qb != "None"){
				$actionText = $actionText . " (Fumble forced by " . $qb . ")";
			}else{
				$actionText = $actionText . " for " .  $yards . " yards";
			}
			
			if($action != "Return by " or $action != "Extra point GOOD by " or $action != "Extra point MISS by " or $action != "2-point conversion GOOD by " or $action != "2-point conversion FAIL by "){
				$actionText = $driveCount . ":<br>" . $actionText;
			}
			
			if($action == "Touchback" or $action == "-End of Half-"){
				$actionText = $action;
			}else if($action == "Timeout"){
				$actionText = "Timeout - " . $team;
			}
		}else if($sport == "fhockey" or $sport == "jvfhockey"){
			if($action == "Shot on goal by "){
				$actionText = "Shot on goal by " . $player . " (Save by " . $goalie . ")";
			}
		}else if($sport == "gsoccer" or $sport == "bsoccer" or $sport == "jvgsoccer" or $sport == "jvbsoccer"){
			if($action == "Shot by " && $defense != ''){
				$actionText = $team . " Shot by " . $player . " (Block by " . $defense . ")";
			}else if($action == "Shot by " && $team == $homeTeam){
				$actionText = $team . " Shot by " . $player . " (Save by " . $agoalie . ")";
			}else if($action == "Shot by " && $team == $awayTeam){
				$actionText = $team . " Shot by " . $player . " (Save by " . $hgoalie . ")";
			}else if($action == "Goal scored by " and $assister != "None" and $assister != ""){
				$actionText = $team . " Goal scored by " . $player . " (Assisted by " . $assister . ")";
			}
		}else if($sport == "glax" or $sport == "jvglax" or $sport == "blax" or $sport == "jvblax"){
			if($action == "Shot on goal by " && $team == $homeTeam){
				$actionText = $team . " Shot on goal by " . $player . " (Save by " . $agoalie . ")";
			}else if($action == "Shot on goal by " && $team == $awayTeam){
				$actionText = $team . " Shot on goal by " . $player . " (Save by " . $hgoalie . ")";
			}else if($action == "Goal scored by " and $assister != "None" and $assister != ""){
				$actionText = $team . " Goal scored by " . $player . " (Assisted by " . $assister . ")";
			}else if($action == "Turnover by "){
				$actionText = $team . " Turnover by " . $player . " (Forced by " . $defense . ")";
			}else if($action == "Faceoff won by "){
				$actionText = $team . " Faceoff won by " . $player . " vs. " . $defense;
			}
		}else if($table == "basketball"){
			if($action == "Timeout"){
				$actionText = "Timeout - " . $team;
			}else if($action == "Jump ball"){
				$actionText = "Jump ball (Poss to $poss)";
				if($poss == $homeTeam){
					$poss = $awayTeam;
				}else{
					$poss = $homeTeam;
				}
			}
		}else if($table == "batball"){
			$actionText = $team . " " . $player . $action;
		}
		if($sport == "jvvball" or $sport == "vball"){
			if($action == "Attack error by " or $action == "Service error by "){//error by team so swap
				if($team == $homeTeam){
					$team = $awayTeam;
					$poss = $awayTeam;
				}else{
					$team = $homeTeam;
					$poss = $homeTeam;
				}
			}
		}
		
		$scoreAmount = 0;
		
		//Can this be made more automatic/cleaner?
		
		if($action == "Extra point GOOD by " or $action == "Goal scored by " or $action == "Kill by " or $action == "Attack error by " or $action == "Service ace by " or $action == "Service error by " or $action == "Block by " or $action == "Free throw by " or $action == " scores" or $action == " homers"){
			$scoreAmount = 1;
		}else if($action == "Safety by " or $action == "Jumper by " or $action == "Layup by " or $action == "Dunk by "){
			$scoreAmount = 2;
		}else if($action == "Field goal GOOD by " or $action == "3 Pointer by "){
			$scoreAmount = 3;
		}else if($action == "Touchdown by " or $action == "Touchdown reception by "){
			$scoreAmount = 6;
		}else if($action == "Timeout by " or $action == "Timeout"){
			if($team == $homeTeam){
				$homeTimeOuts = (int)$homeTimeOuts - 1;
			}else{
				$awayTimeOuts = (int)$awayTimeOuts - 1;
			}
		}

		
		if($scoreAmount != 0){
			if($team == $homeTeam){
				$scoreArray[$period - 1] += $scoreAmount;
			}else{
				switch($period){
					case 1:
						if($sportType == "half"){
							$scoreArray[3] += $scoreAmount;
						}else if($sportType == "inning"){
							$scoreArray[8] += $scoreAmount;
						}else{
							$scoreArray[5] += $scoreAmount;
						}
						break;
					case 2:
						if($sportType == "half"){
							$scoreArray[4] += $scoreAmount;
						}else if($sportType == "inning"){
							$scoreArray[9] += $scoreAmount;
						}else{
							$scoreArray[6] += $scoreAmount;
						}
						break;
					case 3:
						if($sportType == "half"){
							$scoreArray[5] += $scoreAmount;
						}else if($sportType == "inning"){
							$scoreArray[10] += $scoreAmount;
						}else{
							$scoreArray[7] += $scoreAmount;
						}
						break;
					case 4:
						if($sportType == "inning"){
							$scoreArray[11] += $scoreAmount;
						}else{
							$scoreArray[8] += $scoreAmount;
						}
						break;
					case 5:
						if($sportType == "inning"){
							$scoreArray[12] += $scoreAmount;
						}else{
							$scoreArray[9] += $scoreAmount;
						}
						break;
					case 6:
						$scoreArray[13] += $scoreAmount;
						break;
					case 7:
						$scoreArray[14] += $scoreAmount;
						break;
					case 8:
						$scoreArray[15] += $scoreAmount;
						break;
				}
			}
			updateScoreNew($table, $scoreArray, $gameID, $db);
		}
		
		//POST PBP SQL =( that this is the same as above
		if($sportType =="quarter"){
			$sql = "INSERT INTO $pbpTable (text, quarter, time, game_id) VALUES ('$actionText', '$period', '$displayTime', (SELECT id FROM $schedule where id='$gameID'))";
		}else if($sportType =="half"){
			$sql = "INSERT INTO $pbpTable (text, half, time, game_id) VALUES ('$actionText', '$period', '$displayTime', (SELECT id FROM $schedule where id='$gameID'))";
		}else if($sportType =="set"){
			$actionText = $actionText . " | " . $scoreArray[$period-1] . "-" . $scoreArray[$period+4];
			$sql = "INSERT INTO $pbpTable (text, set_, game_id) VALUES ('$actionText', '$period', (SELECT id FROM $schedule where id='$gameID'))";
		}else if($sportType =="inning"){
			$sql = "INSERT INTO $pbpTable (text, inning, game_id) VALUES ('$actionText', '$period', (SELECT id FROM $schedule where id='$gameID'))";
		}
		$query = $db->prepare($sql);
		$query->execute();
		
		if($sport == "jvfhockey" or $sport == "fhockey"){
			updateLiveGame($db, $gameID, $period, $displayTime, $goalie);
		}else if($table == "basketball"){
			updateLiveGame($db, $gameID, $period, $displayTime, $poss, $homeTimeOuts, $awayTimeOuts);
		}else if($table == "batball"){
			if($team == $homeTeam){
				$pitcher = $apitcher;
			}else{
				$pitcher = $hpitcher;
			}
			
			updateLiveGame($db, $gameID, $period, $game_time, "", $outs, $strikes, $balls, $pitcher, $batter, $info_7, $info_8, $info_9);
		}else{
			updateLiveGame($db, $gameID, $period, $displayTime);
		}
		
		if($sport == "jvvball" or $sport == "vball"){
			if($action == "Attack error by " or $action == "Service error by "){//error by team so swap
				if($team == $homeTeam){
					$team = $awayTeam;
				}else{
					$team = $homeTeam;
				}
			}
		}//TEMP FIX SWAP BACK PLEASE FIX LATER, -UPDATE- 2025-01-09 WTF DOES THIS FIX WHY IS THIS HERE
		
		include './stats.php'; //Stats Manager
	}
	
	if($_POST && isset($_POST['complete'])){
		completeGame($table, $season, $homeID, $awayID, $sportID, $gameID, $db, $hTotal, $aTotal);
		
	}
	
	/*
	#########################
	#						#
	#		SCORE TABLE		#CURRENTLY ONLY WORKS FOR QUARTER SPORTS REFACTOR
	#						#
	#########################
	*/

	//table start
	printf("<table>");
	
	for($t = 0; $t < 4; $t++){//each row--- Row 1| Labels, Row 2| Top Line, Row 3| Team 1, Row 4| Team 2
		printf("<tr>");
		for($i = -1; $i < count($scoreArray); $i++){
			if($i < 0){//First column has unique table row labels
				switch ($t){
					case "0":
						printf("<td>Team</td>");
						break;
					case "1":
						printf("<td>----</td>");
						break;
					case "2":
						printf("<td>%s</td>", $awayTeam);
						break;
					case "3":
						printf("<td>%s</td>", $homeTeam);
						break;
				}
			}else{//Every other colums
				if($i % 2){ //Odd columns are info
					if($t == 0){
						if($i == 9){//Replace last label as OT rather than #
							printf("<td> OT </td>");
						}else{
							printf("<td>Qrtr %s</td>", ($i + 1) /2 );
						}
					}else if($t == 1){
						printf("<td>----</td>");
					}else{//Gets correct score value depending on row $t
						printf("<td>%s</td>", $scoreArray[$i/2 + (abs($t - 3) * (count($scoreArray)/2))]);
					}
				}else{//Even columns are dividers
					if($t == 1){
						printf("<td>-</td>");
					}else{
						printf("<td> | </td>");
					}
				}
			}
		}
		if($t == 0){
			printf("<td> | </td> <td>Total</td> ");
		}else if($t == 1){
			printf("<td>-</td><td>----</td>");
		}else{
			printf("<td> | </td> <td> %s </td> ", sumArrayRange($scoreArray, abs($t - 3) * (count($scoreArray)/2), abs($t - 4) * (count($scoreArray)/2)));//Calculates total of scores in that subsection of array
		}
		printf("</tr>");
	}
	
	printf("</table>");
	
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	if($sport == "football"  or $sport == "jvfootball"){
		printf("Poss: %s<br>", $poss);
		printf("%s & %s @ %s %s<br>", $down, $ytg, $sof, $yardline);
		printf("Home TOs: %s<br>", $homeTimeOuts);
		printf("Away TOs: %s<br><br>", $awayTimeOuts);
	}else if($table == "batball"){
		printf("Pitching: %s<br>", $info_5);
		printf("At Bat: %s %s-%s, %s Outs<br>", $info_6, $balls, $strikes, $outs);
		printf("1B: %s<br>", $info_7);
		printf("2B: %s<br>", $info_8);
		printf("3B: %s<br>", $info_9);
	
	}
	
	if($sportType =="quarter"){
		$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.quarter AS period, pbp.time AS tme FROM $pbpTable AS pbp JOIN $schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	}else if($sportType =="half"){
		$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.half AS period, pbp.time AS tme FROM $pbpTable AS pbp JOIN $schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	}else if($sportType =="set"){
		$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.set_ AS period FROM $pbpTable AS pbp JOIN $schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	}else if($sportType =="inning"){
		$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.inning AS period FROM $pbpTable AS pbp JOIN $schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	}
	
	if($homeTeam == "FCHS" or $awayTeam == "FCHS"){
		$query = $db->prepare($sql);
		$query->execute();
	
		while($row = $query->fetchObject()){
			$text = $row->text;
			$period = $row->period;
			$time = "";
			if($sportType != "set" and $sportType != "inning"){
				$time = $row->tme;
			}
			$pbpText = $period . " " . $time . " | " . $text;
			/*if(str_contains($text, "Touchdown") or str_contains($text, "Field goal GOOD") or str_contains($text, "Safety") or str_contains($text, "Extra point GOOD") or str_contains($text, "2-point conversion GOOD") or str_contains($text, "Goal")){
				printf("<b>%s</b><br>", $pbpText);
			}else{
				printf("%s<br>", $pbpText);
			}*/
			array_push($pbpEntries, [$row->pbpID, $pbpText]);
			
		}
		if (count($pbpEntries) > 1){ 
			printf("Last 2 plays: <br><br>%s<br><br>%s<br>", $pbpEntries[count($pbpEntries)-2][1], $pbpEntries[count($pbpEntries)-1][1]);
		}else if(count($pbpEntries) > 0){
			printf("Last play: <br><br>%s<br>", $pbpEntries[count($pbpEntries)-1][1]);
		}
	}
	
	if($completed == 1){
		printf("<br>-END OF GAME-");
	}
	
	//GAME MANAGER
	printf("<hr><h3>Game Manager</h3>");
	
if($completed == 0){
?>
<form action = "<?php echo $phpURL?>" method="POST">
<input type="hidden" id="complete" name = "complete" value="1">
<input type ="submit" Value="Complete">
</form>

<?php
}
?>
<br>
<div class="flex justify-between">
<?php

	printf("<div>-Home Roster- <br>");
	//Home Roster
	$sql = "SELECT r.name AS player, r.number AS number FROM roster_player AS r INNER JOIN roster_schools ON r.school=roster_schools.id JOIN roster_teams AS t ON r.team_id=t.id WHERE r.season = '$season' AND t.urlName='$sport' AND roster_schools.short_name ='$homeTeam'
	ORDER BY (CASE WHEN cast(r.number as unsigned) = 0 THEN 999997 ELSE cast(r.number as unsigned) END)";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$player = $row->player;
		$number = $row->number;
		if($number != "Head Coach" and $number != "Assistant Coaches" and $number != "Assistant Coach" and $number != "Managers"){
			array_push($homeRoster, $player);
			printf("%s | %s<br>", $row->number, $row->player);
		}
	}
	array_push($homeRoster, $homeTeam);
	printf("</div><div>-Away Roster- <br>");
	//Away Roster
	$sql = "SELECT r.name AS player, r.number AS number FROM roster_player AS r INNER JOIN roster_schools ON r.school=roster_schools.id JOIN roster_teams AS t ON r.team_id=t.id WHERE r.season = '$season' AND t.urlName='$sport' AND roster_schools.short_name ='$awayTeam'
	ORDER BY (CASE WHEN cast(r.number as unsigned) = 0 THEN 999997 ELSE cast(r.number as unsigned) END)";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$player = $row->player;
		$number = $row->number;
		if($number != "Head Coach" and $number != "Assistant Coaches" and $number != "Assistant Coach" and $number != "Managers"){
			array_push($awayRoster, $player);
			printf("%s | %s<br>", $row->number, $row->player);
		}
	}
	array_push($awayRoster, $awayTeam);
?>
</div>
</div>

<br>
<h3>-Automatic-</h3>

<h4>Play-By-Play</h4>
<form action = "<?php echo $phpURL?>" method="POST">
<?php
if($sport == "football" or $sport == "jvfootball"){
	
	printf('Quarterback: <select name = "qb">');
	foreach($homeRoster as $qbs){
		if($qbs == $qb){
			echo("<option value = '$qbs' selected>$qbs</option>");
		}else{
			echo("<option value = '$qbs'>$qbs</option>");
		}
	}
	echo("<option value = 'None'>None</option>");

	printf("</select><br>");
}else if($sport == "fhockey" or $sport == "jvfhockey"){
	printf('Goalie/Defense: <select name = "goalie">');
	foreach($homeRoster as $goalies){
		if($goalies == $goalie){
			echo("<option value = '$goalies' selected>$goalies</option>");
		}else{
			echo("<option value = '$goalies'>$goalies</option>");
		}
	}
	echo("<option value = 'None'>None</option>");
	printf("</select><br>");


/*
	#########################
	#						#
	#		Soccer			#
	#						#
	#########################
	*/


}else if($sport == "gsoccer" or $sport == "jvgsoccer" or $sport == "bsoccer" or $sport == "jvbsoccer"){
?>
	<!-- Period SELECT -->
	<b>H </b>
	<select name = "period">
	<?php for($i = 1; $i < 4; $i++){//REFACTOR limit to maximum periods (works for fall)
	if($i == $period){
		echo("<option value = '$i' selected>$i</option>");
	}else{
		echo("<option value = '$i'>$i</option>");
	}
	}?>
</select>
<?php
	echo("<select name = 'minutes'>");
	 for($i = 0; $i < $maximumMinutes+1; $i++){
		if($i == $minutes){
			echo("<option value = '$i' selected>$i'</option>");
		}else{
			echo("<option value = '$i'>$i'</option>");
		}
	}
	echo("</select>");
	?>
	<select name = "action">
	<?php foreach($actionList as $option){ //REFACTORED ALREADY
		printf($option);
	}
	?>
	</select>
	<br><br>


	<select name = "team" id ="teamSelect">
		<option value="" selected = "selected">Select Team</option>
	</select>

	<select name = "player" id="playerSelect">
	<option value = "" selected = "selected">Select Player</option>");
	</select>
	<br><br>Assist:
	<select name = "assister" id="assistSelect">
	<option value = "" selected = "selected">Select Assist</option>");
	</select>
	
	<br><br>Defense:
	<select name = "defense" id="pitcherSelect">
	<option value = "" selected = "selected">Select Defense</option>;
	</select>
<?php

	printf('<br><br>Home Goalie: <select name = "hgoalie">');
	foreach($homeRoster as $goalies){
		if($goalies == $hgoalie){
			echo("<option value = '$goalies' selected>$goalies</option>");
		}else{
			echo("<option value = '$goalies'>$goalies</option>");
		}
	}
	echo("<option value = 'None'>None</option>");
	printf("</select><br><br>");
	
	printf('Away Goalie: <select name = "agoalie">');
	foreach($awayRoster as $goalies){
		if($goalies == $agoalie){
			echo("<option value = '$goalies' selected>$goalies</option>");
		}else{
			echo("<option value = '$goalies'>$goalies</option>");
		}
	}
	echo("<option value = 'None'>None</option>");
	printf("</select><br>");
	
	
	/*
	#########################
	#						#
	#		Lax				#
	#						#
	#########################
	*/
	
}else if($sport == "glax" or $sport == "jvglax" or $sport == "blax" or $sport == "jvblax"){
?>
	<!-- Period SELECT -->
	<b>Q </b>
	<select name = "period">
	<?php for($i = 1; $i < 6; $i++){//REFACTOR limit to maximum periods (works for fall)
	if($i == $period){
		echo("<option value = '$i' selected>$i</option>");
	}else{
		echo("<option value = '$i'>$i</option>");
	}
	}?>
</select>
<?php
	echo("<select name = 'minutes'>");
	 for($i = 0; $i < $maximumMinutes+1; $i++){
		if($i == $minutes){
			echo("<option value = '$i' selected>$i</option>");
		}else{
			echo("<option value = '$i'>$i</option>");
		}
	}
	echo('</select>:<select name = "seconds">');
	for($i = 0; $i < 60; $i++){
		if($i == $seconds){
			echo("<option value = '$i' selected>$i</option>");
		}else{
			echo("<option value = '$i'>$i</option>");
		}
	}
	echo("</select>");
	?>
	<select name = "action">
	<?php foreach($actionList as $option){ //REFACTORED ALREADY
		printf($option);
	}
	?>
	</select>
	<br><br>


	<select name = "team" id ="teamSelect">
		<option value="" selected = "selected">Select Team</option>
	</select>

	<select name = "player" id="playerSelect">
	<option value = "" selected = "selected">Select Player</option>");
	</select>
	<br><br>Assist:
	<select name = "assister" id="assistSelect">
	<option value = "" selected = "selected">Select Assist</option>");
	</select>
	
	<br><br>Defense:
	<select name = "defense" id="pitcherSelect">
	<option value = "" selected = "selected">Select Defense</option>;
	</select>
<?php

	printf('<br><br>Home Goalie: <select name = "hgoalie">');
	foreach($homeRoster as $goalies){
		if($goalies == $hgoalie){
			echo("<option value = '$goalies' selected>$goalies</option>");
		}else{
			echo("<option value = '$goalies'>$goalies</option>");
		}
	}
	echo("<option value = 'None'>None</option>");
	printf("</select><br><br>");
	
	printf('Away Goalie: <select name = "agoalie">');
	foreach($awayRoster as $goalies){
		if($goalies == $agoalie){
			echo("<option value = '$goalies' selected>$goalies</option>");
		}else{
			echo("<option value = '$goalies'>$goalies</option>");
		}
	}
	echo("<option value = 'None'>None</option>");
	printf("</select><br>");
	
	
	/*
	#########################
	#						#
	#		Batball			#
	#						#
	#########################
	*/
	
	
	
}else if($sport == "baseball" or $sport == "jvbaseball" or $sport == "softball" or $sport == "jvsoftball"){
?>
	<!-- Period SELECT -->
	<b>I </b>
	<select name = "period">
	<?php for($i = 1; $i < 9; $i++){//REFACTOR limit to maximum periods (works for fall)
	if($i == $period){
		echo("<option value = '$i' selected>$i</option>");
	}else{
		echo("<option value = '$i'>$i</option>");
	}
	}?>
	</select>
	<br><br>

	<select name = "team" id ="teamSelect">
		<option value="" selected = "selected">Select Team</option>
	</select>

	<select name = "player" id="playerSelect">
	<option value = "" selected = "selected">Select Player</option>;
	</select>
	<select name = "action">
	<?php foreach($actionList as $option){ //REFACTORED ALREADY
		printf($option);
	}
	?>
	</select>

	<?php
	printf('<br><br>Home Pitcher: <select name = "hpitcher">');
	foreach($homeRoster as $pitchers){
		if($pitchers == $hpitcher){
			echo("<option value = '$pitchers' selected>$pitchers</option>");
		}else{
			echo("<option value = '$pitchers'>$pitchers</option>");
		}
	}
	echo("<option value = 'None'>None</option>");
	printf("</select><br><br>");
	
	printf('Away Pitcher: <select name = "apitcher">');
	foreach($awayRoster as $pitchers){
		if($pitchers == $apitcher){
			echo("<option value = '$pitchers' selected>$pitchers</option>");
		}else{
			echo("<option value = '$pitchers'>$pitchers</option>");
		}
	}
	echo("<option value = 'None'>None</option>");
	printf("</select><br>");


	

}else{?>

<!-- Period SELECT -->
	<b>Q </b>
	<select name = "period">
	<?php 
	for($i = 1; $i < 6; $i++){//REFACTOR limit to maximum periods (works for fall)
		if($i == $period){
			echo("<option value = '$i' selected>$i</option>");
		}else{
			echo("<option value = '$i'>$i</option>");
		}
	}?>
	</select>

<!-- TIME SELECT -->
<?php
	if($sport != "vball" and $sport != "jvvball" and $sport != "baseball" and $sport != "jvbaseball" and $sport != "softball" and $sport != "jvsoftball"){
		echo("<select name = 'minutes'>");
		 for($i = 0; $i < $maximumMinutes+1; $i++){
			if($i == $minutes){
				echo("<option value = '$i' selected>$i</option>");
			}else{
				echo("<option value = '$i'>$i</option>");
			}
		}
		echo('</select>:<select name = "seconds">');
		 for($i = 0; $i < 60; $i++){
			if($i == $seconds){
				echo("<option value = '$i' selected>$i</option>");
			}else{
				echo("<option value = '$i'>$i</option>");
			}
		}
		echo("</select>");
	 }?>


	<select name = "action">
	<?php 
	foreach($actionList as $option){ //REFACTORED ALREADY
		printf($option);
	}
	?>
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
	<?php 
	foreach($homeRoster as $opt){
		if($opt == $_POST['player']){
			echo("<option value = '$opt' selected>$opt</option>");
		}else{
			echo("<option value = '$opt'>$opt</option>");
		}
	}
	?>
	</select>
<?php 
}
if($sport == "football" or $sport == "jvfootball"){
	
	printf('for <select name = "yards">');
	 
	for($i = -100; $i < 100; $i++){
		if($i == 0){
			echo("<option value = '$i' selected>$i</option>");
		}else{
			echo("<option value = '$i'>$i</option>");
		}
	}

	printf("</select> yards ");
}?>
<br><br>
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
<!--REFACTOR TO HERE make similar to rework up top-->
<form action = "<?php echo $phpURL?>" method="POST">

<?php 
	if($sport == "football" or $sport == "jvfootball"){
		
		printf("Quarter: <input type='number' id = 'quarter' name = 'quarter' min = '0' max = '5' value = '%s'> ", $period);
		echo("<select name = 'minutes'>");
		for($i = 0; $i < $maximumMinutes+1; $i++){
			if($i == $minutes){
				echo("<option value = '$i' selected>$i</option>");
			}else{
				echo("<option value = '$i'>$i</option>");
			}
		}
		echo('</select>:<select name = "seconds">');
		 for($i = 0; $i < 60; $i++){
			if($i == $seconds){
				echo("<option value = '$i' selected>$i</option>");
			}else{
				echo("<option value = '$i'>$i</option>");
			}
		}
		echo("</select> ");
		printf("Poss: <select name = 'team'><option value='%s' selected>%s</option>", $homeTeam, $homeTeam);
		printf("<option value='%s'>%s</option></select><br>", $awayTeam, $awayTeam);
		printf("TO Home: <input type='number' id = 'tohome' name = 'tohome' min = '0' max = '3' value = '%s'> ", $homeTimeOuts);
		printf("TO Away: <input type='number' id = 'toaway' name = 'toaway' min = '0' max = '3' value = '%s'><br>", $awayTimeOuts);
		printf("Side: <select name = 'manSide'><option value='%s' selected>%s</option>", $homeTeam, $homeTeam);
		printf("<option value='%s'>%s</option></select> ", $awayTeam, $awayTeam);
		printf("Yard line: <input type='number' id = 'yardLine' name = 'yardLine' min = '0' max = '50' value = '%s'><br>", $yardline);
		printf("YTG: <input type='number' id = 'ytg' name = 'ytg' min = '0' max = '100' value = '%s'> ", $ytg);
		printf("Down: <input type='number' id = 'down' name = 'down' min = '0' max = '4' value = '%s'><br><br>", $down);
	}else if($table == "basketball"){
		printf("Poss: <select name = 'team'><option value='%s' selected>%s</option>", $homeTeam, $homeTeam);
		printf("<option value='%s'>%s</option></select><br>", $awayTeam, $awayTeam);
		printf("Home TOs: <input type='number' id = 'hto' name = 'hto' min = '0' max = '9' value = '%s'><br>", $homeTimeOuts);
		printf("Away TOs: <input type='number' id = 'ato' name = 'ato' min = '0' max = '9' value = '%s'><br><br>", $awayTimeOuts);
	}
//table start
	printf("<table>");
	
	for($t = 0; $t < 4; $t++){//each row--- Row 1| Labels, Row 2| Top Line, Row 3| Team 1, Row 4| Team 2
		printf("<tr>");
		for($i = -1; $i < count($scoreArray); $i++){
			if($i < 0){//First column has unique table row labels
				switch ($t){
					case "0":
						printf("<td>Team</td>");
						break;
					case "1":
						printf("<td>----</td>");
						break;
					case "2":
						printf("<td>%s</td>", $awayTeam);
						break;
					case "3":
						printf("<td>%s</td>", $homeTeam);
						break;
				}
			}else{//Every other colums
				if($i % 2){ //Odd columns are info
					if($t == 0){
						if($i == 9 && $table != "batball"){//Replace last label as OT rather than #
							printf("<td> OT </td>");
						}else{
							printf("<td>Qrtr %s</td>", ($i + 1) /2 );
						}
					}else if($t == 1){
						printf("<td>----</td>");
					}else if($t == 2){
						$idName = 'awayPeriod' . ($i + 1)/2 . 'Score';
						printf("<td><input type='number' id='%s' name = '%s' min = '0' max = '99' value ='%s'></td>", $idName, $idName, $scoreArray[$i/2 + (abs($t - 3) * (count($scoreArray)/2))]);
					}else{
						$idName = 'homePeriod' . ($i + 1)/2 . 'Score';
						printf("<td><input type='number' id='%s' name = '%s' min = '0' max = '99' value ='%s'></td>", $idName, $idName, $scoreArray[$i/2 + (abs($t - 3) * (count($scoreArray)/2))]);
					}
				}else{//Even columns are dividers
					if($t == 1){
						printf("<td>-</td>");
					}else{
						printf("<td> | </td>");
					}
				}
			}
		}
		if($t == 0){
			printf("<td> | </td> <td>Total</td> ");
		}else if($t == 1){
			printf("<td>-</td><td>----</td>");
		}else{
			printf("<td> | </td> <td> %s </td> ", sumArrayRange($scoreArray, abs($t - 3) * (count($scoreArray)/2), abs($t - 4) * (count($scoreArray)/2)));//Calculates total of scores in that subsection of array
		}
		printf("</tr>");
	}
	
	printf("</table>");
	?>
<input type ="submit" Value="Submit">
</form>

</body>


<script>
var playerList = {"<?php echo $homeTeam?>": <?php echo json_encode($homeRoster)?>, "<?php echo $awayTeam?>":  <?php echo json_encode($awayRoster)?>};
window.onload = function(){
	var teamSelect = document.getElementById("teamSelect");
	var playerSelect = document.getElementById("playerSelect");
	if("<?php echo $table?>" != "batball"){
		var assistSelect = document.getElementById("assistSelect");
	}
	if("<?php echo $table?>" == "glax" || "<?php echo $table?>" == "blax" || "<?php echo $table?>" == "soccer"){
		var pitcherSelect = document.getElementById("pitcherSelect");
	}
	for(var x in playerList){
		teamSelect.options[teamSelect.options.length] = new Option(x, x);
	}
	teamSelect.onchange = function(){
		playerSelect.length = 1;
		var z = playerList[teamSelect.value];
		var opp = "<?php echo $homeTeam?>";
		if(teamSelect.value == "<?php echo $homeTeam?>"){
			opp = "<?php echo $awayTeam?>";
		}
		var y = playerList[opp];
		
		for(var i = 0; i < z.length; i++){
			playerSelect.options[playerSelect.options.length] = new Option(playerList[teamSelect.value][i], playerList[teamSelect.value][i]);
			if("<?php echo $table?>" != "batball"){
				assistSelect.options[assistSelect.options.length] = new Option(playerList[teamSelect.value][i], playerList[teamSelect.value][i]);
			}
		}
		if("<?php echo $table?>" == "glax" || "<?php echo $table?>" == "blax" || "<?php echo $table?>" == "soccer"){
			for(var i = 0; i < y.length; i++){
				pitcherSelect.options[pitcherSelect.options.length] = new Option(playerList[opp][i], playerList[opp][i]);
			}
		}
	}
	
	if("<?php echo $table?>" == "batball" && "<?php echo $team?>" != "" && "<?php echo $jsplayer?>" != ""){
		teamSelect.value = '<?php echo $team?>';
		var z = playerList[teamSelect.value];
		for(var i = 0; i < z.length; i++){
			playerSelect.options[playerSelect.options.length] = new Option(playerList[teamSelect.value][i], playerList[teamSelect.value][i]);
		}
		playerSelect.value = '<?php echo $jsplayer?>';
	}
}

</script>