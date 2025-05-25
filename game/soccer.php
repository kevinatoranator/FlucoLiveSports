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
$phpURL = "soccer.php?gameID=".$gameID;
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
	$minutes = 0;
	$seconds = 0;
	$maxMin = 99;
	$half = 1;
	$homeID = 0;
	$comp = 0;
	
	$homeTeam = "";
	$awayTeam = "";
	
	$hh1Score = 0;
	$hh2Score = 0;
	$hOTScore = 0;
			
	$ah1Score = 0;
	$ah2Score = 0;
	$aOTScore = 0;
		
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
	
	$sqlsport = "SELECT
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
	}
	
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
			printf('<div class="flex justify-between"><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div><div class="red">H%s %s\'</div><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div></div>', $homeTeam, $sport, $homeName, $half, $time, $awayTeam, $sport, $awayName);
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
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>Half 1</td> <td> | </td> <td>Half 2</td> <td> | </td> <td>OT</td> <td> | </td> <td> Total </td></tr>");
	printf("<tr>	<td>----</td> <td>---</td> <td>------</td> <td>---</td> <td>------</td> <td>---</td> <td>--</td> <td>---</td> <td>------</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr>", $awayTeam, $ah1Score, $ah2Score, $aOTScore, $aTotal);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr></table><br><br>",$homeTeam, $hh1Score, $hh2Score, $hOTScore, $hTotal);
	
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.half AS half, pbp.time AS tme FROM soccer_pbp AS pbp JOIN schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	
	$pbpArray = array(array("Half 1"), array("Half 2"), array("Overtime"));
	
	while($row = $query->fetchObject()){
		$text = $row->text;
		$half = $row->half;
		$time = $row->tme;
		$pbpText = "H" . $half . " " . $time . "'" . " | " . $text;
		if(str_contains($text, "Goal")){
			$pbpText = "<b>$pbpText</b>";
		}
		switch($half){
			case "1":
				$half = 0;
				break;
			case "2":
				$half = 1;
				break;
			case "3":
				$half = 2;
				break;
		}
		$pbpArray[$half][] = $pbpText;
	
	}

	if($comp == 1){
		for($i = 0; $i<count($pbpArray); $i++){
			$quarter = $pbpArray[$i][0];
			?> <input type="checkbox" name="<?php echo $quarter?>" id="<?php echo $quarter?>" onclick="rtoggle('<?php echo $quarter?>')"><label for="<?php echo $quarter?>"><b><?php echo $quarter?> [+]</b></label><br><br>
			<div class = "<?php echo $quarter?> hidden"><?php
			foreach($pbpArray[$i] as $entry){
				printf($entry . "<br><br>");
			}
			printf("</div>");
			printf("<br>");
		}
		printf("<br>-END OF GAME-<br><br><br>");
	}else{
		$newestQuarter = true;
		for($i = count($pbpArray); $i > 0; $i--){
			if(count($pbpArray[$i-1]) > 1){//Only display if entries
				$quarter = $pbpArray[$i-1][0];
				?> <input type="checkbox" name="<?php echo $quarter?>" id="<?php echo $quarter?>" onclick="rtoggle('<?php echo $quarter?>')"><label for="<?php echo $quarter?>"><b><?php echo $quarter?> [+]</b></label><br><br>
				<?php if(!$newestQuarter){ $quarter = $quarter . " hidden";}?>
				<div class = "<?php echo $quarter?>"><?php
				for($j = count($pbpArray[$i-1]); $j > 1; $j--){
					printf($pbpArray[$i-1][$j-1] . "<br><br>");
				}
				printf("</div>");
				$newestQuarter = false;
			}
		}
	}
	
	/*
	#########################
	#						#
	#		Stats			#
	#						#
	#########################
	*/
	
	//Home Team
	
	$sql = "SELECT rp.number AS num, rp.name AS name, ss.goals AS goals, ss.assists AS assists, ss.shots_on_goal AS shots_on_goal,
	ss.blocked_shots AS blocked_shots, ss.fouls AS fouls, ss.saves AS saves, ss.goals_allowed AS goals_allowed
	FROM soccer_stats AS ss JOIN schedule AS s ON ss.game=s.id JOIN roster_player AS rp ON ss.player=rp.id JOIN roster_teams AS t ON s.team_id=t.id JOIN roster_schools h ON rp.school=h.id
	WHERE ss.game = '$gameID' AND h.short_name = '$homeTeam' AND t.urlName = '$sport' ORDER BY cast(num as unsigned)";
	$query = $db->prepare($sql);
	$query->execute();
	
	$homestatArray = array(array(["Players"], ["", "Goals", "Assts", "SOG", "Blocks", "Fouls"]), array(["Goalies"], ["" , "", "", "Saves", "G/A", "Save%"]));
	//no red or yellow cards
	printf("STATS<hr>");
	printf("<br>$homeTeam<br>");
	printf('<table style = "border-spacing: 9px">');
	while($row = $query->fetchObject()){
		$name = $row->name;
		$num = $row->num;
		$goals = $row->goals;
		$assists = $row->assists;
		$shots_on_goal = $row->shots_on_goal;
		$blocked_shots = $row->blocked_shots;
		$fouls = $row->fouls;
		$saves = $row->saves;
		$goals_allowed = $row->goals_allowed;
		$name = explode(" ", $name);
		$name[0] = str_split($name[0])[0] . ".";
		$name = implode(" ", $name);
		if($goals != 0 or $assists != 0 or $shots_on_goal != 0 or $blocked_shots != 0 or $fouls != 0){
			$homestatArray[0][] = [$num . ' ' . $name, $goals, $assists, $shots_on_goal, $blocked_shots, $fouls];
		}
		if($saves != 0 or $goals_allowed != 0){
			$homestatArray[1][] = [$num . ' ' . $name, '', '', $saves, $goals_allowed, (int)($saves/($saves+$goals_allowed)*100) . "%"];
		}
	}
	
	//Table format
	foreach($homestatArray as $statLine){
		for($j = 0; $j < count($statLine); $j++){
			if($j%2){//alternate colors doesn't work well with table
				printf('<tr>');
			}else{
				printf('<tr">');
			}
			for($i = 0; $i < count($statLine[$j]); $i++){
				if($i == 0){
					printf('<td style="text-align: left">%s</td>', $statLine[$j][$i]);
				}else{
					printf('<td style="text-align: right">%s</td>', $statLine[$j][$i]);
				}
			}
			printf('</tr>');
		}
		printf('<tr></tr>');
	}
	printf("</table>");	
	
	
	//Away Team
	$sql = "SELECT rp.number AS num, rp.name AS name, ss.goals AS goals, ss.assists AS assists, ss.shots_on_goal AS shots_on_goal,
	ss.blocked_shots AS blocked_shots, ss.fouls AS fouls, ss.saves AS saves, ss.goals_allowed AS goals_allowed
	FROM soccer_stats AS ss JOIN schedule AS s ON ss.game=s.id JOIN roster_player AS rp ON ss.player=rp.id JOIN roster_teams AS t ON s.team_id=t.id JOIN roster_schools a ON rp.school=a.id
	WHERE ss.game = '$gameID' AND t.urlName = '$sport' AND a.short_name = '$awayTeam' ORDER BY cast(num as unsigned)";
	$query = $db->prepare($sql);
	$query->execute();
	
	$awaystatArray = array(array(["Players"], ["", "Goals", "Assts", "SOG", "Blocks", "Fouls"]), array(["Goalies"], ["" , "", "", "Saves", "G/A", "Save%"]));
	//no red or yellow cards
	printf("<br>$awayTeam<br>");
	printf('<table style = "border-spacing: 9px">');
	while($row = $query->fetchObject()){
		$name = $row->name;
		$num = $row->num;
		$goals = $row->goals;
		$assists = $row->assists;
		$shots_on_goal = $row->shots_on_goal;
		$blocked_shots = $row->blocked_shots;
		$fouls = $row->fouls;
		$saves = $row->saves;
		$goals_allowed = $row->goals_allowed;
		$name = explode(" ", $name);
		$name[0] = str_split($name[0])[0] . ".";
		$name = implode(" ", $name);
		if($goals != 0 or $assists != 0 or $shots_on_goal != 0 or $blocked_shots != 0 or $fouls != 0){
			$awaystatArray[0][] = [$num . ' ' . $name, $goals, $assists, $shots_on_goal, $blocked_shots, $fouls];
		}
		if($saves != 0 or $goals_allowed != 0){
			$awaystatArray[1][] = [$num . ' ' . $name, '', '', $saves, $goals_allowed, (int)($saves/($saves+$goals_allowed)*100) . "%"];
		}
	}
	
	//Table format
	foreach($awaystatArray as $statLine){
		for($j = 0; $j < count($statLine); $j++){
			if($j%2){//alternate colors doesn't work well with table
				printf('<tr>');
			}else{
				printf('<tr">');
			}
			for($i = 0; $i < count($statLine[$j]); $i++){
				if($i == 0){
					printf('<td style="text-align: left">%s</td>', $statLine[$j][$i]);
				}else{
					printf('<td style="text-align: right">%s</td>', $statLine[$j][$i]);
				}
			}
			printf('</tr>');
		}
		printf('<tr></tr>');
	}
	printf("</table>");	
?>

</body>