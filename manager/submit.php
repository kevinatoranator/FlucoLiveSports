<?php
include '../include/database.php';

	$table = $_GET['table'];
	$tablePBP = $table . "_pbp";
	$tableStats = $table . "_stats";
	$gameID = $_GET['gameID'];
	$infoArray = $_GET['infoArray'];
	$scores = $_GET['scores'];
	$home = $_GET['home'];
	$action = $infoArray[0];
	$team = $infoArray[1];
	$oppteam = $infoArray[2];
	$player = $infoArray[3];
	$period = $infoArray[4];
	$time = $infoArray[5];
	$sportID = $infoArray[6];
	$goalie = $infoArray[7];
	$assister = "";
	$defense = "";
	$completed = 0;
	$season = 2024;
	$sql = "";
	
	enum SPORTTYPE{
		case Half;
		case Quarter;
		case Inning;
		case Set;
	}

	$gameType = SPORTTYPE::Half;
	
	$pbp = "$team $action$player";
	
	$message = "";
	if($table == "soccer"){//half sport
		$sql = "INSERT INTO $tablePBP (text, half, time, game_id) VALUES ('$pbp', '$period', '$time', (SELECT id FROM schedule where id='$gameID'))";
	}else if($table == "batball"){//inning
		$gameType = SPORTTYPE::Inning;
		$sql = "INSERT INTO $tablePBP (text, inning, game_id) VALUES ('$pbp', '$period', (SELECT id FROM schedule where id='$gameID'))";
	}else if($table == "blax"){//quarter
	$gameType = SPORTTYPE::Quarter;
		$sql = "INSERT INTO $tablePBP (text, quarter, time, game_id) VALUES ('$pbp', '$period', '$time', (SELECT id FROM schedule where id='$gameID'))";
	}
	$query = $db->prepare($sql);
	$query->execute();
	
	
	/*
	#########################
	#						#
	#		SCORING			#
	#						#
	#########################

	*/
	$points = 0;
	if($action == "Goal scored by "){
		$points += 1;
	}
	if($points > 0){
		if($gameType == SPORTTYPE::Half){
			if($team == $home){
				if($period == 1){
					$scores[0] += $points;
				}else if($period == 2){
					$scores[1] += $points;
				}else if($period == 3){
					$scores[2] += $points;
				}
				
			}else{
				if($period == 1){
					$scores[3] += $points;
				}else if($period == 2){
					$scores[4] += $points;
				}else if($period == 3){
					$scores[5] += $points;
				}
			}
		}else if($gameType == SPORTTYPE::Quarter){
			if($team == $home){
				if($period == 1){
					$scores[0] += $points;
				}else if($period == 2){
					$scores[1] += $points;
				}else if($period == 3){
					$scores[2] += $points;
				}else if($period == 4){
					$scores[3] += $points;
				}else if($period == 5){
					$scores[4] += $points;
				}
				
			}else{
				if($period == 1){
					$scores[5] += $points;
				}else if($period == 2){
					$scores[6] += $points;
				}else if($period == 3){
					$scores[7] += $points;
				}else if($period == 4){
					$scores[8] += $points;
				}else if($period == 5){
					$scores[9] += $points;
				}
			}
		}
	}
	

	if($table == "football" or $table == "field_hockey" or $table == "basketball" or $table == "glax" or $table == "blax"){//Sports with quarters 
		$hTotal = $scores[0] + $scores[1] + $scores[2] + $scores[3] + $scores[4];
		$aTotal = $scores[5] + $scores[6] + $scores[7] + $scores[8] + $scores[9];
		
		$sql = "UPDATE $table SET home_quarter1 = '$scores[0]', home_quarter2 = '$scores[1]', home_quarter3 = '$scores[2]', home_quarter4 = '$scores[3]', home_ot = '$scores[4]', home_total = '$hTotal', 
			away_quarter1 = '$scores[5]', away_quarter2 = '$scores[6]', away_quarter3 = '$scores[7]', away_quarter4 = '$scores[8]', away_ot = '$scores[9]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
		
	}else if($table == "volleyball"){//volleyball (unique with sets)
		$hTotal = 0;
		$aTotal = 0;
		if($scores[0] >= 25 and $scores[0] > $scores[5] + 1){
			$hTotal += 1;
		}else if($scores[5] >= 25 and $scores[5] > $scores[0] + 1){
			$aTotal += 1;
		}
		if($scores[1] >= 25 and $scores[1] > $scores[6] + 1){
			$hTotal += 1;
		}else if($scores[6] >= 25 and $scores[6] > $scores[1] + 1){
			$aTotal += 1;
		}
		if($scores[2] >= 25 and $scores[2] > $scores[7] + 1){
			$hTotal += 1;
		}else if($scores[7] >= 25 and $scores[7] > $scores[2] + 1){
			$aTotal += 1;
		}
		if($scores[3] >= 25 and $scores[3] > $scores[8] + 1){
			$hTotal += 1;
		}else if($scores[8] >= 25 and $scores[8] > $scores[3] + 1){
			$aTotal += 1;
		}
		if($scores[4] >= 15 and $scores[4] > $scores[9] + 1){
			$hTotal += 1;
		}else if($scores[9] >= 15 and $scores[9] > $scores[4] + 1){
			$aTotal += 1;
		}
		
		$sql = "UPDATE $table SET home_set1 = '$scores[0]', home_set2 = '$scores[1]', home_set3 = '$scores[2]', home_set4 = '$scores[3]', home_set5 = '$scores[4]', home_total = '$hTotal',
		away_set1 = '$scores[5]', away_set2 = '$scores[6]', away_set3 = '$scores[7]', away_set4 = '$scores[8]', away_set5 = '$scores[9]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
	}else if($table == "soccer"){//half sports
		$hTotal = $scores[0] + $scores[1] + $scores[2];
		$aTotal = $scores[3] + $scores[4] + $scores[5];
		
		$sql = "UPDATE $table SET home_half1 = '$scores[0]', home_half2 = '$scores[1]', home_OT = '$scores[2]', home_total = '$hTotal', 
			away_half1 = '$scores[3]', away_half2 = '$scores[4]', away_OT = '$scores[5]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
	}else if($table == "batball"){//inning sports
		
		$hTotal = $scores[0] + $scores[1] + $scores[2] + $scores[3] + $scores[4] + $scores[5] + $scores[6] + $scores[7];
		$aTotal = $scores[8] + $scores[9] + $scores[10] + $scores[11] + $scores[12] + $scores[13] + $scores[14]+ $scores[15];
		
		$sql = "UPDATE $table SET home_i1 = '$scores[0]', home_i2 = '$scores[1]', home_i3 = '$scores[2]', home_i4 = '$scores[3]', home_i5 = '$scores[4]', home_i6 = '$scores[5]', home_i7 = '$scores[6]', home_ex = '$scores[7]', home_total = '$hTotal', 
			away_i1 = '$scores[8]', away_i2 = '$scores[9]', away_i3 = '$scores[10]', away_i4 = '$scores[11]', away_i5 = '$scores[12]', away_i6 = '$scores[13]', away_i7 = '$scores[14]', away_ex = '$scores[15]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
	}

	
	/*
	#########################
	#						#
	#		STATS			#
	#						#
	#########################

	#########################
	#						#
	#		Soccer			#
	#						#
	#########################
	*/	
	
	if($table == "soccer"){		
		$stat = '';
			
		$playerID = getPlayerID($db, $team, $player, $sportID, $season);
		$assisterID = getPlayerID($db, $team, $assister, $sportID, $season);
		$defenseID = getPlayerID($db, $oppteam, $defense, $sportID, $season);			
		$goalieID = getPlayerID($db, $oppteam, $goalie, $sportID, $season);
			
		//Get player's stats
		$sql = "SELECT * FROM $tableStats AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
			
		if($query->rowCount() == 0  and $playerID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $tableStats (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
			
		//Get assister stats
		if($assisterID != 0){
			$sqls = "SELECT * FROM $tableStats AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$assisterID' AND s.id='$gameID'";
			$query = $db->prepare($sqls);
			$query->execute();
				
			if($query->rowCount() == 0 ){//Create player stats if doesn't exist
				$sql = "INSERT INTO $tableStats (player, game) VALUES ('$assisterID', '$gameID')";
				$query = $db->prepare($sql);
				$query->execute();
			}
		}
	
		//Get defense's stats
		if($defenseID != 0){
			$sqls = "SELECT * FROM $tableStats AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$defenseID' AND s.id='$gameID'";
			$query = $db->prepare($sqls);
			$query->execute();
				
			if($query->rowCount() == 0 ){//Create player stats if doesn't exist
				$sql = "INSERT INTO $tableStats (player, game) VALUES ('$defenseID', '$gameID')";
				$query = $db->prepare($sql);
				$query->execute();
			}
		}
			
		
		$sqls = "SELECT * FROM $tableStats AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$goalieID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $goalieID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $tableStats (player, game) VALUES ('$goalieID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
			
		if($action == "Goal scored by "){
			$stat = "goals = goals + 1, shots_on_goal = shots_on_goal + 1";
			
			if($goalieID != 0){
				$sqls = "UPDATE $tableStats SET goals_allowed = goals_allowed + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			
			if($assisterID != 0){
				$sqls = "UPDATE $tableStats SET assists = assists + 1 WHERE game='$gameID' AND player='$assisterID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "Assist by "){
			$stat = "assists = assists + 1";
		}else if($action == "Shot by "){
			$stat = "shots_on_goal = shots_on_goal + 1";

			if($goalieID != 0 and $defenseID == 0){
				$sqls = "UPDATE $tableStats SET saves = saves + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}else if($defenseID != 0){
				$sqls = "UPDATE $tableStats SET blocked_shots = blocked_shots + 1 WHERE game='$gameID' AND player='$defenseID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "Save by "){
			$stat = "saves = saves + 1";
		}else if($action == "Shot block by "){
			$stat = "blocked_shots = blocked_shots + 1";
		}else if($action == "Foul on "){
			$stat = "fouls = fouls + 1";
		}

		if($stat != ''){
			$sqls = "UPDATE $tableStats SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}
	}
	
	/*
	#########################
	#						#
	#		BOYS LAX		#
	#						#
	#########################
	*/	
			
	else if($table == "blax"){		
		$stat = '';
			
		$playerID = getPlayerID($db, $team, $player, $sportID, $season);
		$assisterID = getPlayerID($db, $team, $assister, $sportID, $season);
		$defenseID = getPlayerID($db, $oppteam, $defense, $sportID, $season);			
		$goalieID = getPlayerID($db, $oppteam, $goalie, $sportID, $season);
			
		//Get player's stats
		$sql = "SELECT * FROM $tableStats AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
			
		if($query->rowCount() == 0  and $playerID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $tableStats (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
			
		//Get assister stats
		if($assisterID != 0){
			$sqls = "SELECT * FROM $tableStats AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$assisterID' AND s.id='$gameID'";
			$query = $db->prepare($sqls);
			$query->execute();
				
			if($query->rowCount() == 0 ){//Create player stats if doesn't exist
				$sql = "INSERT INTO $tableStats (player, game) VALUES ('$assisterID', '$gameID')";
				$query = $db->prepare($sql);
				$query->execute();
			}
		}
	
		//Get defense's stats
		if($defenseID != 0){
			$sqls = "SELECT * FROM $tableStats AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$defenseID' AND s.id='$gameID'";
			$query = $db->prepare($sqls);
			$query->execute();
				
			if($query->rowCount() == 0 ){//Create player stats if doesn't exist
				$sql = "INSERT INTO $tableStats (player, game) VALUES ('$defenseID', '$gameID')";
				$query = $db->prepare($sql);
				$query->execute();
			}
		}
			
		
		$sqls = "SELECT * FROM $tableStats AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$goalieID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $goalieID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $tableStats (player, game) VALUES ('$goalieID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
			
		if($action == "Goal scored by "){
			$stat = "goals = goals + 1, shots_on_goal = shots_on_goal + 1";
			
			
			if($goalieID != 0){
				$sqls = "UPDATE $tableStats SET goals_allowed = goals_allowed + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($assisterID != 0){
				$sqls = "UPDATE $tableStats SET assists = assists + 1 WHERE game='$gameID' AND player='$assisterID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "Shot on goal by "){
			$stat = "shots_on_goal = shots_on_goal + 1";

			if($goalieID != 0){
				$sqls = "UPDATE $tableStats SET saves = saves + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "Shot by "){
			$stat = "shots = shots + 1";
		}else if($action == "Save by "){
			$stat = "saves = saves + 1";
		}else if($action == "Penalty on "){
			$stat = "fouls = fouls + 1";
		}else if($action == "Ball intercepted by "){
			$stat = "ground_balls = ground_balls + 1, forced_turnovers = forced_turnovers + 1";
		}else if($action == "Ground ball pickup by "){
			$stat = "ground_balls = ground_balls + 1";
		}else if($action == "Faceoff won by "){
			$stat = "faceoff_attempts = faceoff_attempts + 1, faceoff_wins = faceoff_wins + 1";

			if($defenseID != 0){
				$sqls = "UPDATE $tableStats SET faceoff_attempts = faceoff_attempts + 1 WHERE game='$gameID' AND player='$defenseID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "Turnover by "){
			$stat = "turnovers = turnovers + 1";
			
			if($defenseID != 0){
				$sqls = "UPDATE $tableStats SET forced_turnovers = forced_turnovers + 1 WHERE game='$gameID' AND player='$defenseID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}

		if($stat != ''){
			$sqls = "UPDATE $tableStats SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}		
	}
	$message = "Play Added to DB";
	
	$return = array("scores"=>$scores, "period"=>$period);
	echo json_encode($return);	
	
	
	
function getPlayerID($db, $team, $player, $sport, $season){
	
	$playerID = 0;
	//echo '<script>console.log('.$team. ", " . $player. ", " . $sport . ", " . $season.');</script>';
	$sql = "SELECT roster_player.id AS id FROM roster_player INNER JOIN roster_schools ON roster_player.school=roster_schools.id JOIN roster_teams AS sport ON sport.id=roster_player.team_id WHERE name='$player' AND team_id='$sport' AND roster_player.season='$season' AND roster_schools.short_name ='$team'";
	$query = $db->prepare($sql);
	$query->execute();
	
	while($row = $query->fetchObject()){
		$playerID = $row->id;
	}
	return $playerID;
}
?>