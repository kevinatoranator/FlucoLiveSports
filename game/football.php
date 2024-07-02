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


function updateScore($hq1, $hq2, $hq3, $hq4, $hOT, $aq1, $aq2, $aq3, $aq4, $aOT, $gameID, $db){
		$hTotal = $hq1 + $hq2 + + $hq3 + $hq4 + $hOT;
		$aTotal = $aq1 + $aq2 + $aq3 + $aq4 + $aOT;
		
		$sqls = "UPDATE football SET home_q1 = '$hq1', home_q2 = '$hq2', home_q3 = '$hq3', home_q4 = '$hq4', home_ot = '$hOT', home_total = '$hTotal', away_q1 = '$aq1', away_q2 = '$aq2', away_q3 = '$aq3', away_q4 = '$aq4', away_ot = '$aOT', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		$query = $db->prepare($sqls);
		$query->execute();

}

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
	
	$sqlsport = "SELECT s.notes AS info, s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id, s.team_id, t.formattedName,
		h.formal_name AS homeName, a.formal_name AS awayName,
		fb.home_q1 AS hq1, fb.home_q2 AS hq2, fb.home_q3 AS hq3, fb.home_q4 AS hq4, fb.home_ot AS hot, fb.home_total AS ht, 
		fb.away_q1 AS aq1, fb.away_q2 AS aq2, fb.away_q3 AS aq3, fb.away_q4 AS aq4, fb.away_ot AS aot, fb.away_total AS at, fb.completed AS cmp
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
		printf("<h5>%s</h5><br>", $row->formattedName);
		
		$homeID = $row->hNum;
		
		$info = $row->info;
			
		$homeTeam = $row->home;
		$awayTeam = $row->away;
		
		$homeName = $row->homeName;
		$awayName = $row->awayName;
			
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
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>Qrtr 1</td> <td> | </td> <td>Qrtr 2</td> <td> | </td> <td>Qrtr 3</td> <td> | </td> <td>Qrtr 4</td> <td> | </td> <td>OT</td> <td> | </td> <td> Total </td></tr>");
	printf("<tr>	<td>----</td> <td>-</td> <td>------</td> <td>-</td> <td>------</td> <td>-</td>  <td>------</td> <td>-</td> <td>------</td> <td>-</td> <td>--</td> <td>-</td> <td>------</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr>",$homeTeam, $hq1Score, $hq2Score, $hq3Score, $hq4Score, $hOTScore, $hTotal);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr></table><br><br>", $awayTeam, $aq1Score, $aq2Score, $aq3Score, $aq4Score, $aOTScore, $aTotal);
	
	
	/*
	#########################
	#						#
	#		Live Game Info	#
	#						#
	#########################
	*/
	if($comp != 1){
		$sql = "SELECT period AS quarter, game_time AS time_, info_1 AS poss, info_2 AS tohome, info_3 AS toaway, info_4 AS sof, info_5 AS yardline, info_6 AS ytg FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id=s.id WHERE lg.schedule_id = '$gameID'";
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
			printf("Quarter: %s Time: %s Poss: %s<br>", $qrtr, $time, $poss);
			printf("Home TOs: %s<br>", $tohome);
			printf("Away TOs: %s<br><br>", $toaway);
		}
	}
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.quarter AS qrtr, pbp.time AS tme FROM blax_pbp AS pbp JOIN schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	
	$pbpArray = array(array("Quarter 1"), array("Quarter 2"), array("Quarter 3"), array("Quarter 4"), array("Overtime"));
	
	while($row = $query->fetchObject()){
		$text = $row->text;
		$qrtr = $row->qrtr;
		$time = $row->tme;
		$pbpText = $qrtr . " " . $time . " | " . $text;
		if(str_contains($text, "Touchdown") or str_contains($text, "Field goal") or str_contains($text, "Safety") or str_contains($text, "Extra point") or str_contains($text, "2-point")){
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
		$pbpArray[$qrtr][] = $pbpText;
	
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