<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="../stylesheet.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php 
$date = date("Y-m-d", strtotime("today" ));
$fdate = date("l, F d", strtotime("today")); 

$gameID = $_GET['gameID'];
$phpURL = "basketball.php?gameID=".$gameID;
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
	
	$hq1Score = 0;
	$hq2Score = 0;
	$hq3Score = 0;
	$hq4Score = 0;
	$hOTScore = 0;
			
	$aq1Score = 0;
	$aq2Score = 0;
	$aq3Score = 0;
	$aq4Score = 0;
	$aOTScore = 0;
		
	$hTotal = 0;
	$aTotal = 0;
	
	$poss = "";
	$homeTimeOuts = 0;
	$awayTimeOuts = 0;
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
	
	
	
	$sqlsport = "SELECT bb.home_quarter1 AS hq1, bb.home_quarter2 AS hq2, bb.home_quarter3 AS hq3, bb.home_quarter4 AS hq4, bb.home_ot AS hot, bb.home_total AS ht, 
		bb.away_quarter1 AS aq1, bb.away_quarter2 AS aq2, bb.away_quarter3 AS aq3, bb.away_quarter4 AS aq4, bb.away_ot AS aot, bb.away_total AS at, bb.completed AS cmp
		FROM basketball AS bb JOIN schedule AS s ON bb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	
	$query = $db->prepare($sqlsport);
	$query->execute();

	while($row = $query->fetchObject()){
			
		$hq1Score = $row->hq1;
		$hq2Score = $row->hq2;
		$hq3Score = $row->hq3;
		$hq4Score = $row->hq4;
		$hOTScore = $row->hot;
			
		$aq1Score = $row->aq1;
		$aq2Score = $row->aq2;
		$aq3Score = $row->aq3;
		$aq4Score = $row->aq4;
		$aOTScore = $row->aot;
		
		$hTotal = $row->ht;
		$aTotal = $row->at;
		
		$comp = $row->cmp;
	}
	
	$sql = "SELECT rs.formal_name AS name, st.wins AS wins, st.losses AS losses, st.ties AS ties FROM standings AS st JOIN roster_schools AS rs ON st.school_id=rs.id JOIN roster_teams AS rt ON st.sport_id=rt.id WHERE (rs.formal_name ='$homeName' or rs.formal_name='$awayName') and st.season='$season' and rt.urlName='$sport'";
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
		$sql = "SELECT period AS quarter, game_time AS time_, info_1 AS poss, info_2 AS hto, info_3 AS ato FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id=s.id WHERE lg.schedule_id = '$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$qrtr = $row->quarter;
			$time = $row->time_;
			$poss = $row->poss;
			$homeTimeOuts = $row->hto;
			$awayTimeOuts = $row->ato;
			$live = true;
		}
		
		//SPORTS INFO HEADER
		if($live == true){
			printf('<div class="flex justify-between"><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div><div class="red">Q%s %s</div><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div></div>', $homeTeam, $sport, $homeName, $qrtr, $time, $awayTeam, $sport, $awayName);
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
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>Qrtr 1</td> <td> | </td> <td>Qrtr 2</td> <td> | </td> <td>Qrtr 3</td> <td> | </td> <td>Qrtr 4</td> <td> | </td> <td>OT</td> <td> | </td> <td> Total </td></tr>");
	printf("<tr>	<td>----</td> <td>-</td> <td>------</td> <td>-</td> <td>------</td> <td>-</td>  <td>------</td> <td>-</td> <td>------</td> <td>-</td> <td>--</td> <td>-</td> <td>------</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr>",$homeTeam, $hq1Score, $hq2Score, $hq3Score, $hq4Score, $hOTScore, $hTotal);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr></table><br><br>", $awayTeam, $aq1Score, $aq2Score, $aq3Score, $aq4Score, $aOTScore, $aTotal);
	
	printf("Poss: %s<br>", $poss);
	printf("Home TOs: %s<br>", $homeTimeOuts);
	printf("Away TOs: %s<br><br><br>", $awayTimeOuts);

	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.quarter AS qrtr, pbp.time AS tme FROM basketball_pbp AS pbp JOIN schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	
	$pbpArray = array(array("Quarter 1"), array("Quarter 2"), array("Quarter 3"), array("Quarter 4"), array("Overtime"));
	
	while($row = $query->fetchObject()){
		$text = $row->text;
		$qrtr = $row->qrtr;
		$time = $row->tme;
		$pbpText = $time . " | " . $text;
		if(str_contains($text, "Jumper by ") or str_contains($text, "Layup by ") or str_contains($text, "Dunk by ") or str_contains($text, "3 Pointer by ") or str_contains($text, "Free throw by ")){
			$pbpText = "<b>$pbpText</b>";
		}
		switch($qrtr){
			case "Q1":
				$qrtr = 0;
				break;
			case "Q2":
				$qrtr = 1;
				break;
			case "Q3":
				$qrtr = 2;
				break;
			case "Q4":
				$qrtr = 3;
				break;
			case "OT":
				$qrtr = 4;
				break;
		}
		$pbpArray[$qrtr-1][] = $pbpText;
	
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
	
	$sql = "SELECT rp.number AS num, rp.name AS name, bbs.field_goals_attempted AS field_goals_attempted, bbs.field_goals_made AS field_goals_made, bbs.threes_attempted AS threes_attempted, bbs.threes_made AS threes_made, 
	bbs.free_throws_attempted AS free_throws_attempted, bbs.free_throws_made AS free_throws_made , bbs.rebounds AS rebounds, bbs.steals AS steals, bbs.blocks AS blocks, bbs.turnovers AS turnovers, bbs.fouls AS fouls, bbs.assists AS assists 
	FROM basketball_stats AS bbs JOIN schedule AS s ON bbs.game=s.id JOIN roster_player AS rp ON bbs.player=rp.id JOIN roster_teams AS t ON s.team_id=t.id JOIN roster_schools h ON rp.school=h.id
	WHERE bbs.game = '$gameID' AND h.short_name = '$homeTeam' AND t.urlName = '$sport' ORDER BY cast(num as unsigned)";
	$query = $db->prepare($sql);
	$query->execute();
	
	$statArray = array(array(["Players"], ["", "FG", "3PT", "FT", "R", "A", "S", "B", "TO", "PF", "PTS"]));
	printf("STATS<hr>");
	printf("<br>$homeTeam<br>");
	printf('<table style = "border-spacing: 9px">');
	while($row = $query->fetchObject()){
		$name = $row->name;
		$num = $row->num;
		$field_goals_attempted = $row->field_goals_attempted;
		$field_goals_made = $row->field_goals_made;
		$threes_attempted = $row->threes_attempted;
		$threes_made = $row->threes_made;
		$free_throws_attempted = $row->free_throws_attempted;
		$free_throws_made = $row->free_throws_made;
		$rebounds = $row->rebounds;
		$steals = $row->steals;
		$blocks = $row->blocks;
		$turnovers = $row->turnovers;
		$fouls = $row->fouls;
		$assists = $row->assists;
		$urlname = $name;
		$name = explode(" ", $name);
		$name[0] = str_split($name[0])[0] . ".";
		$name = implode(" ", $name);
		$url =  "<a href='../teams/player.php?player=$urlname&school=$homeTeam' class='schedule-game'>$num $name</a>";
		if($field_goals_attempted != '' or $assists != '' or $threes_attempted != '' or $free_throws_attempted != '' or $rebounds != '' or $steals != '' or $blocks != '' or $turnovers != '' or $fouls != ''){
			$statArray[0][] = [$url, $field_goals_made . "/" . $field_goals_attempted, $threes_made . "/" . $threes_attempted, $free_throws_made . "/" . $free_throws_attempted, $rebounds, $assists, $steals, $blocks, $turnovers, $fouls, $free_throws_made + $field_goals_made * 2 + $threes_made * 3];
		}
	}
	
	//Table format
	foreach($statArray as $statLine){
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
	printf("</table>");$sql = "SELECT rp.number AS num, rp.name AS name, bbs.field_goals_attempted AS field_goals_attempted, bbs.field_goals_made AS field_goals_made, bbs.threes_attempted AS threes_attempted, bbs.threes_made AS threes_made, 
	bbs.free_throws_attempted AS free_throws_attempted, bbs.free_throws_made AS free_throws_made , bbs.rebounds AS rebounds, bbs.steals AS steals, bbs.blocks AS blocks, bbs.turnovers AS turnovers, bbs.fouls AS fouls, bbs.assists AS assists 
	FROM basketball_stats AS bbs JOIN schedule AS s ON bbs.game=s.id JOIN roster_player AS rp ON bbs.player=rp.id JOIN roster_teams AS t ON s.team_id=t.id JOIN roster_schools h ON rp.school=h.id
	WHERE bbs.game = '$gameID' AND h.short_name = '$awayTeam' AND t.urlName = '$sport' ORDER BY cast(num as unsigned)";
	$query = $db->prepare($sql);
	$query->execute();
	
	$statArray = array(array(["Players"], ["", "FG", "3PT", "FT", "R", "A", "S", "B", "TO", "PF", "PTS"]));
	
	
	
	printf("<br>$awayTeam<br>");
	printf('<table style = "border-spacing: 9px">');
	while($row = $query->fetchObject()){
		$name = $row->name;
		$num = $row->num;
		$field_goals_attempted = $row->field_goals_attempted;
		$field_goals_made = $row->field_goals_made;
		$threes_attempted = $row->threes_attempted;
		$threes_made = $row->threes_made;
		$free_throws_attempted = $row->free_throws_attempted;
		$free_throws_made = $row->free_throws_made;
		$rebounds = $row->rebounds;
		$steals = $row->steals;
		$blocks = $row->blocks;
		$turnovers = $row->turnovers;
		$fouls = $row->fouls;
		$assists = $row->assists;
		$urlname = $name;
		$name = explode(" ", $name);
		$name[0] = str_split($name[0])[0] . ".";
		$name = implode(" ", $name);
		$url =  "<a href='../teams/player.php?player=$urlname&school=$awayTeam' class='schedule-game'>$num $name</a>";
		if($field_goals_attempted != '' or $assists != '' or $threes_attempted != '' or $free_throws_attempted != '' or $rebounds != '' or $steals != '' or $blocks != '' or $turnovers != '' or $fouls != ''){
			$statArray[0][] = [$url, $field_goals_made . "/" . $field_goals_attempted, $threes_made . "/" . $threes_attempted, $free_throws_made . "/" . $free_throws_attempted, $rebounds, $assists, $steals, $blocks, $turnovers, $fouls, $free_throws_made + $field_goals_made * 2 + $threes_made * 3];
		}
	}
	
	//Table format
	foreach($statArray as $statLine){
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