<?php
include '../include/database.php';

	$table = $_GET['table'];
	$gameID = $_GET['gameID'];
	$infoArray = $_GET['infoArray'];
	$sportID = $infoArray[0];
	$home = $infoArray[1];
	$away = $infoArray[2];
	$homeTotal = $infoArray[3];
	$awayTotal = $infoArray[4];
	
	$season = 2025;
	$sql = "";
	
	
	$message = "";
	$homeWins = 0;
	$homeLosses = 0;
	$homeTies = 0;
	$awayWins = 0;
	$awayLosses = 0;
	$awayTies = 0;
		
	$sql = "UPDATE $table SET completed = 1 WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	#select home team standing info
	$sql = "SELECT wins AS w, losses AS l, ties AS t, school_id AS school, sport_id AS sport FROM standings AS st JOIN roster_teams AS t on st.sport_id=t.id JOIN roster_schools on st.school_id=roster_schools.id
	WHERE roster_schools.short_name='$home' AND st.sport_id = '$sportID' AND st.season='$season'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$homeWins = $row->w;
		$homeLosses = $row->l;
		$homeTies = $row->t;
	}
	#select away team standing info
	$sql = "SELECT wins AS w, losses AS l, ties AS t, school_id AS school, sport_id AS sport FROM standings AS st JOIN roster_teams AS t on st.sport_id=t.id JOIN roster_schools on st.school_id=roster_schools.id
	WHERE roster_schools.short_name='$away' AND st.sport_id = '$sportID' AND st.season='$season'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$awayWins = $row->w;
		$awayLosses = $row->l;
		$awayTies = $row->t;
	}
	#update standings
	if($homeTotal > $awayTotal){
		$homeWins += 1;
		$awayLosses += 1;
	}else if($awayTotal > $homeTotal){
		$homeLosses += 1;
		$awayWins += 1;
	}else{
		$homeTies += 1;
		$awayTies += 1;
	}
	$sqls = "UPDATE standings AS st JOIN roster_teams AS t on st.sport_id=t.id JOIN roster_schools on st.school_id=roster_schools.id SET wins = '$homeWins', losses = '$homeLosses', ties = '$homeTies'
	WHERE roster_schools.short_name='$home' AND sport_id = '$sportID' AND st.season='$season' ";
	$query = $db->prepare($sqls);
	$query->execute();
	$sqls = "UPDATE standings AS st JOIN roster_teams AS t on st.sport_id=t.id JOIN roster_schools on st.school_id=roster_schools.id SET wins = '$awayWins', losses = '$awayLosses', ties = '$awayTies' 
	WHERE roster_schools.short_name='$away' AND sport_id = '$sportID' AND st.season='$season' ";
	$query = $db->prepare($sqls);
	$query->execute();
	
	#remove from livegame
	$sql = "DELETE FROM live_games WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();

	$message = "Game DB Complete";
	
	echo json_encode($message);	
?>