<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../stylesheet.css">
</head>
<body>
<?php 
$date = date("Y-m-d", strtotime("today" ));
$fdate = date("l, F d", strtotime("today")); 

$gameID = $_GET['gameID'];
$phpURL = "tennis.php?gameID=".$gameID;
?>

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

<!--Schedule Body-->

<?php
	include '../include/database.php';
	include '../include/header.php';


	$sport = "";
	$home = "";
	$away = "";
	$roster = array();
	$pbpEntries = array();
	$homeID = 0;
	$comp = 0;
	
	$homeTeam = "";
	$awayTeam = "";
	
	$shgame1 = 0;
	$shgame2 = 0;
	$shgame3 = 0;
	$shgame4 = 0;
	$shgame5 = 0;
	$shgame6 = 0;
	$dhgame1 = 0;
	$dhgame2 = 0;
	$dhgame3 = 0;
	
	$sagame1 = 0;
	$sagame2 = 0;
	$sagame3 = 0;
	$sagame4 = 0;
	$sagame5 = 0;
	$sagame6 = 0;
	$dagame1 = 0;
	$dagame2 = 0;
	$dagame3 = 0;
	

		
	$hTotal = 0;
	$aTotal = 0;
    
	$sql = "SELECT t.urlName AS sport FROM schedule AS s JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id = '$gameID'";
	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sport = $row->sport;
	}
	
	$sql = "SELECT s.notes AS info, s.time, s.game_date, s.season AS season, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id, s.team_id, t.formattedName,
		h.formal_name AS homeName, a.formal_name AS awayName, s.time AS startTime
		FROM schedule AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	
	$query = $db->prepare($sql);
	$query->execute();
	
	while($row = $query->fetchObject()){
		printf("<center><h3>%s</h3></center><br>", $row->formattedName);
		
		$homeID = $row->hNum;
		
		$info = $row->info;
		$season = $row->season;
		$startTime = $row->startTime;
			
		$homeTeam = $row->home;
		$awayTeam = $row->away;
		
		$homeName = $row->homeName;
		$awayName = $row->awayName;
	
	}
	
	/*$sqlsport = "SELECT
		sc.home_half1 AS hh1, sc.home_half2 AS hh2, sc.home_OT AS hot, sc.home_total AS ht, sc.away_half1 AS ah1, sc.away_half2 AS ah2, sc.away_OT AS aot, sc.away_total AS at, sc.completed AS cmp
		FROM soccer AS sc JOIN schedule AS s ON sc.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	
	$query = $db->prepare($sqlsport);
	$query->execute();
	while($row = $query->fetchObject()){
			
		$hh1Score = $row->hh1;
		$hh2Score = $row->hh2;
		$hOTScore = $row->hot;
			
		$ah1Score = $row->ah1;
		$ah2Score = $row->ah2;
		$aOTScore = $row->aot;
		
		$hTotal = $row->ht;
		$aTotal = $row->at;
		
		$comp = $row->cmp;
	}*/
	
	$sql = "SELECT rs.formal_name AS name, st.wins AS wins, st.losses AS losses, st.ties AS ties FROM standings AS st JOIN roster_schools AS rs ON st.school_id=rs.id JOIN roster_teams AS rt ON st.sport_id=rt.id WHERE (rs.formal_name =\"$homeName\" or rs.formal_name=\"$awayName\") and st.season='$season' and rt.urlName='$sport'";
	$query = $db->prepare($sql);
	$query->execute();
	$homeWins = $homeLosses = $homeTies = $awayWins = $awayLosses = $awayTies = 0;
	while($row = $query->fetchObject()){
	
		if($row->name == $homeName){
			$homeWins = $row->wins;
			$homeLosses = $row->losses;		
			$homeTies= $row->ties;
		}else if($row->name == $awayName){
			$awayWins = $row->wins;
			$awayLosses = $row->losses;		
			$awayTies= $row->ties;
		}			
	}
	
	/*
	#########################
	#						#
	#		Live Game Info	#
	#						#
	#########################
	*/
	if($comp != 1){
		$live = false;
		$sql = "SELECT period AS quarter, game_time AS time_, info_1 AS goalie FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id=s.id WHERE lg.schedule_id = '$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$qrtr = $row->quarter;
			$time = $row->time_;
			$goalie = $row->goalie;
			
			$live = true;
		}
		
		//SPORTS INFO HEADER
		if($live == true){
			printf('<div class="flex justify-between"><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div><div class="red">H%s</div><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div></div>', $homeTeam, $sport, $homeName, $half, $awayTeam, $sport, $awayName);
		}else{
			printf('<div class="flex justify-between"><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div><div>%s</div><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div></div>', $homeTeam, $sport, $homeName, $startTime, $awayTeam, $sport, $awayName);
		}
	}else{
		printf('<div class="flex justify-between"><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div><div><b>FINAL</b></div><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div></div>', $homeTeam, $sport, $homeName, $awayTeam, $sport, $awayName);
		
	}
	printf('<div class="flex justify-between" ><div><a href = "../standings/standings.php?sport=%s" class="schedule-game">%s-%s-%s</a></div><div>%s - %s</div><div><a href = "../standings/standings.php?sport=%s" class="schedule-game">%s-%s-%s</a></div></div>', $sport, $homeWins, $homeLosses, $homeTies, $hTotal, $aTotal, $sport, $awayWins, $awayLosses, $awayTies);
	printf("<center>%s</center><br><br><br><br>", $info);
	//SPORTS INFO HEADER

	/*
	TEAM1 Period/Time TEAM2
	Standing1 Score Standing2
	
			Info
	*/
	
	/*
	#########################
	#						#
	#		SCORE TABLE		#
	#						#
	#########################
	*/
	
	
	


	

?>

</body>