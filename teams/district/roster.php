<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../../stylesheet.css">
</head>
<body>
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
<?php
	
	function getRoster($db, $roster, $year){
		$returnRoster = [];
		$sql = "SELECT roster_player.name, roster_player.number, roster_player.season FROM roster_player INNER JOIN roster_teams ON roster_player.team_id=roster_teams.id WHERE roster_teams.urlName='$roster' AND roster_player.season='$year'
		ORDER BY (CASE WHEN roster_player.number = 'Head Coach' THEN 999997 WHEN cast(roster_player.number as unsigned) = 0 THEN 999998 ELSE cast(roster_player.number as unsigned) END)";
		$query = $db->prepare($sql);
		$query->execute();
		$staff = [];
		while($row = $query->fetchObject()){
			if($row->number != "Head Coach" and $row->number != "Assistant Coach" and $row->number != "Assistant Coaches" and $row->number != "Manager" and $row->number != "Managers"){
				$returnRoster[] = sprintf("%s | %s<br>", $row->number, $row->name);
			}else{
				$staff[] = sprintf("%s | %s<br>", $row->number, $row->name);
			}
		}
		
		return array_merge($returnRoster, $staff);
	}

	include '../../include/database.php';
	
	$roster = $_GET['sport'];
	$school = $_GET['school'];
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
	
	$sql = "SELECT * FROM standings JOIN roster_teams ON standings.sport_id=roster_teams.id JOIN roster_schools ON standings.school_id=roster_schools.id WHERE roster_teams.urlName='$roster' AND standings.season='2024' AND roster_schools.short_name='$school'";
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
	<?php 
	include '../../include/header.php';
	?>
	<br>
	<div class="flex justify-between">
        <div></div><b> <?php echo '<u>' . $school . " " . $sportFormat . '</u>' ?></b><div></div>
    </div>
	<div class="flex justify-between">
        <div></div><a href = "../../standings/standings.php?sport=<?php echo $roster?>" class='schedule-game'><b> <?php printf("%s-%s-%s", $wins, $losses, $ties); ?></b></a><div></div>
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
		$sql = "SELECT s.game_date, home_total, away_total, h.formal_name AS home, a.formal_name AS away, s.id AS id FROM $gamedb RIGHT JOIN schedule AS s ON $gamedb.schedule_id=s.id INNER JOIN roster_teams AS r ON s.team_id=r.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE r.urlName='$roster' AND (h.short_name='$school' OR a.short_name='$school') AND s.season='2024' ORDER BY s.game_date";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$hscore = $row->home_total;
			$ascore = $row->away_total;
			$id = $row->id;
			$sdate = date("D m/d", strtotime($row->game_date));
			?><a href="../../game/<?php echo $gamedb?>.php?gameID=<?php echo $id?>" class='schedule-game'> <!-- DOESNT WORK NO GAME PAGE FOR DISTRICT GAMES-->
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
	}else{//If no game database just pull schedule info
		$sql = "SELECT s.game_date, h.formal_name AS home, a.formal_name AS away, s.location AS location, s.id AS id FROM schedule AS s INNER JOIN roster_teams AS r ON s.team_id=r.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE r.urlName='$roster' AND (h.short_name='$school' OR a.short_name='$school') AND s.season='2024' ORDER BY s.game_date";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){

			
			if($row->home != "Fluvanna County"){
				$opponent = $row->home;
			}else{
				$opponent = $row->away;
			}
			
			$id = $row->id;
			$location = $row->location;
			$sdate = date("D m/d", strtotime($row->game_date));

			printf("%s vs. %s @ %s<br>", $sdate, $opponent, $location);

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
		$sql = "SELECT s.game_date, home_total, away_total, r.urlName AS name, s.game_date, h.formal_name AS home, a.formal_name AS away, s.id AS id FROM $gamedb RIGHT JOIN schedule AS s ON $gamedb.schedule_id=s.id INNER JOIN roster_teams AS r ON s.team_id=r.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE r.urlName='$roster' AND (h.short_name='$school' OR a.short_name='$school') AND s.season='2023' ORDER BY s.game_date";
		$query = $db->prepare($sql);
		$query->execute();
		while($row = $query->fetchObject()){
			$hscore = $row->home_total;
			$ascore = $row->away_total;
			$id = $row->id;
			$sdate = date("D m/d", strtotime($row->game_date));
			?><a href="../../game/<?php echo $gamedb?>.php?gameID=<?php echo $id?>" class='schedule-game'>
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

<!--Roster Body  NONE AVAILABLE FOR DISTRICT TEAMS-->

<br>
<b> ROSTERS </b>

</body>