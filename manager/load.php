<?php
include '../include/database.php';

	$gameID = $_GET['gameID'];
	$homeRoster = array();
	$awayRoster = array();
	$pbpEntries = array();
	$completed = 0;
	$table = "";
	enum SPORTTYPE{
		case Half;
		case Quarter;
		case Inning;
		case Set;
	}

	$gameType = SPORTTYPE::Half;
	$sql = "SELECT schedule.season AS season, roster_teams.formattedName AS sportNice, roster_teams.urlName AS sport, home.formal_name AS homeTeam, home.short_name AS homeKey, away.formal_name AS awayTeam, away.short_name AS awayKey, 
	schedule.team_id AS sportID FROM schedule JOIN roster_schools AS home ON schedule.home_id=home.id  JOIN roster_schools AS away ON schedule.away_id=away.id 
	JOIN roster_teams ON schedule.team_id=roster_teams.id WHERE schedule.id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sportNice = $row->sportNice;
		$sport = $row->sport;
		$season = $row->season;
		$sportID = $row->sportID;
				
		$homeTeam = $row->homeTeam;
		$awayTeam = $row->awayTeam;
		$home = $row->homeKey;
		$away = $row->awayKey;
		
	}
	
	
	
	$level = "";
	if(str_contains($sportNice, "JV")){
		$level = "jv";
	}else{
		$level = "varsity";
	}
	
	$table = "";
	$scoreLength = 0;
	$minutes = 0;
	if(str_contains($sportNice, "Soccer")){
		if($level =="jv"){
			$minutes = 35;
		}else{
			$minutes = 40;
		}
		$table = "soccer";
		$gameType = SPORTTYPE::Half;
	}else if(str_contains($sportNice, "Lacrosse") and str_contains($sportNice, "Boys")){
		if($level =="jv"){
			$minutes = 10;
		}else{
			$minutes = 12;
		}
		$table = "blax";
		$gameType = SPORTTYPE::Quarter;
	}else if(str_contains($sportNice, "Lacrosse") and str_contains($sportNice, "Girls")){
		if($level =="jv"){
			$minutes = 10;
		}else{
			$minutes = 12;
		}
		$table = "glax";
		$gameType = SPORTTYPE::Quarter;
	}else if(str_contains($sportNice, "Field Hockey")){
		if($level =="jv"){
			$minutes = 12;
		}else{
			$minutes = 15;
		}
		$table = "field_hockey";
		$gameType = SPORTTYPE::Quarter;
	}

	$info = array("sport"=>$sportNice, "sportID"=>$sportID, "home"=>$homeTeam, "away"=>$awayTeam, "homeKey"=>$home, "awayKey"=>$away, "table"=>$table, "level"=>$level, "minutes"=>$minutes);


	if($gameType == SPORTTYPE::Half){
		$sql = "SELECT game.home_half1 AS hh1, game.home_half2 AS hh2, game.home_OT AS hot, game.home_total AS ht, 
			game.away_half1 AS ah1, game.away_half2 AS ah2, game.away_OT AS aot, game.away_total AS at, game.completed AS cmp
			FROM $table AS game JOIN schedule ON game.schedule_id = schedule.id WHERE schedule.id='$gameID'";
	}else if($gameType == SPORTTYPE::Quarter){
		$sql = "SELECT game.home_quarter1 AS hq1, game.home_quarter2 AS hq2, game.home_quarter3 AS hq3, game.home_quarter4 AS hq4, game.home_ot AS hot, 
		game.away_quarter1 AS aq1, game.away_quarter2 AS aq2, game.away_quarter3 AS aq3, game.away_quarter4 AS aq4, game.away_ot AS aot, game.completed AS cmp 
		FROM $table AS game JOIN schedule ON game.schedule_id = schedule.id WHERE schedule.id='$gameID'";
	}

	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){


		$scoreArray = [];
		if($gameType == SPORTTYPE::Half){
			$scoreArray = [$row->hh1, $row->hh2, $row->hot, $row->ah1, $row->ah2, $row->aot];
		}else if($gameType == SPORTTYPE::Quarter){
			$scoreArray = [$row->hq1, $row->hq2, $row->hq3, $row->hq4, $row->hot, $row->aq1, $row->aq2, $row->aq3, $row->aq4, $row->aot];
		}
		

		$info["scores"] = $scoreArray;
			
		$completed = $row->cmp;
		
		$info['completed'] = $completed;
	}
	

	$sql = "SELECT roster_player.name AS player, roster_player.number AS number FROM roster_player INNER JOIN roster_schools ON roster_player.school=roster_schools.id 
	JOIN roster_teams ON roster_player.team_id=roster_teams.id WHERE roster_player.season = '$season' AND roster_teams.urlName='$sport' AND roster_schools.short_name ='$home'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$player = $row->player;
		$number = $row->number;
		if($number != "Head Coach" and $number != "Assistant Coaches" and $number != "Assistant Coach" and $number != "Managers"){
			$homeRoster[$player] = $number;
		}
	}
	$info["homeRoster"] = $homeRoster;
	
	$sql = "SELECT roster_player.name AS player, roster_player.number AS number FROM roster_player INNER JOIN roster_schools ON roster_player.school=roster_schools.id 
	JOIN roster_teams ON roster_player.team_id=roster_teams.id WHERE roster_player.season = '$season' AND roster_teams.urlName='$sport' AND roster_schools.short_name ='$away'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$player = $row->player;
		$number = $row->number;
		if($number != "Head Coach" and $number != "Assistant Coaches" and $number != "Assistant Coach" and $number != "Managers"){
			$awayRoster[$player] = $number;
		}
	}
	$info["awayRoster"] = $awayRoster;

echo json_encode($info);
			
?>