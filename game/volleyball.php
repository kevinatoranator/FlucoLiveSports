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
$phpURL = "volleyball.php?gameID=".$gameID;
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
	$set = 1;
	$homeID = 0;
	$comp = 0;
	
	$homeTeam = "";
	$awayTeam = "";
		
	$hs1Score = 0;
	$hs2Score = 0;
	$hs3Score = 0;
	$hs4Score = 0;
	$hs5Score = 0;
			
	$as1Score = 0;
	$as2Score = 0;
	$as3Score = 0;
	$as4Score = 0;
	$as5Score = 0;
	
	$hTotal = 0;
	$aTotal = 0;
    
	$info = "";
	
	$sql = "SELECT t.urlName AS sport FROM schedule AS s JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id = '$gameID'";
	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$sport = $row->sport;
	}
	
	$sql = "SELECT s.notes AS info, s.time AS startTime, s.game_date, s.season AS season, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id, s.team_id, t.formattedName,
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
		
		$startTime = $row->startTime;
		
	}

	$sqlsport = "SELECT 
	vb.home_set1 AS hs1, vb.home_set2 AS hs2, vb.home_set3 AS hs3, vb.home_set4 AS hs4, vb.home_set5 AS hs5, vb.home_total AS ht,
	vb.away_set1 AS as1, vb.away_set2 AS as2, vb.away_set3 AS as3, vb.away_set4 AS as4, vb.away_set5 AS as5, vb.away_total AS at, vb.completed AS cmp
	FROM volleyball AS vb JOIN schedule AS s ON vb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";

	
	$query = $db->prepare($sqlsport);
	$query->execute();
	while($row = $query->fetchObject()){
		
		$hs1Score = $row->hs1;
		$hs2Score = $row->hs2;
		$hs3Score = $row->hs3;
		$hs4Score = $row->hs4;
		$hs5Score = $row->hs5;
			
		$as1Score = $row->as1;
		$as2Score = $row->as2;
		$as3Score = $row->as3;
		$as4Score = $row->as4;
		$as5Score = $row->as5;

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
		$sql = "SELECT period AS set_, game_time AS serving FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id=s.id WHERE lg.schedule_id = '$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$set = $row->set_;
			
			$live = true;
		}
		
		//SPORTS INFO HEADER
		if($live == true){
			printf('<div class="flex justify-between"><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div><div class="red">Set %s</div><div><a href="../teams/roster.php?school=%s&sport=%s" class="schedule-game"><b>%s</b></a></div></div>', $homeTeam, $sport, $homeName, $set, $awayTeam, $sport, $awayName);
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
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>1</td> <td> | </td> <td>2</td> <td> | </td> <td>3</td> <td> | </td> <td>4</td> <td> | </td> <td>5</td> <td> || </td> <td> T </td><td></tr>");
	printf("<tr>	<td>----</td> <td>-</td> <td>----</td> <td>-</td> <td>----</td> <td>-</td> <td>----</td> <td>-</td> <td>----</td> <td>-</td> <td>----</td> <td>-</td> <td>----</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td> </tr>", $awayTeam, $as1Score, $as2Score, $as3Score, $as4Score, $as5Score, $aTotal);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td> </tr></table><br><br>", $homeTeam, $hs1Score, $hs2Score, $hs3Score, $hs4Score, $hs5Score, $hTotal);
	
	/*
	#########################
	#						#
	#		Live Game Info	#
	#						#
	#########################
	*/
	if($comp != 1){
		$sql = "SELECT period AS set_, info_1 AS serving, info_2 AS hometo, info_3 AS awayto FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id=s.id WHERE lg.schedule_id = '$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$set = $row->set_;
			$serving = $row->serving;
			$hometo = $row->hometo;
			$awayto = $row->awayto;
			printf("Current Set: %s<br>", $set);
			printf("Serving: %s<br><br><br>", $serving);
		}
	}
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT * FROM volleyball_pbp AS pbp JOIN schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	$pbpArray = array(array("Set 1"), array("Set 2"), array("Set 3"), array("Set 4"), array("Set 5"));

	
	while($row = $query->fetchObject()){
		$text = $row->text;
		$set = $row->set_;
		$pbpText = $set . " | " . $text;

		$pbpArray[intval($set)-1][] = $pbpText;
	
	}
	
	
	if($comp == 1){
		for($i = 0; $i<count($pbpArray); $i++){
			$set = $pbpArray[$i][0];
			?> <input type="checkbox" name="<?php echo $set?>" id="<?php echo $set?>" onclick="rtoggle('<?php echo $set?>')"><label for="<?php echo $set?>"><b><?php echo $set?> [+]</b></label><br><br>
			<div class = "<?php echo $set?> hidden"><?php
			foreach($pbpArray[$i] as $entry){
				$entry = explode("|", $entry, 3);
				printf("<div  style='float: left;'>%s | %s</div>", $entry[0], $entry[1]);
				printf("<div  style='float: right;'>%s</div><br><br>", $entry[2]);
			}
			printf("</div>");
			printf("<br>");
		}
		printf("<br>-END OF GAME-<br><br><br>");
	}else{
		$newestQuarter = true;
		for($i = count($pbpArray); $i > 0; $i--){
			if(count($pbpArray[$i-1]) > 1){//Only display if entries
				$set = $pbpArray[$i-1][0];
				?> <input type="checkbox" name="<?php echo $set?>" id="<?php echo $set?>" onclick="rtoggle('<?php echo $set?>')"><label for="<?php echo $set?>"><b><?php echo $set?> [+]</b></label><br><br>
				<?php if(!$newestQuarter){ $set = $set . " hidden";}?>
				<div class = "<?php echo $set?>"><?php
				for($j = count($pbpArray[$i-1]); $j > 1; $j--){
					$entry = explode("|", $pbpArray[$i-1][$j-1], 3);
					printf("<div  style='float: left;'>%s | %s</div>", $entry[0], $entry[1]);
					printf("<div  style='float: right;'>%s</div><br><br>", $entry[2]);
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
	
	$sql = "SELECT rp.number AS num, rp.name AS name, vbs.kills AS kills, vbs.assists AS assists, vbs.aces AS aces, vbs.attack_errors AS attack_errors, vbs.service_errors AS service_errors , vbs.blocks AS blocks , vbs.digs AS digs 
	FROM volleyball_stats AS vbs JOIN schedule AS s ON vbs.game=s.id JOIN roster_player AS rp ON vbs.player=rp.id JOIN roster_teams AS t ON s.team_id=t.id JOIN roster_schools h ON rp.school=h.id
	WHERE vbs.game = '$gameID' AND h.short_name = '$homeTeam' AND t.urlName = '$sport' ORDER BY cast(num as unsigned)";
	$query = $db->prepare($sql);
	$query->execute();
	
	$statArray = array(array(["Players"],["","Kills", "Assts", "Aces", "A/ERR", "S/ERR", "Blocks", "Digs"]));
	printf("STATS<hr>");
	printf("<br>$homeTeam<br>");
	printf('<table style = "border-spacing: 9px">');
	while($row = $query->fetchObject()){
		$name = $row->name;
		$num = $row->num;
		$kills = $row->kills;
		$assists = $row->assists;
		$aces = $row->aces;
		$attack_errors = $row->attack_errors;
		$service_errors = $row->service_errors;
		$blocks = $row->blocks;
		$digs = $row->digs;
		$urlname = $name;
		$name = explode(" ", $name);
		$name[0] = str_split($name[0])[0] . ".";
		$name = implode(" ", $name);
		$url =  "<a href='../teams/player.php?player=$urlname&school=$homeTeam' class='schedule-game'>$num $name</a>";
		if($kills != '' or $assists != '' or $aces != '' or $attack_errors != '' or $service_errors != ''or $blocks != '' or $digs != ''){
			$statArray[0][] = [$url, $kills, $assists, $aces, $attack_errors, $service_errors, $blocks, $digs];
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
	
	$sql = "SELECT rp.number AS num, rp.name AS name, vbs.kills AS kills, vbs.assists AS assists, vbs.aces AS aces, vbs.attack_errors AS attack_errors, vbs.service_errors AS service_errors , vbs.blocks AS blocks , vbs.digs AS digs 
	FROM volleyball_stats AS vbs JOIN schedule AS s ON vbs.game=s.id JOIN roster_player AS rp ON vbs.player=rp.id JOIN roster_teams AS t ON s.team_id=t.id JOIN roster_schools h ON rp.school=h.id
	WHERE vbs.game = '$gameID' AND h.short_name = '$awayTeam' AND t.urlName = '$sport' ORDER BY cast(num as unsigned)";
	$query = $db->prepare($sql);
	$query->execute();
	
	$statArray = array(array(["Players"],["","Kills", "Assts", "Aces", "A/ERR", "S/ERR", "Blocks", "Digs"]));
	printf("<br>$awayTeam<br>");
	printf('<table style = "border-spacing: 9px">');
	while($row = $query->fetchObject()){
		$name = $row->name;
		$num = $row->num;
		$kills = $row->kills;
		$assists = $row->assists;
		$aces = $row->aces;
		$attack_errors = $row->attack_errors;
		$service_errors = $row->service_errors;
		$blocks = $row->blocks;
		$digs = $row->digs;
		$urlname = $name;
		$name = explode(" ", $name);
		$name[0] = str_split($name[0])[0] . ".";
		$name = implode(" ", $name);
		$url =  "<a href='../teams/player.php?player=$urlname&school=$awayTeam' class='schedule-game'>$num $name</a>";
		if($kills != '' or $assists != '' or $aces != '' or $attack_errors != '' or $service_errors != ''or $blocks != '' or $digs != ''){
			$statArray[0][] = [$url, $kills, $assists, $aces, $attack_errors, $service_errors, $blocks, $digs];
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