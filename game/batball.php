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
$phpURL = "batball.php?gameID=".$gameID;
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
	$maxMin = 7;
	$inning = 1;
	$side = "Top";
	$homeID = 0;
	$comp = 0;
	
	$homeTeam = "";
	$awayTeam = "";
		
	$hi1Score = 0;
	$hi2Score = 0;
	$hi3Score = 0;
	$hi4Score = 0;
	$hi5Score = 0;
	$hi6Score = 0;
	$hi7Score = 0;
	$hexScore = 0;	
		
	$hhits = 0;
	$herr = 0;
			
	$ai1Score = 0;
	$ai2Score = 0;
	$ai3Score = 0;
	$ai4Score = 0;
	$ai5Score = 0;
	$ai6Score = 0;
	$ai7Score = 0;
	$aexScore = 0;
		
	$ahits = 0;
	$aerr = 0;
		
	$hTotal = 0;
	$aTotal = 0;
    
	$info = "";
	
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
		bb.home_i1 AS hi1, bb.home_i2 AS hi2, bb.home_i3 AS hi3, bb.home_i4 AS hi4, bb.home_i5 AS hi5, bb.home_i6 AS hi6, bb.home_i7 AS hi7, bb.home_ex AS hex, bb.home_total AS ht, bb.home_hits AS hh, bb.home_errors AS herr,
		bb.away_i1 AS ai1, bb.away_i2 AS ai2, bb.away_i3 AS ai3, bb.away_i4 AS ai4, bb.away_i5 AS ai5, bb.away_i6 AS ai6, bb.away_i7 AS ai7, bb.away_ex AS aex, bb.away_total AS at, bb.away_hits AS ah, bb.away_errors AS aerr, bb.completed AS cmp
		FROM batball AS bb JOIN schedule AS s ON bb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	$query = $db->prepare($sqlsport);
	$query->execute();
	while($row = $query->fetchObject()){
			
		$hi1Score = $row->hi1;
		$hi2Score = $row->hi2;
		$hi3Score = $row->hi3;
		$hi4Score = $row->hi4;
		$hi5Score = $row->hi5;
		$hi6Score = $row->hi6;
		$hi7Score = $row->hi7;
		$hexScore = $row->hex;	
		
		$hhits = $row->hh;
		$herr = $row->herr;
			
		$ai1Score = $row->ai1;
		$ai2Score = $row->ai2;
		$ai3Score = $row->ai3;
		$ai4Score = $row->ai4;
		$ai5Score = $row->ai5;
		$ai6Score = $row->ai6;
		$ai7Score = $row->ai7;
		$aexScore = $row->aex;
		
		$ahits = $row->ah;
		$aerr = $row->aerr;
		
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
		$sql = "SELECT period AS inning, game_time AS side, info_1 AS pitches, info_2 AS outs, info_3 AS strikes, info_4 AS balls, info_5 AS pitching, info_6 AS atBat, info_7 AS first, info_8 AS second, info_9 AS third FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id=s.id WHERE lg.schedule_id = '$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){// time tb outs strikes balls atbat 1st 2nd 3rd 
			$inning = $row->inning;
			$side = $row->side; //top bottom
			$pitches = $row->pitches; //top bottom
			$outs = $row->outs;
			$strikes = $row->strikes;
			$balls = $row->balls;
			$pitching = $row->pitching;
			$atBat = $row->atBat; //palyer
			$first = $row->first; //palyer
			$second = $row->second; //palyer
			$third = $row->third; //palyer
			
			$live = true;
			
			$pitches = explode(",", $pitches);
			
		}
		
		//SPORTS INFO HEADER
		if($live == true){
			printf('<div class="flex justify-between"><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div><div class="red">%s %s</div><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div></div>', $homeTeam, $sport, $homeName, $side, $inning, $awayTeam, $sport, $awayName);
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
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>1</td> <td> | </td> <td>2</td> <td> | </td> <td>3</td> <td> | </td> <td>4</td> <td> | </td> <td>5</td> <td> | </td> <td>6</td> <td> | </td> <td>7</td> <td> | </td> <td>Ex</td> <td> || </td> <td> R </td><td> | </td> <td> H </td><td> | </td> <td> E </td></tr>");
	printf("<tr>	<td>----</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr>", $awayTeam, $ai1Score, $ai2Score, $ai3Score, $ai4Score, $ai5Score, $ai6Score, $ai7Score, $aexScore, $aTotal, $ahits, $aerr);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr></table><br><br>", $homeTeam, $hi1Score, $hi2Score, $hi3Score, $hi4Score, $hi5Score, $hi6Score, $hi7Score, $hexScore, $hTotal, $hhits, $herr);
	
	if($comp != 1 && $live == true){
		printf("Pitching: %s<br>", $pitching);
		printf("At Bat: %s %s-%s, %s Outs<br>", $atBat, $balls, $strikes, $outs);
		printf("1B: %s<br>", $first);
		printf("2B: %s<br>", $second);
		printf("3B: %s<br>", $third);
		
		printf("Pitches:<br>");
		
		foreach($pitches as $pitch){
			if(str_contains($pitch, "strike") or str_contains($pitch, "foul")){
				printf("<span class='red'>%s</span><br>", $pitch);
			}else{
				printf("<span class='green'>%s</span><br>", $pitch);
			}
			
		}
	}
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.inning AS inning FROM batball_pbp AS pbp JOIN schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	$pbpArray = array(array("Inning 1"), array("Inning 2"), array("Inning 3"), array("Inning 4"), array("Inning 5"), array("Inning 6"), array("Inning 7"), array("Extra Inning"));

	
	while($row = $query->fetchObject()){
		$text = $row->text;
		$inn = $row->inning;
		$pbpText = $inn . " | " . $text;
		if(str_contains($text, "scores") or str_contains($text, "homers")){
			$pbpText = "<b>$pbpText</b>";
		}
		if($inn == "EX"){
			$inn = 8;
		}
		$pbpArray[$inn-1][] = $pbpText;
	
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
	
	$sql = "SELECT rp.number AS num, rp.name AS name, bbs.hits AS hits, bbs.at_bats AS at_bats, bbs.runs AS runs, bbs.runs_batted_in AS runs_batted_in, bbs.base_on_balls AS base_on_balls, bbs.strikeouts AS strikeouts, bbs.left_on_base AS left_on_base,
	bbs.doubles AS doubles, bbs.triples AS triples, bbs.homeruns AS homeruns,
	bbs.innings_pitched AS innings_pitched, bbs.hits_allowed AS hits_allowed, bbs.pitches AS pitches, bbs.runs_allowed AS runs_allowed, bbs.earned_runs_allowed AS earned_runs_allowed, bbs.base_on_balls_allowed AS base_on_balls_allowed, bbs.strikeouts_given AS strikeouts_given, bbs.homeruns_allowed AS homeruns_allowed 
	FROM batball_stats AS bbs JOIN schedule AS s ON bbs.game=s.id JOIN roster_player AS rp ON bbs.player=rp.id JOIN roster_teams AS t ON s.team_id=t.id JOIN roster_schools h ON rp.school=h.id
	WHERE bbs.game = '$gameID' AND h.short_name = '$homeTeam' AND t.urlName = '$sport' ORDER BY cast(num as unsigned)";
	$query = $db->prepare($sql);
	$query->execute();
	//Doesn't pull doubles, triples, homeruns, errors, bases_stolen
	
	
	$statArray = array(array(["Batting"], ["", "", "H", "AB", "R", "RBI", "BB", "K", "2B", "3B", "HR"]), array(["Pitching"], ["" , "", "IP", "H", "R", "ER", "BB", "K", "HR", "P"]));
	printf("STATS<hr>");
	printf("<br>$homeTeam<br>");
	printf('<table style = "border-spacing: 9px">');
	while($row = $query->fetchObject()){
		$name = $row->name;
		$num = $row->num;
		$hits = $row->hits;
		$at_bats = $row->at_bats;
		$runs = $row->runs;
		$runs_batted_in = $row->runs_batted_in;
		$base_on_balls = $row->base_on_balls;
		$strikeouts = $row->strikeouts;
		$left_on_base = $row->left_on_base;
		$doubles = $row->doubles;
		$triples = $row->triples;
		$homeruns = $row->homeruns;
		$hits_allowed = $row->hits_allowed;
		$innings_pitched = $row->innings_pitched;
		$pitches = $row->pitches;
		$runs_allowed = $row->runs_allowed;
		$earned_runs_allowed = $row->earned_runs_allowed;
		$base_on_balls_allowed = $row->base_on_balls_allowed;
		$strikeouts_given = $row->strikeouts_given;
		$homeruns_allowed = $row->homeruns_allowed;
		$name = explode(" ", $name);
		$name[0] = str_split($name[0])[0] . ".";
		$name = implode(" ", $name);
		if($at_bats != 0 or $runs != 0){
			$statArray[0][] = [$num . ' ' . $name, "",$hits, $at_bats, $runs, $runs_batted_in, $base_on_balls, $strikeouts, $doubles, $triples, $homeruns];
		}
		if($pitches != 0){
			$statArray[1][] = [$num . ' ' . $name, "",$innings_pitched, $hits_allowed, $runs_allowed, $earned_runs_allowed, $base_on_balls_allowed, $strikeouts_given, $homeruns_allowed, $pitches];
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
	
	$sql = "SELECT rp.number AS num, rp.name AS name, bbs.hits AS hits, bbs.at_bats AS at_bats, bbs.runs AS runs, bbs.runs_batted_in AS runs_batted_in, bbs.base_on_balls AS base_on_balls, bbs.strikeouts AS strikeouts, bbs.left_on_base AS left_on_base,
	bbs.doubles AS doubles, bbs.triples AS triples, bbs.homeruns AS homeruns,
	bbs.innings_pitched AS innings_pitched, bbs.hits_allowed AS hits_allowed, bbs.pitches AS pitches, bbs.runs_allowed AS runs_allowed, bbs.earned_runs_allowed AS earned_runs_allowed, bbs.base_on_balls_allowed AS base_on_balls_allowed, bbs.strikeouts_given AS strikeouts_given, bbs.homeruns_allowed AS homeruns_allowed 
	FROM batball_stats AS bbs JOIN schedule AS s ON bbs.game=s.id JOIN roster_player AS rp ON bbs.player=rp.id JOIN roster_teams AS t ON s.team_id=t.id JOIN roster_schools h ON rp.school=h.id
	WHERE bbs.game = '$gameID' AND h.short_name = '$awayTeam' AND t.urlName = '$sport' ORDER BY cast(num as unsigned)";
	$query = $db->prepare($sql);
	$query->execute();
	//Doesn't pull doubles, triples, homeruns, errors, bases_stolen
	
	
	$statArray = array(array(["Batting"], ["", "", "H", "AB", "R", "RBI", "BB", "K", "2B", "3B", "HR"]), array(["Pitching"], ["" , "", "IP", "H", "R", "ER", "BB", "K", "HR", "P"]));
	printf("STATS<hr>");
	printf("<br>$awayTeam<br>");
	printf('<table style = "border-spacing: 9px">');
	while($row = $query->fetchObject()){
		$name = $row->name;
		$num = $row->num;
		$hits = $row->hits;
		$at_bats = $row->at_bats;
		$runs = $row->runs;
		$runs_batted_in = $row->runs_batted_in;
		$base_on_balls = $row->base_on_balls;
		$strikeouts = $row->strikeouts;
		$left_on_base = $row->left_on_base;
		$doubles = $row->doubles;
		$triples = $row->triples;
		$homeruns = $row->homeruns;
		$hits_allowed = $row->hits_allowed;
		$innings_pitched = $row->innings_pitched;
		$pitches = $row->pitches;
		$runs_allowed = $row->runs_allowed;
		$earned_runs_allowed = $row->earned_runs_allowed;
		$base_on_balls_allowed = $row->base_on_balls_allowed;
		$strikeouts_given = $row->strikeouts_given;
		$homeruns_allowed = $row->homeruns_allowed;
		$name = explode(" ", $name);
		$name[0] = str_split($name[0])[0] . ".";
		$name = implode(" ", $name);
		if($at_bats != 0 or $runs != 0){
			$statArray[0][] = [$num . ' ' . $name, "",$hits, $at_bats, $runs, $runs_batted_in, $base_on_balls, $strikeouts, $doubles, $triples, $homeruns];
		}
		if($pitches != 0){
			$statArray[1][] = [$num . ' ' . $name, "", $innings_pitched, $hits_allowed, $runs_allowed, $earned_runs_allowed, $base_on_balls_allowed, $strikeouts_given, $homeruns_allowed, $pitches];
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