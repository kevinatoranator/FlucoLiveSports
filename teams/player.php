<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../stylesheet.css">
</head>
<body>
<script>
	function rtoggle(id){
		var checkBox = document.getElementById(id);
		var displayr = document.getElementsByClassName(id)[0];
		if(displayr.classList.contains("hidden")){
			displayr.classList.remove("hidden")
		}else{
			displayr.classList.add("hidden")
		}
	}
</script>
<?php
	

	include '../include/database.php';
	
	$playerName = $_GET['player'];
	$school = $_GET['school'];
	
?>


<!--Schedule Header-->

    <br>
    <?php 
	include '../include/header.php';
	
	printf("<br><center><b>%s</b></center><br><br>", $playerName);
	$seasonsPlayed = [];
	
	$sql = "SELECT roster_teams.formattedName AS team, roster_teams.urlName AS url, roster_player.season AS year FROM roster_player INNER JOIN roster_teams ON roster_player.team_id=roster_teams.id 
	INNER JOIN roster_schools ON roster_player.school=roster_schools.id WHERE roster_player.name='$playerName' and roster_schools.short_name='$school'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$team = $row->team;
		$year = $row->year;
		$url = $row->url;

		
		array_push($seasonsPlayed, array($team, $year, $url));
		//print_r($seasonsPlayed);	
	}
	
	$seasonArray = array();
	foreach($seasonsPlayed as $sportSeason){
		$statdb = "";
		if($sportSeason[0] == "Boys JV Lacrosse" or $sportSeason[0] == "Boys Varsity Lacrosse"){
			$statdb = "blax_stats";
		}else if($sportSeason[0] == "Girls JV Lacrosse" or $sportSeason[0] == "Girls Varsity Lacrosse"){
			$statdb = "glax_stats";
		}else if($sportSeason[0] == "Boys JV Soccer" or $sportSeason[0] == "Boys Varsity Soccer" or $sportSeason[0] == "Girls JV Soccer" or $sportSeason[0] == "Girls Varsity Soccer"){
			$statdb = "soccer_stats";
		}else if($sportSeason[0] == "JV Baseball" or $sportSeason[0] == "Varsity Baseball" or $sportSeason[0] == "JV Softball" or $sportSeason[0] == "Varsity Softball"){
			$statdb = "batball_stats";
		}else if($sportSeason[0] == "jvfhockey" or $sportSeason[0] == "fhockey"){
			$statdb = "field_hockey";
		}else if($sportSeason[0] == "JV Football" or $sportSeason[0] == "Varsity Football"){
			$statdb = "football_stats";
		}else if($sportSeason[0] == "jvbbball" or $sportSeason[0] == "bbball" or $sportSeason[0] == "jvgbball" or $sportSeason[0] == "gbball"){
			$statdb = "basketball";
		}else if($sportSeason[0] == "Girls JV Volleyball" or $sportSeason[0] == "Girls Varsity Volleyball"){
			$statdb = "volleyball_stats";
		}
		if($statdb == "volleyball_stats"){
			$seasonArray[] = ["<a href = './roster.php?school=$school&sport=$sportSeason[2]' class='schedule-game'>$sportSeason[0]($sportSeason[1])</a>", "Kills", "Assts", "Aces", "A/ERR", "S/ERR", "Blocks", "Digs"];
			$sql = "SELECT kills, assists, aces, attack_errors, service_errors, blocks, digs, schedule.game_date AS date_, schedule.id AS gameID FROM $statdb 
			INNER JOIN roster_player ON roster_player.id=$statdb.player INNER JOIN schedule ON schedule.id=$statdb.game INNER JOIN roster_teams AS sport ON schedule.team_id=sport.id 
			WHERE roster_player.name='$playerName' AND schedule.season='$sportSeason[1]' AND sport.formattedName='$sportSeason[0]'";
			$query = $db->prepare($sql);
			$query->execute();
			while($row = $query->fetchObject()){
				$gameID = $row->gameID;
				$dateURL = "<a href='../game/vball.php?gameID=$gameID' class='schedule-game'>" . $row->date_ . "</a>";
				$seasonArray[] = [$dateURL, $row->kills, $row->assists, $row->aces, $row->attack_errors, $row->service_errors, $row->blocks, $row->digs];
				//printf("%s: %s<br>", $game, $row->kills);		
			}
			
		}else if($statdb == "football_stats"){
			$seasonArray[] = ["<a href = './roster.php?school=$school&sport=$sportSeason[2]' class='schedule-game'>$sportSeason[0]($sportSeason[1])</a>", "CAR", "YDS", "REC", "YDS", "TD", "SACKS", "INT"];

			$sql = "SELECT carries, total_carry_yards, receptions, total_reception_yards, rushing_touchdowns, reception_touchdowns, sacks, interceptions, schedule.game_date AS date_, schedule.id AS gameID FROM $statdb 
			INNER JOIN roster_player ON roster_player.id=$statdb.player INNER JOIN schedule ON schedule.id=$statdb.game INNER JOIN roster_teams AS sport ON schedule.team_id=sport.id 
			WHERE roster_player.name='$playerName' AND schedule.season='$sportSeason[1]' AND sport.formattedName='$sportSeason[0]'";
			$query = $db->prepare($sql);
			$query->execute();
			while($row = $query->fetchObject()){
				$gameID = $row->gameID;
				$dateURL = "<a href='../game/football.php?gameID=$gameID' class='schedule-game'>" . $row->date_ . "</a>";
				$seasonArray[] = [$dateURL, $row->carries, $row->total_carry_yards, $row->receptions, $row->total_reception_yards, $row->rushing_touchdowns+$row->reception_touchdowns, $row->sacks, $row->interceptions];
				//printf("%s: %s<br>", $game, $row->kills);		
			}
			
		}else if($statdb == "glax_stats"){
			$seasonArray[] = ["<a href = './roster.php?school=$school&sport=$sportSeason[2]' class='schedule-game'>$sportSeason[0]($sportSeason[1])</a>", "Goals", "Assts", "GB", "DC", "SOff", "SOG", "FTO", "TO"];

			$sql = "SELECT goals, assists, ground_balls, draw_control, forced_turnovers, shots_off_target, shots_on_goal, turnovers, schedule.game_date AS date_, schedule.id AS gameID FROM $statdb 
			INNER JOIN roster_player ON roster_player.id=$statdb.player INNER JOIN schedule ON schedule.id=$statdb.game INNER JOIN roster_teams AS r ON schedule.team_id=r.id WHERE roster_player.name='$playerName' AND schedule.season='$sportSeason[1]' AND r.formattedName='$sportSeason[0]'";
			$query = $db->prepare($sql);
			$query->execute();
			while($row = $query->fetchObject()){
				$gameID = $row->gameID;
				$dateURL = "<a href='../game/glax.php?gameID=$gameID' class='schedule-game'>" . $row->date_ . "</a>";
				$seasonArray[] = [$dateURL, $row->goals, $row->assists, $row->ground_balls, $row->draw_control, $row->shots_off_target, $row->shots_on_goal, $row->forced_turnovers, $row->turnovers];
				//printf("%s: %s<br>", $game, $row->kills);		
			}
			
		}else if($statdb == "blax_stats"){
			$seasonArray[] = ["<a href = './roster.php?school=$school&sport=$sportSeason[2]' class='schedule-game'>$sportSeason[0]($sportSeason[1])</a>", "Goals", "Assts", "GB", "FO", "S", "SOG", "TO", "CT", "P"];

			$sql = "SELECT goals, assists, ground_balls, faceoff_attempts, faceoff_wins, shots, shots_on_goal, turnovers, forced_turnovers, fouls, schedule.game_date AS date_, schedule.id AS gameID FROM $statdb 
			INNER JOIN roster_player ON roster_player.id=$statdb.player INNER JOIN schedule ON schedule.id=$statdb.game INNER JOIN roster_teams AS r ON schedule.team_id=r.id WHERE roster_player.name='$playerName' AND schedule.season='$sportSeason[1]' AND r.formattedName='$sportSeason[0]'";
			$query = $db->prepare($sql);
			$query->execute();
			while($row = $query->fetchObject()){
				$gameID = $row->gameID;
				$dateURL = "<a href='../game/blax.php?gameID=$gameID' class='schedule-game'>" . $row->date_ . "</a>";
				$seasonArray[] = [$dateURL, $row->goals, $row->assists, $row->ground_balls, $row->faceoff_wins . "/" . $row->faceoff_attempts, $row->shots, $row->shots_on_goal, $row->turnovers, $row->forced_turnovers, $row->fouls];
				//printf("%s: %s<br>", $game, $row->kills);		
			}
			
		}else if($statdb == "batball_stats"){
			$seasonArray[] = ["<a href = './roster.php?school=$school&sport=$sportSeason[2]' class='schedule-game'>$sportSeason[0]($sportSeason[1])</a>", "H", "AB", "R", "RBI", "BB", "K", "2B", "3B", "HR"];

			$sql = "SELECT hits, at_bats, runs, runs_batted_in, base_on_balls, strikeouts, doubles, triples, homeruns, schedule.game_date AS date_, schedule.id AS gameID FROM $statdb 
			INNER JOIN roster_player ON roster_player.id=$statdb.player INNER JOIN schedule ON schedule.id=$statdb.game INNER JOIN roster_teams AS r ON schedule.team_id=r.id WHERE roster_player.name='$playerName' AND schedule.season='$sportSeason[1]' AND r.formattedName='$sportSeason[0]'";
			$query = $db->prepare($sql);
			$query->execute();
			while($row = $query->fetchObject()){
				$gameID = $row->gameID;
				$dateURL = "<a href='../game/batball.php?gameID=$gameID' class='schedule-game'>" . $row->date_ . "</a>";
				$seasonArray[] = [$dateURL, $row->hits, $row->at_bats, $row->runs, $row->runs_batted_in, $row->base_on_balls, $row->strikeouts, $row->doubles, $row->triples, $row->homeruns];
				//printf("%s: %s<br>", $game, $row->kills);		
			}
			
		}else if($statdb == "soccer_stats"){
			$seasonArray[] = ["<a href = './roster.php?school=$school&sport=$sportSeason[2]' class='schedule-game'>$sportSeason[0]($sportSeason[1])</a>", "Goals", "Assts", "SOG", "Blocks", "Fouls"];

			$sql = "SELECT goals, assists, shots_on_goal, blocked_shots, fouls, schedule.game_date AS date_, schedule.id AS gameID FROM $statdb 
			INNER JOIN roster_player ON roster_player.id=$statdb.player INNER JOIN schedule ON schedule.id=$statdb.game INNER JOIN roster_teams AS r ON schedule.team_id=r.id WHERE roster_player.name='$playerName' AND schedule.season='$sportSeason[1]' AND r.formattedName='$sportSeason[0]'";
			$query = $db->prepare($sql);
			$query->execute();
			while($row = $query->fetchObject()){
				$gameID = $row->gameID;
				$dateURL = "<a href='../game/soccer.php?gameID=$gameID' class='schedule-game'>" . $row->date_ . "</a>";
				$seasonArray[] = [$dateURL, $row->goals, $row->assists, $row->shots_on_goal, $row->blocked_shots, $row->fouls];
				//printf("%s: %s<br>", $game, $row->kills);		
			}
			
		}else{
			$seasonArray[] = ["<a href = './roster.php?school=$school&sport=$sportSeason[2]' class='schedule-game'>$sportSeason[0]($sportSeason[1])</a>", "-", "-", "-", "-", "-", "-"];
		}
		
	}
	printf('<table style = "border-spacing: 9px">');
	for($j = 0; $j < count($seasonArray); $j++){
		if($j%2){//alternate colors doesn't work well with table
			printf('<tr>');
		}else{
			printf('<tr>');
		}
		for($i = 0; $i < count($seasonArray[$j]); $i++){
			if($i == 0){
				printf('<td style="text-align: left">%s</td>', $seasonArray[$j][$i]);
			}else{
				printf('<td style="text-align: right">%s</td>', $seasonArray[$j][$i]);
			}
		}
		printf('</tr>');
		
	}

	printf("</table>");
	
	
?>



</body>