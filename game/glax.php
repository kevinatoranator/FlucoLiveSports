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
$phpURL = "glaxmanager.php?gameID=".$gameID;

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
	$half = 1;
	$homeID = 0;
	$comp = 0;
    
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
	
	$sqlsport = "SELECT s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id as hNum, s.away_id, s.team_id, t.formattedName, 
		gl.home_half1 AS hh1, gl.home_half2 AS hh2, gl.home_ot AS hot, gl.home_total AS ht, gl.away_half1 AS ah1, gl.away_half2 AS ah2, gl.away_ot AS aot, gl.away_total AS at, gl.completed AS cmp
		FROM glax AS gl JOIN schedule AS s ON gl.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE s.id='$gameID'";
	
	if($sport=="glax"){
		$minutes = 25;
		$seconds = 0;
		$maxMin = 25;
	}else if($sport=="jvglax"){
		$minutes = 25;
		$seconds = 0;
		$maxMin = 25;
	}
	
	$query = $db->prepare($sqlsport);
	$query->execute();
	while($row = $query->fetchObject()){
		printf("<h5>%s</h5><br>", $row->formattedName);
		
		$homeID = $row->hNum;
			
		$homeTeam = $row->home;
		$awayTeam = $row->away;
			
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
	
	
	/*
	#########################
	#						#
	#		SCORE TABLE		#
	#						#
	#########################
	*/
	
	printf("<table><tr>	<td>Team</td> <td> | </td> <td>Half 1</td> <td> | </td> <td>Half 2</td> <td> | </td> <td>OT</td> <td> | </td> <td> Total </td></tr>");
	printf("<tr>	<td>----</td> <td>---</td> <td>------</td> <td>---</td> <td>------</td> <td>---</td> <td>--</td> <td>---</td> <td>------</td></tr>");
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr>",$homeTeam, $hh1Score, $hh2Score, $hOTScore, $hTotal);
	printf("<tr><td>%s</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td> <td> | </td> <td>%d</td></tr></table><br><br>", $awayTeam, $ah1Score, $ah2Score, $aOTScore, $aTotal);
	
	/*
	#########################
	#						#
	#		PLAY-BY-PLAY	#
	#						#
	#########################
	*/
	
	$sql = "SELECT pbp.id AS pbpID, pbp.text AS text, pbp.half AS half, pbp.time AS tme FROM glax_pbp AS pbp JOIN schedule AS s ON pbp.game_id=s.id WHERE pbp.game_id = '$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	
	$pbpArray = array(array("Half 1"), array("Half 2"), array("Overtime"));
	
	while($row = $query->fetchObject()){
		$text = $row->text;
		$half = $row->half;
		$time = $row->tme;
		$pbpText = $half . " " . $time . " | " . $text;
		if(str_contains($text, "Goal")){
			$pbpText = "<b>$pbpText</b>";
		}
		switch($half){
			case "H1":
				$half = 0;
				break;
			case "H2":
				$half = 1;
				break;
			case "OT":
				$half = 2;
				break;
		}
		$pbpArray[$half][] = $pbpText;
	
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