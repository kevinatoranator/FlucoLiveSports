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
			
			if($query->rowCount() == 0){//Create player stats if doesn't exist
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
			
			if($i == 0){//Updates player
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
	}
}else{
	//Sack/interception against QB
	if($statTable == "football_stats"){
		
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

			}
		}

		if($action == "Sack by "){
			$sacks_taken += 1;
			$stat = "sacks_taken = '$sacks_taken'";
		}else if($action == "Interception by "){
			$thrown_interceptions += 1;
			$pass_attempts += 1;
			$stat = "pass_attempts = '$pass_attempts', thrown_interceptions = '$thrown_interceptions'";
		}
		
		if($stat != ''){
			$sqls = "UPDATE $statTable SET $stat WHERE game='$gameID' AND player='$playerID'";
			$query = $db->prepare($sqls);
			$query->execute();
		}		
	}
}	

?>