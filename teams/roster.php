<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../stylesheet.css">
</head>
<body>
<script>
	function rtoggle(id){
		var checkBox = document.getElementById(id);
		var displayr = document.getElementsByClassName(id)[0];
		if(checkBox.checked == false){
			displayr.style.display = "block";
		}else{
			displayr.style.display = "none";
		}
	}
</script>
<?php
	
	function getRoster($db, $roster, $year){
		$returnRoster = [];
		$sql = "SELECT roster_player.name, roster_player.number, roster_player.season FROM roster_player INNER JOIN roster_teams ON roster_player.team_id=roster_teams.id WHERE roster_teams.urlName='$roster' AND roster_player.season='$year'
		ORDER BY (CASE WHEN cast(roster_player.number as unsigned) = 0 THEN 999997 ELSE cast(roster_player.number as unsigned) END)";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$returnRoster[] = sprintf("%s | %s<br>", $row->number, $row->name);
		}
		
		return $returnRoster;
	}

	include '../include/database.php';
	
	$roster = $_GET['sport'];
	$sql = "SELECT formattedName FROM roster_teams WHERE roster_teams.urlName='$roster'";
	
	 try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	$query = $db->prepare($sql);
	$query->execute();
	$i = $query->fetchObject();
	$sportFormat = $i->formattedName;
	
	$sql = "SELECT * FROM standings JOIN roster_teams ON standings.sport_id=roster_teams.id WHERE roster_teams.urlName='$roster' AND standings.season='2024'";
	$query = $db->prepare($sql);
	$query->execute();
	$i = $query->fetchObject();
	$wins = 0;
	$losses = 0;
	$ties = 0;
	if($i){
		$wins = $i->wins;
		$losses = $i->losses;
		$ties = $i->ties;
	}
?>


<!--Schedule Header-->

    <br>
    <div class="flex justify-between">
        <a href ="./index.php">Teams</a>
        <a href ="../index.php">Schedule</a>
    </div>
    <br>
    <div class="flex justify-between">
        <a href ="../standings/index.php">Standings</a>  <a href ="/schedule/district/district.php">District Schedule</a><!--BROKEN LINK-->
    </div>
	<br>
	<div class="flex justify-between">
        <div></div><b> <?php echo '<u>' . $sportFormat . '</u>' ?></b><div></div>
    </div>
	<div class="flex justify-between">
        <div></div><a href = "../standings/standings.php?sport=<?php echo $roster?>" class='schedule-game'><b> <?php printf("%s-%s-%s", $wins, $losses, $ties); ?></b></a><div></div>
    </div>
	
	
<br>

<!-- Results -->
<br>
<b> RESULTS </b>

<br><br>

<!-- 2024 -->
<input type="checkbox" name="g2024" id="g2024" onclick="rtoggle('g2024')"><label for="g2024"><b>2024 [+]</b></label>
<br><br>
<div class = "g2024">
<?php
	$gamedb = "";
	if($roster == "jvblax" or $roster == "blax"){
		$gamedb = "blax";
	}else if($roster == "jvglax" or $roster == "glax"){
		$gamedb = "glax";
	}else if($roster == "jvbsoccer" or $roster == "bsoccer" or $roster == "jvgsoccer" or $roster == "gsoccer"){
		$gamedb = "soccer";
	}else if($roster == "jvbaseball" or $roster == "baseball" or $roster == "jvsoftball" or $roster == "softball"){
		$gamedb = "batball";
	}else if($roster == "jvfhockey" or $roster == "fhockey"){
		$gamedb = "field_hockey";
	}else if($roster == "jvfootball" or $roster == "football"){
		$gamedb = "football";
	}else if($roster == "jvbbball" or $roster == "bbball" or $roster == "jvgbball" or $roster == "gbball"){
		$gamedb = "basketball";
	}else if($roster == "jvvball" or $roster == "vball"){
		$gamedb = "volleyball";
	}

	if($gamedb != ""){
		$sql = "SELECT s.game_date, home_total, away_total, r.urlName AS name, s.game_date, h.formal_name AS home, a.formal_name AS away, s.id AS id FROM $gamedb RIGHT JOIN schedule AS s ON $gamedb.schedule_id=s.id INNER JOIN roster_teams AS r ON s.team_id=r.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE r.urlName='$roster' AND (h.formal_name='Fluvanna County' OR a.formal_name='Fluvanna County') AND s.season='2024' ORDER BY s.game_date";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$hscore = $row->home_total;
			$ascore = $row->away_total;
			$id = $row->id;
			$sdate = date("D m/d", strtotime($row->game_date));
			?><a href="../game/<?php echo $gamedb?>.php?gameID=<?php echo $id?>" class='schedule-game'>
			<?php
			if($hscore > $ascore){
				printf("%s <b>%s %s</b>-%s %s<br>", $sdate, $row->home, $hscore, $ascore, $row->away);
			}else if($hscore < $ascore){
				printf("%s %s %s-<b>%s %s</b><br>", $sdate, $row->home, $hscore, $ascore, $row->away);
			}else{
				printf("%s %s %s-%s %s<br>", $sdate, $row->home, $hscore, $ascore, $row->away);
			}
			?></a><?php
		}
	}
?>
</div><br><br>

<!-- 2023 -->
<input type="checkbox" name="g2023" id="g2023" onclick="rtoggle('g2023')"><label for="g2023"><b>2023 [+]</b></label>
<br><br>
<div class = "g2023 hidden">
<?php

	if($gamedb != ""){
		$sql = "SELECT s.game_date, home_total, away_total, r.urlName AS name, s.game_date, h.formal_name AS home, a.formal_name AS away, s.id AS id FROM $gamedb RIGHT JOIN schedule AS s ON $gamedb.schedule_id=s.id INNER JOIN roster_teams AS r ON s.team_id=r.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE r.urlName='$roster' AND (h.formal_name='Fluvanna County' OR a.formal_name='Fluvanna County') AND s.season='2023' ORDER BY s.game_date";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$hscore = $row->home_total;
			$ascore = $row->away_total;
			$id = $row->id;
			$sdate = date("D m/d", strtotime($row->game_date));
			?><a href="../game/<?php echo $gamedb?>.php?gameID=<?php echo $id?>" class='schedule-game'>
			<?php
			if($hscore > $ascore){
				printf("%s <b>%s %s</b>-%s %s<br>", $sdate, $row->home, $hscore, $ascore, $row->away);
			}else if($hscore < $ascore){
				printf("%s %s %s-<b>%s %s</b><br>", $sdate, $row->home, $hscore, $ascore, $row->away);
			}else{
				printf("%s %s %s-%s %s<br>", $sdate, $row->home, $hscore, $ascore, $row->away);
			}
			?></a><?php
		}
	}
?>
</div>
<br><br>
<input type="checkbox" name="g2021" id="g2021" onclick="rtoggle('g2021')"><label for="g2021"><b>2021 [+]</b></label>
<br><br>

<!-- 2021 -->
<div class = "g2021 hidden">
<?php

	if($roster == "jvblax" or $roster == "blax"){
		$gamedb = "games_boyslacrosse";
	}else if($roster == "jvglax" or $roster == "glax"){
		$gamedb = "games_girlslacrosse";
	}else if($roster == "jvbsoccer" or $roster == "bsoccer" or $roster == "jvgsoccer" or $roster == "gsoccer"){
		$gamedb = "games_soccer";
	}else if($roster == "jvbaseball" or $roster == "baseball" or $roster == "jvsoftball" or $roster == "softball"){
		$gamedb = "games_batball";
	}else if($roster == "jvfhockey" or $roster == "fhockey"){
		$gamedb = "games_fieldhockey";
	}else if($roster == "jvfootball" or $roster == "football"){
		$gamedb = "games_football";
	}else if($roster == "jvbbball" or $roster == "bbball" or $roster == "jvgbball" or $roster == "gbball"){
		$gamedb = "games_basketball";
	}else if($roster == "jvvball" or $roster == "vball"){
		$gamedb = "games_volleyball";
	}

	if($gamedb != ""){
		$sql = "SELECT s.game_date, totalScoreHome, totalScoreAway, r.urlName AS name, s.game_date, h.formal_name AS home, a.formal_name AS away FROM $gamedb g INNER JOIN schedule_schedule AS s ON g.schedGame_id=s.id INNER JOIN roster_teams AS r ON s.team_id=r.id JOIN roster_schools a ON s.awayTeam_id=a.id JOIN roster_schools h ON s.homeTeam_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE r.urlName='$roster' AND (h.formal_name='Fluvanna County' OR a.formal_name='Fluvanna County') AND s.season='2021' ORDER BY s.game_date asc";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$hscore = $row->totalScoreHome;
			$ascore = $row->totalScoreAway;
			$sdate = date("D m/d", strtotime($row->game_date));
			if($hscore > $ascore){
				printf("%s <b>%s %s</b>-%s %s<br>", $sdate, $row->home, $row->totalScoreHome, $row->totalScoreAway, $row->away);
			}else if($hscore < $ascore){
				printf("%s %s %s-<b>%s %s</b><br>", $sdate, $row->home, $row->totalScoreHome, $row->totalScoreAway, $row->away);
			}else{
				printf("%s %s %s-%s %s<br>", $sdate, $row->home, $row->totalScoreHome, $row->totalScoreAway, $row->away);
			}
		}
	}
?>
</div>
<!--Roster Body-->

<br>
<b> ROSTERS </b>

<!--2024-->
<br><br>
<input type="checkbox" name="r2024" id="r2024" onclick="rtoggle('r2024')"><label for="r2024"><b>2024 [+]</b></label>
<br><br>
<div class = "r2024">
<?php
	$playerRoster = getRoster($db, $roster, 2024);
	foreach($playerRoster as $player){
		printf($player);
	}
	if(count($playerRoster) == 0){
		printf("No Roster Available");
	}
?>
</div>

<!--2023-->
<br><br>
<input type="checkbox" name="r2023" id="r2023" onclick="rtoggle('r2023')"><label for="r2023"><b>2023 [+]</b></label>
<br><br>
<div class = "r2023 hidden">
<?php
	$playerRoster = getRoster($db, $roster, 2023);
	foreach($playerRoster as $player){
		printf($player);
	}
	if(count($playerRoster) == 0){
		printf("No Roster Available");
	}
?>
</div>

<!--2022-->
<br><br>
<input type="checkbox" name="r2022" id="r2022" onclick="rtoggle('r2022')"><label for="r2022"><b>2022 [+]</b></label>
<br><br>
<div class = "r2022 hidden">
<?php
	$playerRoster = getRoster($db, $roster, 2022);
	foreach($playerRoster as $player){
		printf($player);
	}
	if(count($playerRoster) == 0){
		printf("No Roster Available");
	}
?>
</div>

<!--2021-->
<br><br>
<input type="checkbox" name="r2021" id="r2021" onclick="rtoggle('r2021')"><label for="r2021"><b>2021 [+]</b></label>
<br><br>
<div class = "r2021 hidden">
<?php
	$playerRoster = getRoster($db, $roster, 2021);
	foreach($playerRoster as $player){
		printf($player);
	}
	if(count($playerRoster) == 0){
		printf("No Roster Available");
	}
?>
</div>
</body>