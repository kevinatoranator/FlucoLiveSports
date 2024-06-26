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
	$maxMin = 7;
	$inning = 1;
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
	
	if($sport=="softball" or  $sport=="baseball" or $sport=="jvsoftball" or  $sport=="jvbaseball"){
		$sqlsport = "SELECT s.notes AS info, s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id, s.team_id, t.formattedName,
		h.formal_name AS homeName, a.formal_name AS awayName,
		bb.home_i1 AS hi1, bb.home_i2 AS hi2, bb.home_i3 AS hi3, bb.home_i4 AS hi4, bb.home_i5 AS hi5, bb.home_i6 AS hi6, bb.home_i7 AS hi7, bb.home_ex AS hex, bb.home_total AS ht, bb.home_hits AS hh, bb.home_errors AS herr,
		bb.away_i1 AS ai1, bb.away_i2 AS ai2, bb.away_i3 AS ai3, bb.away_i4 AS ai4, bb.away_i5 AS ai5, bb.away_i6 AS ai6, bb.away_i7 AS ai7, bb.away_ex AS aex, bb.away_total AS at, bb.away_hits AS ah, bb.away_errors AS aerr, bb.completed AS cmp
		FROM batball AS bb JOIN schedule AS s ON bb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
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
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>1</td> <td> | </td> <td>2</td> <td> | </td> <td>3</td> <td> | </td> <td>4</td> <td> | </td> <td>5</td> <td> | </td> <td>6</td> <td> | </td> <td>7</td> <td> | </td> <td>Ex</td> <td> || </td> <td> R </td><td> | </td> <td> H </td><td> | </td> <td> E </td></tr>");
	printf("<tr>	<td>----</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td> <td>-</td> <td>--</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr>", $awayTeam, $ai1Score, $ai2Score, $ai3Score, $ai4Score, $ai5Score, $ai6Score, $ai7Score, $aexScore, $aTotal, $ahits, $aerr);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> </td> <td>%d</td> <td> | </td> <td>%d</td> <td> || </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr></table><br><br>", $homeTeam, $hi1Score, $hi2Score, $hi3Score, $hi4Score, $hi5Score, $hi6Score, $hi7Score, $hexScore, $hTotal, $hhits, $herr);
	
	
	/*
	#########################
	#						#
	#		Live Game Info	#
	#						#
	#########################
	*/
	if($comp != 1){
		$sql = "SELECT game_time AS outs, info_2 AS balls, info_3 AS strikes, info_4 AS bases, info_5 AS atBat FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id=s.id WHERE lg.schedule_id = '$gameID'";
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
	
	foreach($pbpArray as $row){
		foreach($row as $entry){
			printf($entry . "<br>");
		}
		printf("<br>");
	}
	
	
	if($comp == 1){
		printf("<br>-END OF GAME-");
	}
	

?>

</body>