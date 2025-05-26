<?php
include '../include/database.php';

	$gameID = $_GET['gameID'];
	$homeRoster = array();
	$awayRoster = array();
	$pbpEntries = array();
	$completed = 0;
	$table = "";
	
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
    
	
	

	$sql = "SELECT schedule.season AS season, roster_teams.formattedName AS sportNice, roster_teams.urlName AS sport, home.formal_name AS homeTeam, home.short_name AS homeKey, away.formal_name AS awayTeam, away.short_name AS awayKey 
	FROM schedule JOIN roster_schools AS home ON schedule.home_id=home.id  JOIN roster_schools AS away ON schedule.away_id=away.id 
	JOIN roster_teams ON schedule.team_id=roster_teams.id WHERE schedule.id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sportNice = $row->sportNice;
		$sport = $row->sport;
		$season = $row->season;
				
		$homeTeam = $row->homeTeam;
		$awayTeam = $row->awayTeam;
		$home = $row->homeKey;
		$away = $row->awayKey;
		
	}
	
	$info = array("sport"=>$sportNice, "home"=>$homeTeam, "away"=>$awayTeam, "homeKey"=>$home, "awayKey"=>$away);


	$table = "soccer";
	$minutes = 0;
	$seconds = 0;
	$maximumMinutes = 35;
	$sportType = "half";
	include './soccer.php'; //Import all soccer variables

	


	$sql = "SELECT game.home_half1 AS hh1, game.home_half2 AS hh2, game.home_OT AS hot, game.home_total AS ht, 
			game.away_half1 AS ah1, game.away_half2 AS ah2, game.away_OT AS aot, game.away_total AS at, game.completed AS cmp
			FROM $table AS game JOIN schedule ON game.schedule_id = schedule.id WHERE schedule.id='$gameID'";

	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){


		$scoreArray = [];

		$scoreArray = [$row->hh1, $row->hh2, $row->hot, $row->ah1, $row->ah2, $row->aot];

		$info["scores"] = $scoreArray;
			
		$completed = $row->cmp;
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