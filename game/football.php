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
$phpURL = "football.php?gameID=".$gameID;
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
	include '../include/header.php';
	include '../include/database.php';

	$sport = "";
	$home = "";
	$away = "";
	$roster = array();
	$pbpEntries = array();
	$minutes = 0;
	$seconds = 0;
	$maxMin = 99;
	$quarter = 1;
	$homeID = 0;
	$comp = 0;
	
	$info = "";
    
	$sql = "SELECT t.urlName AS sport FROM schedule AS s JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id = '$gameID'";
	
	try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sport = $row->sport;
	}
	
	$sql = "SELECT s.notes AS info, s.time, s.game_date, s.season AS season, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id, s.team_id, t.formattedName,
		h.formal_name AS homeName, a.formal_name AS awayName
		FROM schedule AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	
	
	$query = $db->prepare($sql);
	$query->execute();
	
	while($row = $query->fetchObject()){
		printf("<center><h3>%s</h3></center><br>", $row->formattedName);
		
		$homeID = $row->hNum;
		
		$info = $row->info;
		$season = $row->season;
			
		$homeTeam = $row->home;
		$awayTeam = $row->away;
		
		$homeName = $row->homeName;
		$awayName = $row->awayName;
	}
	
	$sqlsport = "SELECT
		fb.home_quarter1 AS hq1, fb.home_quarter2 AS hq2, fb.home_quarter3 AS hq3, fb.home_quarter4 AS hq4, fb.home_ot AS hot, fb.home_total AS ht, 
		fb.away_quarter1 AS aq1, fb.away_quarter2 AS aq2, fb.away_quarter3 AS aq3, fb.away_quarter4 AS aq4, fb.away_ot AS aot, fb.away_total AS at, fb.completed AS cmp
		FROM football AS fb JOIN schedule AS s ON fb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	
	if($sport=="football"){
		$minutes = 15;
		$seconds = 0;
		$maxMin = 15;
	}else if($sport=="jvfootball"){
		$minutes = 12;
		$seconds = 0;
		$maxMin = 12;
	}
	
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
		$sql = "SELECT period AS quarter, game_time AS time_, info_1 AS poss, info_2 AS tohome, info_3 AS toaway, info_4 AS sof, info_5 AS yardline, info_6 AS ytg, info_7 AS down FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id=s.id WHERE lg.schedule_id = '$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$qrtr = $row->quarter;
			$time = $row->time_;
			$poss = $row->poss;
			$tohome = $row->tohome;
			$toaway = $row->toaway;
			$sof = $row->sof;
			$yardline = $row->yardline;
			$ytg = $row->ytg;
			$down = $row->down;
		}
		
		//SPORTS INFO HEADER
		if($homeTeam == "FCHS"){
			printf('<div class="flex justify-between"><div><a href="../teams/roster.php?sport=%s" class="schedule-game"><b>%s</b></a></div><div class="red">Q%s %s</div><div><b>%s</b></div></div>', $sport, $homeName, $qrtr, $time, $awayName);
		}else{
			printf('<div class="flex justify-between"><div><b>%s</b></div><div class="red">Q%s %s</div><div><a href="../teams/roster.php?sport=%s" class="schedule-game"><b>%s</b></a></div></div>', $homeName, $qrtr, $time, $sport, $awayName);
		}
	}else{
		if($homeTeam == "FCHS"){
			printf('<div class="flex justify-between"> <div><a href="../teams/roster.php?sport=%s" class="schedule-game"><b>%s</b></a></div>         <div><b>FINAL</b></div> <div><b>%s</b></div></div>', $sport, $homeName, $awayName);
		}else{
			printf('<div class="flex justify-between"> <div><b>%s</b></div>         <div><b>FINAL</b></div> <div><a href="../teams/roster.php?sport=%s" class="schedule-game"><b>%s</b></a></div></div>', $homeName, $sport, $awayName);
		}
		
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
	
	
	//LIVE GAME INFO
	if($comp != 1){
	
		switch($down){
			case 1:
				$down = "1st";
				break;
			case 2:
				$down = "2nd";
				break;
			case 3:
				$down = "3rd";
				break;
			case 4:
				$down = "4th";
				break;
		}
		printf("Poss: %s<br>", $poss);
		printf("%s & %s @ %s %s<br>", $down, $ytg, $sof, $yardline);
		printf("Home TOs: %s<br>", $tohome);
		printf("Away TOs: %s<br><br>", $toaway);
	
	}
	
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.quarter AS qrtr, pbp.time AS tme FROM football_pbp AS pbp JOIN schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	
	$pbpArray = array(array("Quarter 1"), array("Quarter 2"), array("Quarter 3"), array("Quarter 4"), array("Overtime"));
	
	while($row = $query->fetchObject()){
		$text = $row->text;
		$qrtr = $row->qrtr;
		$time = $row->tme;
		$pbpText = $time . " | " . $text;
		
		if(str_contains($pbpText, "Kickoff")){
			$pbpArray[$qrtr-1][] = "<hr>";
		}
		
		if(str_contains($text, "Touchdown") or str_contains($text, "Field goal") or str_contains($text, "Safety") or str_contains($text, "Extra point") or str_contains($text, "2-point")){
			$pbpText = "<b>$pbpText</b>";
		}

		$pbpArray[$qrtr-1][] = $pbpText;
		
		if(str_contains($pbpText, "Punt") or str_contains($pbpText, "Interception") or str_contains($pbpText, "Recovered by")){
			$pbpArray[$qrtr-1][] = "<hr>";
		}
		
		//TODO split previous and current at colon take 2nd, if previous array if XOR off opp team and not then add hr
	
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
	
	$sql = "SELECT rp.number AS num, rp.name AS name, fbs.pass_attempts AS patt, fbs.pass_completions AS pcomp, fbs.total_passing_yards AS tpy, fbs.passing_touchdowns AS ptd, fbs.thrown_interceptions AS pint, fbs.sacks_taken AS qbsacks,
	fbs.carries AS carries, fbs.total_carry_yards AS tcy, fbs.rushing_touchdowns AS rtd, fbs.longest_carry AS lc,
	fbs.receptions AS rec, fbs.total_reception_yards AS rec_yards, fbs.reception_touchdowns AS rec_tds, fbs.longest_reception AS rec_long, fbs.targets AS targets,
	fbs.sacks AS sacks, fbs.tackle_for_loss AS tfl, fbs.interceptions AS ints, fbs.forced_fumbles AS ffs
	FROM football_stats AS fbs JOIN schedule AS s ON fbs.game=s.id JOIN roster_player AS rp ON fbs.player=rp.id JOIN roster_teams AS t ON s.team_id=t.id WHERE fbs.game = '$gameID' AND t.urlName = '$sport' ORDER BY cast(num as unsigned)";
	$query = $db->prepare($sql);
	$query->execute();
	
	$statArray = array(array(["Passing"], ["", "C/ATT", "YDS", "AVG", "TD", "INT", "SACKS"]), array(["Rushing"], ["" , "", "CAR", "YDS", "AVG", "TD", "LONG"]), array(["Receiving"], ["" , "REC", "YDS", "AVG", "TD", "LONG", "TGTS"]), array(["Defense"], ["" , "", "", "SACKS", "TFL", "INT", "FF"]));
	printf("STATS<hr>");
	printf('<table style = "border-spacing: 9px">');
	while($row = $query->fetchObject()){
		$name = $row->name;
		$num = $row->num;
		
		$pass_attempts = $row->patt;
		$pass_completions = $row->pcomp;
		$total_passing_yards = $row->tpy;
		$pass_avg = 0;
		if($pass_attempts > 0){
			$pass_avg = $total_passing_yards/$pass_attempts;
		}
		$passing_touchdowns = $row->ptd;
		$thrown_interceptions = $row->pint;
		$sacks_taken = $row->qbsacks;
		
		$carries = $row->carries;
		$total_carry_yards = $row->tcy;
		$rushing_touchdowns = $row->rtd;
		$longest_carry = $row->lc;
		$rushing_avg = 0;
		if($carries > 0){
			$rushing_avg = $total_carry_yards/$carries;
		}
		
		$rec = $row->rec;
		$rec_yards = $row->rec_yards;
		$rec_avg = 0;
		if($rec > 0){
			$rec_avg = $rec_yards/$rec;
		}
		$rec_tds = $row->rec_tds;
		$rec_long = $row->rec_long;
		$targets = $row->targets;
		
		$sacks = $row->sacks;
		$tfl = $row->tfl;
		$ints = $row->ints;
		$ffs = $row->ffs;
		
		$name = explode(" ", $name);
		$name[0] = str_split($name[0])[0] . ".";
		$name = implode(" ", $name);
		if($pass_attempts != ''){
			$statArray[0][] = [$num . ' ' . $name, $pass_completions . "/" . $pass_attempts , $total_passing_yards, number_format($pass_avg, 1), $passing_touchdowns, $thrown_interceptions, $sacks_taken];
		}
		if($carries != ''){
			$statArray[1][] = [$num . ' ' . $name, "", $carries, $total_carry_yards, number_format($rushing_avg, 1), $rushing_touchdowns, $longest_carry];
		}
		if($rec != '' or $rec_yards != '' or $targets != ''){
			$statArray[2][] = [$num . ' ' . $name, $rec, $rec_yards, number_format($rec_avg, 1), $rec_tds, $rec_long, $targets];
		}
		if($sacks != '' or $tfl != '' or $ints != '' or $ffs != ''){
			$statArray[3][] = [$num . ' ' . $name, "", "", $sacks, $tfl, $ints, $ffs];
		}
	}
	
	//Table format
	foreach($statArray as $statLine){
		//sort($statLine);
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
	
	//Non-table format
	/*foreach($statArray as $statLine){
		for($j = 0; $j < count($statLine); $j++){
			if($j%2){//alternate colors doesn't work well with table
				printf('<div class="flex justify-between">');
			}else{
				printf('<div class="flex justify-between">');
			}
			for($i = 0; $i < count($statLine[$j]); $i++){
				if($i == 0){
					printf('<div class="left-align">%s</div><br>', $statLine[$j][$i]);
				}else{
					printf('<div class="right-align stat-width">%s</div>', $statLine[$j][$i]);
				}
			}
			printf('</div><br>');
		}
	}*/
?>

</body>