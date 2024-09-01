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
$phpURL = "vball.php?gameID=".$gameID;
?>


<div class="flex justify-between">
        <a href="../index.php">Return to Schedule</a>
        <a href='<?php echo $phpURL?>'>Reload</a>
    </div>


<!--Schedule Body-->

<?php
	include '../include/database.php';

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
	
	$sqlsport = "";
	
	if($sport=="vball" or  $sport=="jvvball"){
		$sqlsport = "SELECT s.notes AS info, s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id, s.team_id, t.formattedName,
		h.formal_name AS homeName, a.formal_name AS awayName,
		vb.home_set1 AS hs1, vb.home_set2 AS hs2, vb.home_set3 AS hs3, vb.home_set4 AS hs4, vb.home_set5 AS hs5, vb.home_total AS ht,
		vb.away_set1 AS as1, vb.away_set2 AS as2, vb.away_set3 AS as3, vb.away_set4 AS as4, vb.away_set5 AS as5, vb.away_total AS at, vb.completed AS cmp
		FROM volleyball AS vb JOIN schedule AS s ON vb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	}
	
	$query = $db->prepare($sqlsport);
	$query->execute();
	while($row = $query->fetchObject()){
		printf("<h5>%s</h5><br>", $row->formattedName);
		
		$homeID = $row->hNum;
		
		$info = $row->info;
			
		$homeTeam = $row->home;
		$awayTeam = $row->away;
		
		$homeName = $row->homeName;
		$awayName = $row->awayName;
		
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
		
		printf("<b>%s vs. %s</b><br>", $homeName, $awayName);
		printf("<p>%s</p><br>", $info);
	}
	
	
	/*
	#########################
	#						#
	#		SCORE TABLE		#
	#						#
	#########################
	*/
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>1</td> <td> | </td> <td>2</td> <td> | </td> <td>3</td> <td> | </td> <td>4</td> <td> | </td> <td>5</td> <td> || </td> <td> T </td><td></tr>");
	printf("<tr>	<td>----</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td> </tr>", $homeTeam, $hs1Score, $hs2Score, $hs3Score, $hs4Score, $hs5Score, $hTotal);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td> </tr></table><br><br>", $awayTeam, $as1Score, $as2Score, $as3Score, $as4Score, $as5Score, $aTotal);
	
	
	/*
	#########################
	#						#
	#		Live Game Info	#
	#						#
	#########################
	*/
	//if($comp != 1){
		/*$sql = "SELECT game_time AS outs, info_2 AS balls, info_3 AS strikes, info_4 AS bases, info_5 AS atBat FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id=s.id WHERE lg.schedule_id = '$gameID'";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$atBat = $row->atBat;
			$balls = $row->balls;
			$strikes = $row->strikes;
			$bases = $row->bases;
			$outs = $row->outs;
			printf("At Bat: %s %s-%s<br>", $atBat, $balls, $strikes);
			printf("Outs: %s<br>", $outs);
			printf("Bases: %s<br><br>", $bases);
		}*/
	//}
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
	
	foreach($pbpArray as $row){
		foreach($row as $entry){
			printf($entry . "<br>");
		}
		printf("<br>");
	}
	
	
	if($comp == 1){
		printf("<br>-END OF GAME-");
	}
	
	/*
	#########################
	#						#
	#		Stats			#
	#						#
	#########################
	*/
	
	$sql = "SELECT rp.number AS num, rp.name AS name, vbs.kills AS kills, vbs.assists AS assists, vbs.aces AS aces, vbs.attack_errors AS attack_errors, vbs.service_errors AS service_errors , vbs.blocks AS blocks , vbs.digs AS digs 
	FROM volleyball_stats AS vbs JOIN schedule AS s ON vbs.game=s.id JOIN roster_player AS rp ON vbs.player=rp.id JOIN roster_teams AS t ON s.team_id=t.id WHERE vbs.game = '$gameID' AND t.urlName = '$sport'";
	$query = $db->prepare($sql);
	$query->execute();
	
	$statArray[] = ["", "Kills", "Assts", "Aces", "A/ERR", "S/ERR", "Blocks", "Digs"];
	printf("STATS<hr>");
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
		$name = explode(" ", $name);
		$name[0] = str_split($name[0])[0] . ".";
		$name = implode(" ", $name);
		if($kills != '' or $assists != '' or $aces != '' or $attack_errors != '' or $service_errors != ''or $blocks != '' or $digs != ''){
			$statArray[] = [$num . ' ' . $name, $kills, $assists, $aces, $attack_errors, $service_errors, $blocks, $digs];
		}
	}
	
	//Table format
	for($j = 0; $j < count($statArray); $j++){
		if($j%2){//alternate colors doesn't work well with table
			printf('<tr>');
		}else{
			printf('<tr">');
		}
		for($i = 0; $i < count($statArray[$j]); $i++){
			if($i == 0){
				printf('<td style="text-align: left">%s</td>', $statArray[$j][$i]);
			}else{
				printf('<td style="text-align: right">%s</td>', $statArray[$j][$i]);
			}
		}
		printf('</tr>');
	}
	printf('<tr></tr>');
	printf("</table>");

?>

</body>