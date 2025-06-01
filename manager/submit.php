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
	$assister = $infoArray[8];
	$defense = $infoArray[9];
	$batter = $infoArray[10];
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
	
	if($table == "soccer"){
		if($action == "Shot by " and $defense != ""){
			$pbp = "$team Shot by $player (Block by $defense)";
		}else if($action == "Shot by "){
			$pbp = "$team Shot by $player (Save by $goalie)";
		}else if($action == "Goal scored by " and $assister != ""){
			$pbp = "$team Goal scored by $player (Assisted by $assister)";
		}
	}else if($table == "glax" or $table == "blax"){
		if($action == "Shot on goal by "){
			$pbp = "$team Shot on goal by $player (Save by $goalie)";
		}else if($action == "Goal scored by " and $assister != ""){
			$pbp = "$team Goal scored by $player (Assisted by $assister)";
		}else if($action == "Turnover by "){
			$pbp = "$team Turnover by $player (Forced by $defense)";
		}else if($action == "Faceoff won by "){
			$pbp = "$team Faceoff won by $player vs. $defense";
		}
	}else if($table =="field_hockey"){
		if($action == "Shot on goal by " and $defense != ""){
			$pbp = "$team Shot on goal by $player (Block by $defense)";
		}else if($action == "Shot on goal by "){
			$pbp = "$team Shot on goal by $player (Save by $goalie)";
		}else if($action == "Goal scored by " and $assister != ""){
			$pbp = "$team Goal scored by $player (Assisted by $assister)";
		}
	}else if($table =="batball"){
		$pbp = "$team $player$action";
	}else if($table =="basketball"){
		if($action == "Timeout"){
			$pbp = "$action - $team";
		}else if($action == "Jump ball"){
			$pbp = "$action (Poss to $team)";
		}else if(($action == "Jumper by " or $action == "Layup by " or $action == "Dunk by " or $action == "3 Pointer by ") and $assister != ""){
			$pbp = "$team $action$player (Assisted by $assister)";
		}
	}
	
	$message = "";
	if($table == "soccer"){//half sport
		$sql = "INSERT INTO $tablePBP (text, half, time, game_id) VALUES ('$pbp', '$period', '$time', (SELECT id FROM schedule where id='$gameID'))";
	}else if($table == "batball"){//inning
		$gameType = SPORTTYPE::Inning;
		$sql = "INSERT INTO $tablePBP (text, inning, game_id) VALUES ('$pbp', '$period', (SELECT id FROM schedule where id='$gameID'))";
	}else if($table == "blax" or $table == "glax" or $table == "field_hockey" or $table == "basketball"){//quarter
	$gameType = SPORTTYPE::Quarter;
		$sql = "INSERT INTO $tablePBP (text, quarter, time, game_id) VALUES ('$pbp', '$period', '$time', (SELECT id FROM schedule where id='$gameID'))";
	}
	
	if($action != "pitch"){
		$query = $db->prepare($sql);
		$query->execute();
	}
	
	
	/*
	#########################
	#						#
	#		SCORING			#
	#						#
	#########################

	*/
	$points = 0;
	if($action == "Goal scored by " or $action == " scores" or $action == " homers"){
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
		}else if($gameType == SPORTTYPE::Inning){
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
				}else if($period == 6){
					$scores[5] += $points;
				}else if($period == 7){
					$scores[6] += $points;
				}else if($period == 8){
					$scores[7] += $points;
				}
				
			}else{
				if($period == 1){
					$scores[8] += $points;
				}else if($period == 2){
					$scores[9] += $points;
				}else if($period == 3){
					$scores[10] += $points;
				}else if($period == 4){
					$scores[11] += $points;
				}else if($period == 5){
					$scores[12] += $points;
				}else if($period == 6){
					$scores[13] += $points;
				}else if($period == 7){
					$scores[14] += $points;
				}else if($period == 8){
					$scores[15] += $points;
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
	}


	
	/*
	#########################
	#						#
	#		STATS			#
	#						#
	#########################
	
	*/
	
	$playerID = getPlayerID($db, $team, $player, $sportID, $season);
	$assisterID = getPlayerID($db, $team, $assister, $sportID, $season);
	$defenseID = getPlayerID($db, $oppteam, $defense, $sportID, $season);			
	$goalieID = getPlayerID($db, $oppteam, $goalie, $sportID, $season); //Also goalie
			
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
	
	/*

	#########################
	#						#
	#		Soccer			#
	#						#
	#########################
	*/
	
	
	if($table == "soccer"){		
		$stat = '';
			
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
	
	
	/*
	#########################
	#						#
	#		GIRLS LAX		#
	#						#
	#########################
	*/			
	else if($table == "glax"){		
		$stat = '';

			
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
			$stat = "shots_off_target = shots_off_target + 1";
		}else if($action == "Ground ball pickup by "){
			$stat = "ground_balls = ground_balls + 1";
		}else if($action == "Draw control by "){
			$stat = "draw_control = draw_control + 1";
		}else if($action == "Ball intercepted by "){
			$stat = "ground_balls = ground_balls + 1, forced_turnovers = forced_turnovers + 1";
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
	
	/*
	#########################
	#						#
	#	   FIELD HOCKEY		#
	#						#
	#########################
	*/	
	
	else if($table == "field_hockey"){		
		$stat = '';
			

			
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

			if($goalieID != 0 and $defenseID == 0){
				$sqls = "UPDATE $tableStats SET saves = saves + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}//else if($defenseID != 0){
			//	$sqls = "UPDATE $tableStats SET blocked_shots = blocked_shots + 1 WHERE game='$gameID' AND player='$defenseID'";
			//	$query = $db->prepare($sqls);
			//	$query->execute();
			//}
		}else if($action == "Shot by "){
			$stat = "shots = shots + 1";
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
	#	   BATBALL			#
	#						#
	#########################
	*/	
	
	
	
	else if($table == "batball"){		
		$stat = '';
		$batterID = getPlayerID($db, $team, $batter, $sportID, $season);
		
		$sqls = "SELECT * FROM $tableStats AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$batterID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $batterID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $tableStats (player, game) VALUES ('$batterID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
			
		if($action == " singles"){
			
			if($goalieID != 0){
				$sqls = "UPDATE $tableStats SET pitches = pitches + 1, hits_allowed = hits_allowed + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $tableStats SET hits = hits + 1, at_bats = at_bats + 1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " doubles"){
			
			if($goalieID != 0){
				$sqls = "UPDATE $tableStats SET pitches = pitches + 1, hits_allowed = hits_allowed + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $tableStats SET hits = hits + 1, at_bats = at_bats + 1, doubles = doubles + 1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " triples"){
			
			if($goalieID != 0){
				$sqls = "UPDATE $tableStats SET pitches = pitches + 1, hits_allowed = hits_allowed + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $tableStats SET hits = hits + 1, at_bats = at_bats + 1, triples = triples + 1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " homers"){
			
			if($goalieID != 0){
				$sqls = "UPDATE $tableStats SET pitches = pitches + 1, hits_allowed = hits_allowed + 1, runs_allowed = runs_allowed + 1, homeruns_allowed = homeruns_allowed + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $tableStats SET hits = hits + 1, at_bats = at_bats + 1, runs = runs + 1, homeruns = homeruns + 1, runs_batted_in = runs_batted_in +1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " strikes out looking" or $action == " strikes out swinging"){
			
			if($goalieID != 0){
				$innings_pitched = 0;
				$sqls = "SELECT innings_pitched AS innings_pitched FROM $tableStats WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
				while($row = $query->fetchObject()){
					$innings_pitched = $row->innings_pitched;
				}
				//echo $innings_pitched - floor($innings_pitched);
				if(intval($innings_pitched * 10) - intval(floor($innings_pitched) * 10) >= 2){
					$innings_pitched = floor($innings_pitched) + 1;
				}else{
					$innings_pitched += 0.1;
				}
				
				$sqls = "UPDATE $tableStats SET pitches = pitches + 1, strikeouts_given = strikeouts_given + 1, innings_pitched = '$innings_pitched' WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $tableStats SET strikeouts = strikeouts + 1, at_bats = at_bats + 1 WHERE game='$gameID' AND player='$playerID'";// could add individual lob as , left_on_base = left_on_base + '$lob'
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " flies out" or $action == " pops out" or $action == " grounds out" or $action == " lines out" or $action == " sacrifice fly" or $action == " sacrifice bunt"){
			
			if($goalieID != 0){
				$innings_pitched = 0;
				$sqls = "SELECT innings_pitched AS innings_pitched FROM $tableStats WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
				while($row = $query->fetchObject()){
					$innings_pitched = $row->innings_pitched;
				}
				//echo $innings_pitched - floor($innings_pitched);
				if(intval($innings_pitched * 10) - intval(floor($innings_pitched) * 10) >= 2){
					$innings_pitched = floor($innings_pitched) + 1;
				}else{
					$innings_pitched += 0.1;
				}
				
				$sqls = "UPDATE $tableStats SET pitches = pitches + 1, innings_pitched = '$innings_pitched' WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $tableStats SET at_bats = at_bats + 1 WHERE game='$gameID' AND player='$playerID'";// could add individual lob as , left_on_base = left_on_base + '$lob'
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " scores"){
			
			if($goalie != 0){
				$sqls = "UPDATE $tableStats SET runs_allowed = runs_allowed + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $tableStats SET runs = runs + 1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($batterID != 0){
				$sqls = "UPDATE $tableStats SET runs_batted_in = runs_batted_in + 1 WHERE game='$gameID' AND player='$batterID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			
			
		}else if($action == " walks"){
			
			if($goalieID != 0){
				$sqls = "UPDATE $tableStats SET pitches = pitches + 1, base_on_balls_allowed = base_on_balls_allowed + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $tableStats SET base_on_balls = base_on_balls + 1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "pitch"){
			
			if($goalieID != 0){
				$sqls = "UPDATE $tableStats SET pitches = pitches + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " out at first" or $action == " out at second" or $action == " out at third" or $action == " out at home"){
			
			if($goalieID != 0){
				$innings_pitched = 0;
				$sqls = "SELECT innings_pitched AS innings_pitched FROM $tableStats WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
				while($row = $query->fetchObject()){
					$innings_pitched = $row->innings_pitched;
				}
				//echo $innings_pitched - floor($innings_pitched);
				if(intval($innings_pitched * 10) - intval(floor($innings_pitched) * 10) >= 2){
					$innings_pitched = floor($innings_pitched) + 1;
				}else{
					$innings_pitched += 0.1;
				}
				
				$sqls = "UPDATE $tableStats SET innings_pitched = '$innings_pitched' WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}	
	}
	
	
	/*
	#########################
	#						#
	#	   BASKETBALL		#
	#						#
	#########################
	*/
	
	
	
	else if($table == "basketball"){
		$stat = '';
		
		
		if($action == "Jumper by " or $action == "Layup by " or $action == "Dunk by "){
			$stat = "field_goals_made = field_goals_made + 1, field_goals_attempted = field_goals_attempted + 1";
			if($assisterID != 0){
				$sqls = "UPDATE $tableStats SET assists = assists + 1 WHERE game='$gameID' AND player='$assisterID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "Jumper missed by " or $action == "Layup missed by " or $action == "Dunk missed by "){
			$stat = "field_goals_attempted = field_goals_attempted + 1";
		}else if($action == "3 Pointer by "){
			$stat = "threes_made = threes_made + 1, threes_attempted = threes_attempted + 1";
			if($assisterID != 0){
				$sqls = "UPDATE $tableStats SET assists = assists + 1 WHERE game='$gameID' AND player='$assisterID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "3 Pointer missed by "){
			$stat = "threes_attempted = threes_attempted + 1";
		}else if($action == "Free throw by "){
			$stat = "free_throws_made = free_throws_made + 1, free_throws_attempted = free_throws_attempted + 1";
		}else if($action == "Free throw missed by "){
			$stat = "free_throws_attempted = free_throws_attempted + 1";
		}else if($action == "Foul on "){
			$stat = "fouls = fouls + 1";
		}else if($action == "Turnover by "){
			$stat = "turnovers = turnovers + 1";
		}else if($action == "Steal by "){
			$stat = "steals = steals + 1";
		}else if($action == "Block by "){
			$stat = "blocks = blocks + 1";
		}else if($action == "Defensive rebound by " or $action == "Offensive rebound by "){
			$stat = "rebounds = rebounds + 1";
		}
		
		if($stat != ''){
			$sqls = "UPDATE $tableStats SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}	
	}	
		
	$message = "Play Added to DB";
	
	$return = array("scores"=>$scores, "period"=>$period, "action"=>$action);
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