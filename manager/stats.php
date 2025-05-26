<?php



/*
TODO

Needs to get:
Database -> $statTable
which stat to add

*/
	
if($team == "FCHS"){	
	if($statTable == "football_stats"){
		
		
		$updaters = [$player, $qb];
		
		for($i = 0; $i<2; $i++){
			$stat = '';
			
			$playerID =  '';
			$currentPlayer = $updaters[$i];
			
			//Get player's id
			$sqls = "SELECT p.id AS pid FROM roster_player AS p JOIN roster_teams AS sport ON sport.id=p.team_id WHERE name='$currentPlayer' AND sport.urlName='$sport' AND p.season='$season'";
			$query = $db->prepare($sqls);
			$query->execute();
			while($row = $query->fetchObject()){
				$playerID = $row->pid;
			}
			
			//Get player's stats
			$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
			$query = $db->prepare($sqls);
			$query->execute();
		
			//Rushing
			$carries = 0;
			$total_carry_yards = 0;
			$rushing_touchdowns = 0;
			$longest_carry = 0;
			//Passing
			$targets = 0;
			$receptions = 0;
			$total_reception_yards = 0;
			$reception_touchdowns = 0;
			$longest_reception = 0;
			//QB
			$pass_attempts = 0;
			$pass_completions = 0;
			$total_passing_yards = 0;
			$passing_touchdowns = 0;
			$thrown_interceptions = 0;
			$sacks_taken = 0;
			//Defense
			$sacks = 0;
			$tackle_for_loss = 0;
			$interceptions = 0;
			$forced_fumbles = 0;
			
			if($query->rowCount() == 0 and $playerID != null){//Create player stats if doesn't exist
				$sql = "INSERT INTO football_stats (player, game) VALUES ('$playerID', '$gameID')";
				$query = $db->prepare($sql);
				$query->execute();
			}else{//Otherwise pull stats
				while($row = $query->fetchObject()){
					//Rushing
					$carries = $row->carries;
					$total_carry_yards = $row->total_carry_yards;
					$rushing_touchdowns = $row->rushing_touchdowns;
					$longest_carry = $row->longest_carry;
					//Passing
					$targets = $row->targets;
					$receptions = $row->receptions;
					$total_reception_yards = $row->total_reception_yards;
					$reception_touchdowns = $row->reception_touchdowns;
					$longest_reception = $row->longest_reception;
					//QB
					$pass_attempts = $row->pass_attempts;
					$pass_completions = $row->pass_completions;
					$total_passing_yards = $row->total_passing_yards;
					$passing_touchdowns = $row->passing_touchdowns;
					$thrown_interceptions = $row->thrown_interceptions;
					$sacks_taken = $row->sacks_taken;
					//Defense
					$sacks = $row->sacks;
					$tackle_for_loss = $row->tackle_for_loss;
					$interceptions = $row->interceptions;
					$forced_fumbles = $row->forced_fumbles;
				}
			}
			
			if($i == 0 and $player != "FCHS"){//Updates player
				if($action == "Reception by " or $action == "Touchdown reception by "){
					$targets += 1;
					$receptions += 1;
					$total_reception_yards += $yards;
					if($yards > $longest_reception){
						$longest_reception = $yards;
					}
					if($action == "Touchdown reception by "){
						$reception_touchdowns += 1;
						$stat = "targets = '$targets', receptions='$receptions', total_reception_yards='$total_reception_yards', reception_touchdowns='$reception_touchdowns', longest_reception='$longest_reception'";
					}else{
						$stat = "targets = '$targets', receptions='$receptions', total_reception_yards='$total_reception_yards', longest_reception='$longest_reception'";
					}	
				}else if($action == "Incomplete pass to "){
					$targets += 1;
					$stat = "targets = '$targets'";
				}else if($action == "Run by " or $action == "Touchdown by "){
					$carries += 1;
					$total_carry_yards += $yards;
					if($yards > $longest_carry){
						$longest_carry = $yards;
					}
					if($action == "Touchdown by "){
						$rushing_touchdowns += 1;
						$stat = "carries = '$carries', total_carry_yards='$total_carry_yards', rushing_touchdowns='$rushing_touchdowns', longest_carry='$longest_carry'";
					}else{
						$stat = "carries = '$carries', total_carry_yards='$total_carry_yards', longest_carry='$longest_carry'";
					}
					
				}else if($action == "Sack by "){
					$sacks += 1;
					$stat = "sacks = '$sacks'";
				}else if($action == "Interception by "){
					$interceptions += 1;
					$stat = "interceptions = '$interceptions'";
				}
			}else{//Updates QB
				if($action == "Reception by " or $action == "Touchdown reception by "){
					$pass_attempts += 1;
					$pass_completions += 1;
					$total_passing_yards += $yards;
					if($action == "Touchdown reception by "){
						$passing_touchdowns += 1;
						$stat = "pass_attempts = '$pass_attempts', pass_completions='$pass_completions', total_passing_yards='$total_passing_yards', passing_touchdowns='$passing_touchdowns'";
					}else{
						$stat = "pass_attempts = '$pass_attempts', pass_completions='$pass_completions', total_passing_yards='$total_passing_yards'";
					}
				}else if($action == "Incomplete pass to "){
					$pass_attempts += 1;
					$stat = "pass_attempts = '$pass_attempts'";
				}
			}
			if($stat != ''){
				$sqls = "UPDATE $statTable SET $stat WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}	
		
	}else if($statTable == "field_hockey_stats"){		
		$stat = '';
			
		$playerID =  '';
			
		//Get player's id
		$sqls = "SELECT p.id AS pid FROM roster_player AS p JOIN roster_teams AS sport ON sport.id=p.team_id WHERE name='$player' AND sport.urlName='$sport' AND p.season='$season'";
		$query = $db->prepare($sqls);
		$query->execute();
		while($row = $query->fetchObject()){
			$playerID = $row->pid;
		}
			
		//Get player's stats
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
		
		//Stats
		$goals = 0;
		$assists = 0;
		$shots = 0;
		$shots_on_goal = 0;
		$saves = 0;
		$goals_allowed = 0;

			
		if($query->rowCount() == 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}else{//Otherwise pull stats
			while($row = $query->fetchObject()){
				$goals = $row->goals;
				$assists = $row->assists;
				$shots = $row->shots;
				$shots_on_goal = $row->shots_on_goal;
				$saves = $row->saves;
				$goals_allowed = $row->goals_allowed;
			}
		}
			
		if($action == "Goal scored by "){
			$goals += 1;
			$stat = "goals = '$goals'";
		}else if($action == "Assist by "){
			$assists += 1;
			$stat = "assists = '$assists'";
		}else if($action == "Shot by "){
			$shots += 1;
			$stat = "shots = '$shots'";
		}else if($action == "Shot on goal by "){
			$shots_on_goal += 1;
			$stat = "shots_on_goal = '$shots_on_goal'";
		}else if($action == "Save by "){
			$saves += 1;
			$stat = "saves = '$saves'";
		}

		if($stat != ''){
			$sqls = "UPDATE $statTable SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}		
	}else if($statTable == "volleyball_stats"){
		
		$stat = '';
			
		$playerID =  '';
			
		//Get player's id
		$sqls = "SELECT p.id AS pid FROM roster_player AS p JOIN roster_teams AS sport ON sport.id=p.team_id WHERE name='$player' AND sport.urlName='$sport' AND p.season='$season'";
		$query = $db->prepare($sqls);
		$query->execute();
		while($row = $query->fetchObject()){
			$playerID = $row->pid;
		}
			
		//Get player's stats
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
		
		//Stats
		$kills = 0;
		$assists = 0;
		$aces = 0;
		$attack_errors = 0;
		$service_errors = 0;
		$blocks = 0;
		$digs = 0;

			
		if($query->rowCount() == 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}else{//Otherwise pull stats
			while($row = $query->fetchObject()){
				$kills = $row->kills;
				$assists = $row->assists;
				$aces = $row->aces;
				$attack_errors = $row->attack_errors;
				$service_errors = $row->service_errors;
				$blocks = $row->blocks;
				$digs = $row->digs;
			}
		}
			
		if($action == "Kill by "){
			$kills += 1;
			$stat = "kills = '$kills'";
		}else if($action == "Assist by "){
			$assists += 1;
			$stat = "assists = '$assists'";
		}else if($action == "Service ace by "){
			$aces += 1;
			$stat = "aces = '$aces'";
		}else if($action == "Attack error by "){
			$attack_errors += 1;
			$stat = "attack_errors = '$attack_errors'";
		}else if($action == "Service error by "){
			$service_errors += 1;
			$stat = "service_errors = '$service_errors'";
		}else if($action == "Block by "){
			$blocks += 1;
			$stat = "blocks = '$blocks'";
		}

		if($stat != ''){
			$sqls = "UPDATE $statTable SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}		
	}else if($statTable == "basketball_stats"){
		$stat = '';
			
		$playerID =  '';
			
		//Get player's id
		$sqls = "SELECT p.id AS pid FROM roster_player AS p JOIN roster_teams AS sport ON sport.id=p.team_id WHERE name='$player' AND sport.urlName='$sport' AND p.season='$season'";
		$query = $db->prepare($sqls);
		$query->execute();
		while($row = $query->fetchObject()){
			$playerID = $row->pid;
		}
			
		//Get player's stats
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
		
		//Stats
		$field_goals_made = 0;
		$field_goals_attempted = 0;
		$threes_made = 0;
		$threes_attempted = 0;
		$free_throws_made = 0;
		$free_throws_attempted = 0;
		$rebounds = 0;
		$assists = 0;
		$steals = 0;
		$blocks = 0;
		$turnovers = 0;
		$fouls = 0;
		
		if($query->rowCount() == 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}else{//Otherwise pull stats
			while($row = $query->fetchObject()){
				$field_goals_made = $row->field_goals_made;
				$field_goals_attempted = $row->field_goals_attempted;
				$threes_made = $row->threes_made;
				$threes_attempted = $row->threes_attempted;
				$free_throws_made = $row->free_throws_made;
				$free_throws_attempted = $row->free_throws_attempted;
				$rebounds = $row->rebounds;
				$assists = $row->assists;
				$steals = $row->steals;
				$blocks = $row->blocks;
				$turnovers = $row->turnovers;
				$fouls = $row->fouls;
			}
		}
		
		if($action == "Jumper by " or $action == "Layup by " or $action == "Dunk by "){
			$field_goals_made += 1;
			$field_goals_attempted += 1;
			$stat = "field_goals_made = '$field_goals_made', field_goals_attempted = '$field_goals_attempted'";
		}else if($action == "Jumper missed by " or $action == "Layup missed by " or $action == "Dunk missed by "){
			$field_goals_attempted += 1;
			$stat = "field_goals_attempted = '$field_goals_attempted'";
		}else if($action == "3 Pointer by "){
			$threes_made += 1;
			$threes_attempted += 1;
			$stat = "threes_made = '$threes_made', threes_attempted = '$threes_attempted'";
		}else if($action == "3 Pointer missed by "){
			$threes_attempted += 1;
			$stat = "threes_attempted = '$threes_attempted'";
		}else if($action == "Free throw by "){
			$free_throws_made += 1;
			$free_throws_attempted += 1;
			$stat = "free_throws_made = '$free_throws_made', free_throws_attempted = '$free_throws_attempted'";
		}else if($action == "Free throw missed by "){
			$free_throws_attempted += 1;
			$stat = "free_throws_attempted = '$free_throws_attempted'";
		}else if($action == "Foul on "){
			$fouls += 1;
			$stat = "fouls = '$fouls'";
		}else if($action == "Turnover by "){
			$turnovers += 1;
			$stat = "turnovers = '$turnovers'";
		}else if($action == "Steal by "){
			$steals += 1;
			$stat = "steals = '$steals'";
		}else if($action == "Assist by "){
			$assists += 1;
			$stat = "assists = '$assists'";
		}else if($action == "Block by "){
			$blocks += 1;
			$stat = "blocks = '$blocks'";
		}else if($action == "Defensive rebound by " or $action == "Offensive rebound by "){
			$rebounds += 1;
			$stat = "rebounds = '$rebounds'";
		}
		
		if($stat != ''){
			$sqls = "UPDATE $statTable SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}	
	}
	
#Stats where away team is the main team
}else{
	//Sack/interception against QB
	if($statTable == "football_stats" and $qb != "FCHS" and $qb != "None"){
		
		$stat = '';
			
		$playerID =  '';
		$currentPlayer = $qb;
			
			//Get player's id
		$sqls = "SELECT p.id AS pid FROM roster_player AS p JOIN roster_teams AS sport ON sport.id=p.team_id WHERE name='$currentPlayer' AND sport.urlName='$sport' AND p.season='$season'";
		$query = $db->prepare($sqls);
		$query->execute();
		while($row = $query->fetchObject()){
			$playerID = $row->pid;
		}
			
		//Get player's stats
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();

		//QB
		$pass_attempts = 0;
		$pass_completions = 0;
		$total_passing_yards = 0;
		$passing_touchdowns = 0;
		$thrown_interceptions = 0;
		$sacks_taken = 0;
		
		
		//defense
		$tackle_for_loss = 0;
		$forced_fumbles = 0;

			
		if($query->rowCount() == 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO football_stats (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}else{//Otherwise pull stats
			while($row = $query->fetchObject()){

				//QB
				$pass_attempts = $row->pass_attempts;
				$pass_completions = $row->pass_completions;
				$total_passing_yards = $row->total_passing_yards;
				$passing_touchdowns = $row->passing_touchdowns;
				$thrown_interceptions = $row->thrown_interceptions;
				$sacks_taken = $row->sacks_taken;
				
				//defense
				$tackle_for_loss = $row->tackle_for_loss;
				$forced_fumbles = $row->forced_fumbles;

			}
		}

		if($action == "Sack by "){
			$sacks_taken += 1;
			$stat = "sacks_taken = '$sacks_taken'";
		}else if($action == "Interception by "){
			$thrown_interceptions += 1;
			$pass_attempts += 1;
			$stat = "pass_attempts = '$pass_attempts', thrown_interceptions = '$thrown_interceptions'";
		}else if($yards < 0 and ($action == "Run by " or $action == "Reception by ")){
			$tackle_for_loss += 1;
			$stat = "tackle_for_loss = '$tackle_for_loss'";
		}else if($action == "Fumble by "){
			$forced_fumbles += 1;
			$stat = "forced_fumbles = '$forced_fumbles'";
		}
		
		if($stat != ''){
			$sqls = "UPDATE $statTable SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}		
	}else if($statTable == "field_hockey_stats"){//saves/goals against
		$stat = '';
			
		$playerID =  '';
			
		//Get player's id
		$sqls = "SELECT p.id AS pid FROM roster_player AS p JOIN roster_teams AS sport ON sport.id=p.team_id WHERE name='$goalie' AND sport.urlName='$sport' AND p.season='$season'";
		$query = $db->prepare($sqls);
		$query->execute();
		while($row = $query->fetchObject()){
			$playerID = $row->pid;
		}
			
		//Get player's stats
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
		
		//Stats
		$saves = 0;
		$goals_allowed = 0;

			
		if($query->rowCount() == 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}else{//Otherwise pull stats
			while($row = $query->fetchObject()){
				$saves = $row->saves;
				$goals_allowed = $row->goals_allowed;
			}
		}
			
		if($action == "Goal scored by "){
			$goals_allowed += 1;
			$stat = "goals_allowed = '$goals_allowed'";
		}else if($action == "Shot on goal by "){
			$saves += 1;
			$stat = "saves = '$saves'";
		}

		if($stat != ''){
			$sqls = "UPDATE $statTable SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}
	}
}	

//TEAM STATS

if($statTable == "football_stats"){
	//Get team's stats
		$sqls = "SELECT team.id AS teamID FROM roster_schools AS school WHERE team.short_name='$team'";
		$query = $db->prepare($sqls);
		$query->execute();
		while($row = $query->fetchObject()){
			$teamID = $row->teamID;
		}
	
		$teamTable = $statTable . "_team";
		$sqls = "SELECT * FROM $teamTable AS teamstat JOIN roster_schools AS team ON teamstat.player=t.id JOIN schedule AS sched ON teamstat.game=sched.id WHERE team.id='$teamID' AND sched.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
		
		$total_plays = 0;
		$total_drives = 0;
		$avg_start = 0;
		$poss_time = 0;
		$pass_first_down = 0;
		$rush_first_down = 0;
		$pen_first_down = 0;
		$third_down_att = 0;
		$fourth_down_att = 0;
		$third_down_conv = 0;
		$fourth_down_conv = 0;
		$total_passing_yards_team = 0;
		$pass_completions_team = 0;
		$pass_attempts_team = 0;
		$thrown_interceptions_team = 0;
		$sacks_taken_team = 0;
		$sack_yards_lost = 0;
		$total_rushing_yards = 0;
		$carries_team = 0;
		$red_zone_conv = 0;
		$penalties = 0;
		$penalty_yards = 0;
		$fumbles = 0;
		$interceptions_team = 0;
		$defense_tds = 0;
		
		$stat = '';
		
		if($query->rowCount() == 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO football_stats_team (team, game) VALUES ('$teamID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}else{//Otherwise pull stats
			while($row = $query->fetchObject()){
				$total_plays = $row->total_plays;
				$total_drives = $row->total_drives;
				$avg_start = $row->avg_start;
				$poss_time = $row->poss_time;
				$pass_first_down = $row->pass_first_down;
				$rush_first_down = $row->rush_first_down;
				$pen_first_down = $row->pen_first_down;
				$third_down_att = $row->third_down_att;
				$fourth_down_att = $row->fourth_down_att;
				$third_down_conv = $row->third_down_conv;
				$fourth_down_conv = $row->fourth_down_conv;
				$total_passing_yards_team = $row->total_passing_yards;
				$pass_completions_team = $row->pass_completions;
				$pass_attempts_team = $row->pass_attempts_team;
				$thrown_interceptions_team = $row->thrown_interceptions;
				$sacks_taken_team = $row->sacks_taken;
				$sack_yards_lost = $row->sack_yards_lost;
				$total_rushing_yards = $row->total_rushing_yards;
				$carries_team = $row->carries;
				$red_zone_conv = $row->red_zone_conv;
				$penalties = $row->penalties;
				$penalty_yards = $row->penalty_yards;
				$fumbles = $row->fumbles;
				$interceptions_team = $row->interceptions;
				$defense_tds = $row->defense_tds;
			}
		}
		
		if($stat != ''){
			$sqls = "UPDATE $teamTable SET $stat WHERE game='$gameID' AND team='$teamID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}	

}else if($statTable == "soccer_stats"){		
		$stat = '';
			
		$playerID = getPlayerID($db, $homeTeam, $hgoalie, $sportID, $season);//TEMP FIX =TO INSERT GOALISES
			
		//Get player's stats
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $playerID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
		
		$assisterID = getPlayerID($db, $team, $assister, $sportID, $season);//
			
		//Get player's stats
		if($assisterID != 0){
			$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$assisterID' AND s.id='$gameID'";
			$query = $db->prepare($sqls);
			$query->execute();
				
			if($query->rowCount() == 0 ){//Create player stats if doesn't exist
				$sql = "INSERT INTO $statTable (player, game) VALUES ('$assisterID', '$gameID')";
				$query = $db->prepare($sql);
				$query->execute();
			}
		}
		
		if($team == $homeTeam){
				$defenseID = getPlayerID($db, $awayTeam, $defense, $sportID, $season);			
		}else{
				$defenseID = getPlayerID($db, $homeTeam, $defense, $sportID, $season);
		}
		
		//Get player's stats
		if($defenseID != 0){
			$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$defenseID' AND s.id='$gameID'";
			$query = $db->prepare($sqls);
			$query->execute();
				
			if($query->rowCount() == 0 ){//Create player stats if doesn't exist
				$sql = "INSERT INTO $statTable (player, game) VALUES ('$defenseID', '$gameID')";
				$query = $db->prepare($sql);
				$query->execute();
			}
		}
		$playerID = getPlayerID($db, $awayTeam, $agoalie, $sportID, $season);
		
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $playerID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
		
		$playerID = getPlayerID($db, $team, $player, $sportID, $season);
		
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $playerID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
			
		if($action == "Goal scored by "){
			$stat = "goals = goals + 1, shots_on_goal = shots_on_goal + 1";
			
			if($team == $homeTeam){
				$goalieID = getPlayerID($db, $awayTeam, $agoalie, $sportID, $season);			
			}else{
				$goalieID = getPlayerID($db, $homeTeam, $hgoalie, $sportID, $season);
			}
			if($goalieID != 0){
				$sqls = "UPDATE $statTable SET goals_allowed = goals_allowed + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			
			if($assisterID != 0){
				$sqls = "UPDATE $statTable SET assists = assists + 1 WHERE game='$gameID' AND player='$assisterID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "Assist by "){
			$stat = "assists = assists + 1";
		}else if($action == "Shot by "){
			$stat = "shots_on_goal = shots_on_goal + 1";
			if($team == $homeTeam){
				$goalieID = getPlayerID($db, $awayTeam, $agoalie, $sportID, $season);			
			}else{
				$goalieID = getPlayerID($db, $homeTeam, $hgoalie, $sportID, $season);
			}
			if($goalieID != 0 and $defenseID == 0){
				$sqls = "UPDATE $statTable SET saves = saves + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}else if($defenseID != 0){
				$sqls = "UPDATE $statTable SET blocked_shots = blocked_shots + 1 WHERE game='$gameID' AND player='$defenseID'";
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
			$sqls = "UPDATE $statTable SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}


	/*
	#########################
	#						#
	#		GIRLS LAX		#
	#						#
	#########################
	*/			
	}else if($statTable == "glax_stats"){		
		$stat = '';
			
		$hgoalieID = getPlayerID($db, $homeTeam, $hgoalie, $sportID, $season);
			
		//Get player's stats
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$hgoalieID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0 and $hgoalieID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$hgoalieID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
		
		$assisterID = getPlayerID($db, $team, $assister, $sportID, $season);
			
		//Get player's stats
		if($assisterID != 0){
			$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$assisterID' AND s.id='$gameID'";
			$query = $db->prepare($sqls);
			$query->execute();
				
			if($query->rowCount() == 0){//Create player stats if doesn't exist
				$sql = "INSERT INTO $statTable (player, game) VALUES ('$assisterID', '$gameID')";
				$query = $db->prepare($sql);
				$query->execute();
			}
		}
		$agoalieID = getPlayerID($db, $awayTeam, $agoalie, $sportID, $season);
		
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$agoalieID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $agoalieID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$agoalieID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
		
		$playerID = getPlayerID($db, $team, $player, $sportID, $season);
		
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $playerID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
		
		$defenseID = getPlayerID($db, $team, $defense, $sportID, $season);
		
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$defenseID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $defenseID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$defenseID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
			
		if($action == "Goal scored by "){
			$stat = "goals = goals + 1, shots_on_goal = shots_on_goal + 1";
			
			if($team == $homeTeam){
				$goalieID = getPlayerID($db, $awayTeam, $agoalie, $sportID, $season);			
			}else{
				$goalieID = getPlayerID($db, $homeTeam, $hgoalie, $sportID, $season);
			}
			if($goalieID != 0){
				$sqls = "UPDATE $statTable SET goals_allowed = goals_allowed + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($assisterID != 0){
				$sqls = "UPDATE $statTable SET assists = assists + 1 WHERE game='$gameID' AND player='$assisterID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "Assist by "){
			$stat = "assists = assists + 1";
		}else if($action == "Shot on goal by "){
			$stat = "shots_on_goal = shots_on_goal + 1";
			if($team == $homeTeam){
				$goalieID = getPlayerID($db, $awayTeam, $agoalie, $sportID, $season);			
			}else{
				$goalieID = getPlayerID($db, $homeTeam, $hgoalie, $sportID, $season);
			}
			if($goalieID != 0){
				$sqls = "UPDATE $statTable SET saves = saves + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "Shot by "){
			$stat = "shots_off_target = shots_off_target + 1";
		}else if($action == "Save by "){
			$stat = "saves = saves + 1";
		}else if($action == "Ground ball pickup by "){
			$stat = "ground_balls = ground_balls + 1";
		}else if($action == "Draw control by "){
			$stat = "draw_control = draw_control + 1";
		}else if($action == "Ball intercepted by "){
			$stat = "ground_balls = ground_balls + 1, forced_turnovers = forced_turnovers + 1";
		}else if($action == "Turnover by "){
			$stat = "turnovers = turnovers + 1";
			
			if($team == $homeTeam){
				$defenseID = getPlayerID($db, $awayTeam, $defense, $sportID, $season);			
			}else{
				$defenseID = getPlayerID($db, $homeTeam, $defense, $sportID, $season);
			}
			if($defenseID != 0){
				$sqls = "UPDATE $statTable SET forced_turnovers = forced_turnovers + 1 WHERE game='$gameID' AND player='$defenseID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}

		if($stat != ''){
			$sqls = "UPDATE $statTable SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}		
		
		
	
	/*
	#########################
	#						#
	#		BOYS LAX		#
	#						#
	#########################
	*/	
		
		
		
	}else if($statTable == "blax_stats"){		
		$stat = '';
			
		$hgoalieID = getPlayerID($db, $homeTeam, $hgoalie, $sportID, $season);
			
		//Get player's stats
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$hgoalieID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0 and $hgoalieID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$hgoalieID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
		
		$assisterID = getPlayerID($db, $team, $assister, $sportID, $season);//
			
		//Get player's stats
		if($assisterID != 0){
			$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$assisterID' AND s.id='$gameID'";
			$query = $db->prepare($sqls);
			$query->execute();
				
			if($query->rowCount() == 0){//Create player stats if doesn't exist
				$sql = "INSERT INTO $statTable (player, game) VALUES ('$assisterID', '$gameID')";
				$query = $db->prepare($sql);
				$query->execute();
			}
		}
		$agoalieID = getPlayerID($db, $awayTeam, $agoalie, $sportID, $season);
		
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$agoalieID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $agoalieID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$agoalieID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
		
		$playerID = getPlayerID($db, $team, $player, $sportID, $season);
		
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $playerID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
		
		$defenseID = getPlayerID($db, $team, $defense, $sportID, $season);
		
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$defenseID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $defenseID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$defenseID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
			
		if($action == "Goal scored by "){
			$stat = "goals = goals + 1, shots_on_goal = shots_on_goal + 1";
			
			if($team == $homeTeam){
				$goalieID = getPlayerID($db, $awayTeam, $agoalie, $sportID, $season);			
			}else{
				$goalieID = getPlayerID($db, $homeTeam, $hgoalie, $sportID, $season);
			}
			if($goalieID != 0){
				$sqls = "UPDATE $statTable SET goals_allowed = goals_allowed + 1 WHERE game='$gameID' AND player='$goalieID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($assisterID != 0){
				$sqls = "UPDATE $statTable SET assists = assists + 1 WHERE game='$gameID' AND player='$assisterID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "Assist by "){
			$stat = "assists = assists + 1";
		}else if($action == "Shot on goal by "){
			$stat = "shots_on_goal = shots_on_goal + 1";
			if($team == $homeTeam){
				$goalieID = getPlayerID($db, $awayTeam, $agoalie, $sportID, $season);			
			}else{
				$goalieID = getPlayerID($db, $homeTeam, $hgoalie, $sportID, $season);
			}
			if($goalieID != 0){
				$sqls = "UPDATE $statTable SET saves = saves + 1 WHERE game='$gameID' AND player='$goalieID'";
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
			if($team == $homeTeam){
				$defenseID = getPlayerID($db, $awayTeam, $defense, $sportID, $season);			
			}else{
				$defenseID = getPlayerID($db, $homeTeam, $defense, $sportID, $season);
			}
			if($defenseID != 0){
				$sqls = "UPDATE $statTable SET faceoff_attempts = faceoff_attempts + 1 WHERE game='$gameID' AND player='$defenseID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == "Turnover by "){
			$stat = "turnovers = turnovers + 1";
			
			if($team == $homeTeam){
				$defenseID = getPlayerID($db, $awayTeam, $defense, $sportID, $season);			
			}else{
				$defenseID = getPlayerID($db, $homeTeam, $defense, $sportID, $season);
			}
			if($defenseID != 0){
				$sqls = "UPDATE $statTable SET forced_turnovers = forced_turnovers + 1 WHERE game='$gameID' AND player='$defenseID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}

		if($stat != ''){
			$sqls = "UPDATE $statTable SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}		
	}else if($statTable == "batball_stats"){		
		$stat = '';
			
		$hpitcherID = getPlayerID($db, $homeTeam, $hpitcher, $sportID, $season);//TEMP FIX =TO INSERT GOALISES
			
		//Get player's stats
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$hpitcherID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0 and $hpitcherID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$hpitcherID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
		
		$apitcherID = getPlayerID($db, $awayTeam, $apitcher, $sportID, $season);//
			
		//Get player's stats
		if($apitcherID != 0){
			$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$apitcherID' AND s.id='$gameID'";
			$query = $db->prepare($sqls);
			$query->execute();
				
			if($query->rowCount() == 0){//Create player stats if doesn't exist
				$sql = "INSERT INTO $statTable (player, game) VALUES ('$apitcherID', '$gameID')";
				$query = $db->prepare($sql);
				$query->execute();
			}
		}
		
		$playerID = getPlayerID($db, $team, $player, $sportID, $season);
		
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$playerID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $playerID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$playerID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
		$batterID = getPlayerID($db, $team, $batter, $sportID, $season);
		
		$sqls = "SELECT * FROM $statTable AS stat JOIN roster_player AS p ON stat.player=p.id JOIN schedule AS s ON stat.game=s.id WHERE p.id='$batterID' AND s.id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();
			
		if($query->rowCount() == 0  and $batterID != 0){//Create player stats if doesn't exist
			$sql = "INSERT INTO $statTable (player, game) VALUES ('$batterID', '$gameID')";
			$query = $db->prepare($sql);
			$query->execute();
		}
		
		if($team == $homeTeam){
			$pitcher = $apitcherID;			
		}else{
			$pitcher = $hpitcherID;		
		}
			
		if($action == " singles"){
			
			if($pitcher != 0){
				$sqls = "UPDATE $statTable SET pitches = pitches + 1, hits_allowed = hits_allowed + 1 WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $statTable SET hits = hits + 1, at_bats = at_bats + 1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " doubles"){
			
			if($pitcher != 0){
				$sqls = "UPDATE $statTable SET pitches = pitches + 1, hits_allowed = hits_allowed + 1 WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $statTable SET hits = hits + 1, at_bats = at_bats + 1, doubles = doubles + 1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " triples"){
			
			if($pitcher != 0){
				$sqls = "UPDATE $statTable SET pitches = pitches + 1, hits_allowed = hits_allowed + 1 WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $statTable SET hits = hits + 1, at_bats = at_bats + 1, triples = triples + 1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " homers"){
			
			if($pitcher != 0){
				$sqls = "UPDATE $statTable SET pitches = pitches + 1, hits_allowed = hits_allowed + 1, runs_allowed = runs_allowed + 1, homeruns_allowed = homeruns_allowed + 1 WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $statTable SET hits = hits + 1, at_bats = at_bats + 1, runs = runs + 1, homeruns = homeruns + 1, runs_batted_in = runs_batted_in +1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " strikes out looking" or $action == " strikes out swinging"){
			
			if($pitcher != 0){
				$innings_pitched = 0;
				$sqls = "SELECT innings_pitched AS innings_pitched FROM $statTable WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
				while($row = $query->fetchObject()){
					$innings_pitched = $row->innings_pitched;
				}
				//echo $innings_pitched - floor($innings_pitched);
				if($innings_pitched - floor($innings_pitched) >= 0.2){
					$innings_pitched = floor($innings_pitched) + 1;
				}else{
					$innings_pitched += 0.1;
				}
				
				$sqls = "UPDATE $statTable SET pitches = pitches + 1, strikeouts_given = strikeouts_given + 1, innings_pitched = '$innings_pitched' WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $statTable SET strikeouts = strikeouts + 1, at_bats = at_bats + 1 WHERE game='$gameID' AND player='$playerID'";// could add individual lob as , left_on_base = left_on_base + '$lob'
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " flies out" or $action == " pops out" or $action == " grounds out" or $action == " lines out" or $action == " sacrifice fly" or $action == " sacrifice bunt"){
			
			if($pitcher != 0){
				$innings_pitched = 0;
				$sqls = "SELECT innings_pitched AS innings_pitched FROM $statTable WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
				while($row = $query->fetchObject()){
					$innings_pitched = $row->innings_pitched;
				}
				//echo $innings_pitched - floor($innings_pitched);
				if($innings_pitched - floor($innings_pitched) >= 0.2){
					$innings_pitched = floor($innings_pitched) + 1;
				}else{
					$innings_pitched += 0.1;
				}
				
				$sqls = "UPDATE $statTable SET pitches = pitches + 1, innings_pitched = '$innings_pitched' WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $statTable SET at_bats = at_bats + 1 WHERE game='$gameID' AND player='$playerID'";// could add individual lob as , left_on_base = left_on_base + '$lob'
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " scores"){
			
			if($pitcher != 0){
				$sqls = "UPDATE $statTable SET runs_allowed = runs_allowed + 1 WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $statTable SET runs = runs + 1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($batterID != 0){
				$sqls = "UPDATE $statTable SET runs_batted_in = runs_batted_in + 1 WHERE game='$gameID' AND player='$batterID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			
			
		}else if($action == " walks"){
			
			if($pitcher != 0){
				$sqls = "UPDATE $statTable SET pitches = pitches + 1, base_on_balls_allowed = base_on_balls_allowed + 1 WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
			if($playerID != 0){
				$sqls = "UPDATE $statTable SET base_on_balls = base_on_balls + 1 WHERE game='$gameID' AND player='$playerID'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " strike" or $action == " ball" or $action == " foul"){
			
			if($pitcher != 0){
				$sqls = "UPDATE $statTable SET pitches = pitches + 1 WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}else if($action == " out at first" or $action == " out at second" or $action == " out at third" or $action == " out at home"){
			
			if($pitcher != 0){
				$innings_pitched = 0;
				$sqls = "SELECT innings_pitched AS innings_pitched FROM $statTable WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
				while($row = $query->fetchObject()){
					$innings_pitched = $row->innings_pitched;
				}
				//echo $innings_pitched - floor($innings_pitched);
				if($innings_pitched - floor($innings_pitched) >= 0.2){
					$innings_pitched = floor($innings_pitched) + 1;
				}else{
					$innings_pitched += 0.1;
				}
				
				$sqls = "UPDATE $statTable SET innings_pitched = '$innings_pitched' WHERE game='$gameID' AND player='$pitcher'";
				$query = $db->prepare($sqls);
				$query->execute();
			}
		}

		/*if($stat != ''){
			$sqls = "UPDATE $statTable SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}	*/	
	}

?>