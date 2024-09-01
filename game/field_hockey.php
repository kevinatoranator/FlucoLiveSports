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
$phpURL = "fhockey.php?gameID=".$gameID;

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
		fh.home_quarter1 AS hq1, fh.home_quarter2 AS hq2, fh.home_quarter3 AS hq3, fh.home_quarter4 AS hq4, fh.home_ot AS hot, fh.home_total AS ht, 
		fh.away_quarter1 AS aq1, fh.away_quarter2 AS aq2, fh.away_quarter3 AS aq3, fh.away_quarter4 AS aq4, fh.away_ot AS aot, fh.away_total AS at, fh.completed AS cmp
		FROM field_hockey AS fh JOIN schedule AS s ON fh.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	
	if($sport=="fhockey"){
		$minutes = 15;
		$seconds = 0;
		$maxMin = 15;
	}else if($sport=="jvfhockey"){
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
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.quarter AS qrtr, pbp.time AS tme FROM field_hockey_pbp AS pbp JOIN schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	
	$pbpArray = array(array("Quarter 1"), array("Quarter 2"), array("Quarter 3"), array("Quarter 4"), array("Overtime"));
	
	while($row = $query->fetchObject()){
		$text = $row->text;
		$qrtr = $row->qrtr;
		$time = $row->tme;
		$pbpText = $time . " | " . $text;
		if(str_contains($text, "Goal")){
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
	
	$sql = "SELECT rp.number AS num, rp.name AS name, fhs.goals AS goals, fhs.assists AS assists, fhs.shots AS shots, fhs.shots_on_goal AS shots_on_goal, fhs.saves AS saves , fhs.goals_allowed AS goals_allowed 
	FROM field_hockey_stats AS fhs JOIN schedule AS s ON fhs.game=s.id JOIN roster_player AS rp ON fhs.player=rp.id JOIN roster_teams AS t ON s.team_id=t.id WHERE fhs.game = '$gameID' AND t.urlName = '$sport' AND rp.season = 2023";
	$query = $db->prepare($sql);
	$query->execute();
	
	$statArray = array(array(["Players"], ["", "Goals", "Assts", "Shots", "SOG"]), array(["Goalies"], ["" , "", "", "Saves", "G/A"]));
	printf("STATS<hr>");
	printf('<table style = "border-spacing: 9px">');
	while($row = $query->fetchObject()){
		$name = $row->name;
		$num = $row->num;
		$goals = $row->goals;
		$assists = $row->assists;
		$shots = $row->shots;
		$shots_on_goal = $row->shots_on_goal;
		$saves = $row->saves;
		$goals_allowed = $row->goals_allowed;
		$name = explode(" ", $name);
		$name[0] = str_split($name[0])[0] . ".";
		$name = implode(" ", $name);
		if($goals != '' or $assists != '' or $shots != '' or $shots_on_goal != ''){
			$statArray[0][] = [$num . ' ' . $name, $goals, $assists, $shots, $shots_on_goal];
		}else{
			$statArray[1][] = [$num . ' ' . $name, '', '', $saves, $goals_allowed];
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