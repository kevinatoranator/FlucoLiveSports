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
$phpURL = "track.php?gameID=".$gameID;
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
	$comp = 0;
	
	$homeTeam = "";
	$awayTeam = "";
	
    
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

	
	
	$url = "https://va.milesplit.com/api/v1/meets/676424/performances?isMeetPro=0&resultsId=1182311&fields=id%2CmeetId%2CmeetName%2CteamId%2CvideoId%2CteamName%2CathleteId%2CfirstName%2ClastName%2Cgender%2CgenderName%2CdivisionId%2CdivisionName%2CmeetResultsDivisionId%2CresultsDivisionId%2CageGroupName%2CgradYear%2CeventName%2CeventCode%2CeventDistance%2CeventGenreOrder%2Cround%2CroundName%2Cheat%2Cunits%2Cmark%2Cplace%2CwindReading%2CprofileUrl%2CteamProfileUrl%2CperformanceVideoId%2CteamLogo%2CstatusCode&m=GET";
	$result = file_get_contents($url);
	$newjson = json_decode($result);
	
	$hdashg = [["100m", "girls"]];
	$hdashb = [["100m", "boys"]];
	
	foreach($newjson->data as $entry){
		if($entry->eventCode == "100m" and $entry->genderName=="Girls"){
			$hdashg[] = [$entry->mark, "$entry->firstName $entry->lastName ($entry->teamName)"];
		}else if($entry->eventCode == "100m" and $entry->genderName=="Boys"){
			$hdashb[] = [$entry->mark, "$entry->firstName $entry->lastName ($entry->teamName)"];
		}
	}
	//ksort($hdashg);
	//ksort($hdashb);
	foreach($hdashg as $entry){
		echo $entry[0] . " - " . $entry[1] . "<br>";
	}
	echo "<hr>";
	foreach($hdashb as $entry){
		echo $entry[0] . " - " . $entry[1] . "<br>";
	}
	echo "<script>console.log($result);</script>";
?>

</body>