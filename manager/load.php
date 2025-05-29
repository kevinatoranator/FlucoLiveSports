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
	}else if(str_contains($sportNice, "Softball") or str_contains($sportNice, "Baseball")){
		
		$table = "batball";
		$gameType = SPORTTYPE::Inning;
	}

	$info = array("sport"=>$sportNice, "sportID"=>$sportID, "home"=>$homeTeam, "away"=>$awayTeam, "homeKey"=>$home, "awayKey"=>$away, "table"=>$table, "level"=>$level, "minutes"=>$minutes);


	if($gameType == SPORTTYPE::Half){
		$sql = "SELECT game.home_half1 AS hh1, game.home_half2 AS hh2, game.home_OT AS hot, 
			game.away_half1 AS ah1, game.away_half2 AS ah2, game.away_OT AS aot, game.completed AS cmp
			FROM $table AS game JOIN schedule ON game.schedule_id = schedule.id WHERE schedule.id='$gameID'";
	}else if($gameType == SPORTTYPE::Quarter){
		$sql = "SELECT game.home_quarter1 AS hq1, game.home_quarter2 AS hq2, game.home_quarter3 AS hq3, game.home_quarter4 AS hq4, game.home_ot AS hot, 
		game.away_quarter1 AS aq1, game.away_quarter2 AS aq2, game.away_quarter3 AS aq3, game.away_quarter4 AS aq4, game.away_ot AS aot, game.completed AS cmp 
		FROM $table AS game JOIN schedule ON game.schedule_id = schedule.id WHERE schedule.id='$gameID'";
	}else if($gameType == SPORTTYPE::Inning){
		$sql = "SELECT game.home_i1 AS hi1, game.home_i2 AS hi2, game.home_i3 AS hi3, game.home_i4 AS hi4, game.home_i5 AS hi5, game.home_i6 AS hi6, game.home_i7 AS hi7, game.home_ex AS hex, game.home_hits AS hh, game.home_errors AS he,
		game.away_i1 AS ai1, game.away_i2 AS ai2, game.away_i3 AS ai3, game.away_i4 AS ai4, game.away_i5 AS ai5, game.away_i6 AS ai6, game.away_i7 AS ai7, game.away_ex AS aex, game.away_hits AS ah, game.away_errors AS ae, game.completed AS cmp
		FROM $table AS game JOIN schedule ON game.schedule_id = schedule.id JOIN roster_schools a ON schedule.away_id=a.id JOIN roster_schools h ON schedule.home_id=h.id JOIN roster_teams AS t ON schedule.team_id=t.id WHERE schedule.id='$gameID'";
	
	}

	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){


		$scoreArray = [];
		if($gameType == SPORTTYPE::Half){
			$scoreArray = [$row->hh1, $row->hh2, $row->hot, $row->ah1, $row->ah2, $row->aot];
		}else if($gameType == SPORTTYPE::Quarter){
			$scoreArray = [$row->hq1, $row->hq2, $row->hq3, $row->hq4, $row->hot, $row->aq1, $row->aq2, $row->aq3, $row->aq4, $row->aot];
		}else if($gameType == SPORTTYPE::Inning){
			$scoreArray = [$row->hi1, $row->hi2, $row->hi3, $row->hi4, $row->hi5, $row->hi6, $row->hi7, $row->hex, $row->ai1, $row->ai2, $row->ai3, $row->ai4, $row->ai5, $row->ai6, $row->ai7, $row->aex];
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