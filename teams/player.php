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
		if(displayr.classList.contains("hidden")){
			displayr.classList.remove("hidden")
		}else{
			displayr.classList.add("hidden")
		}
	}
</script>
<?php
	

	include '../include/database.php';
	
	$playerName = $_GET['player'];
	
	 try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }	
?>


<!--Schedule Header-->

    <br>
    <?php 
	include '../include/header.php';
	
	printf("<br><center><b>%s</b></center><br><br>", $playerName);
	$seasonsPlayed = [];
	
	$sql = "SELECT roster_teams.formattedName AS team, roster_teams.urlName AS url, roster_player.season AS year FROM roster_player INNER JOIN roster_teams ON roster_player.team_id=roster_teams.id WHERE roster_player.name='$playerName'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$team = $row->team;
		$year = $row->year;
		$url = $row->url;

		
		array_push($seasonsPlayed, array($team, $year, $url));
		//print_r($seasonsPlayed);	
	}
	
	$seasonArray = array();
	foreach($seasonsPlayed as $sportSeason){
		$seasonArray[] = ["<a href = './roster.php?sport=$sportSeason[2]' class='schedule-game'>$sportSeason[0]($sportSeason[1])</a>", "----", "----", "----", "----", "----", "----"];
		$statdb = "";
		if($sportSeason[0] == "jvblax" or $sportSeason[0] == "blax"){
			$statdb = "blax";
		}else if($sportSeason[0] == "jvglax" or $sportSeason[0] == "glax"){
			$statdb = "glax";
		}else if($sportSeason[0] == "jvbsoccer" or $sportSeason[0] == "bsoccer" or $sportSeason[0] == "jvgsoccer" or $sportSeason[0] == "gsoccer"){
			$statdb = "soccer";
		}else if($sportSeason[0] == "jvbaseball" or $sportSeason[0] == "baseball" or $sportSeason[0] == "jvsoftball" or $sportSeason[0] == "softball"){
			$statdb = "batball";
		}else if($sportSeason[0] == "jvfhockey" or $sportSeason[0] == "fhockey"){
			$statdb = "field_hockey";
		}else if($sportSeason[0] == "JV Football" or $sportSeason[0] == "Varsity Football"){
			$statdb = "football_stats";
		}else if($sportSeason[0] == "jvbbball" or $sportSeason[0] == "bbball" or $sportSeason[0] == "jvgbball" or $sportSeason[0] == "gbball"){
			$statdb = "basketball";
		}else if($sportSeason[0] == "Girls JV Volleyball" or $sportSeason[0] == "Girls Varsity Volleyball"){
			$statdb = "volleyball_stats";
		}
		if($statdb == "volleyball_stats"){
			$seasonArray[] = ["", "Kills", "Assts", "Aces", "A/ERR", "S/ERR", "Blocks", "Digs"];
			$sql = "SELECT kills, assists, aces, attack_errors, service_errors, blocks, digs, schedule.game_date AS date_ FROM $statdb 
			INNER JOIN roster_player ON roster_player.id=$statdb.player INNER JOIN schedule ON schedule.id=$statdb.game WHERE roster_player.name='$playerName' AND schedule.season='$sportSeason[1]'";
			$query = $db->prepare($sql);
			$query->execute();
			while($row = $query->fetchObject()){
				$seasonArray[] = [$row->date_, $row->kills, $row->assists, $row->aces, $row->attack_errors, $row->service_errors, $row->blocks, $row->digs];
				//printf("%s: %s<br>", $game, $row->kills);		
			}
			
		}else if($statdb == "football_stats"){
			$seasonArray[] = ["", "CAR", "YDS", "REC", "YDS", "TD", "SACKS", "INT"];

			$sql = "SELECT carries, total_carry_yards, receptions, total_reception_yards, rushing_touchdowns, reception_touchdowns, sacks, interceptions, schedule.game_date AS date_ FROM $statdb 
			INNER JOIN roster_player ON roster_player.id=$statdb.player INNER JOIN schedule ON schedule.id=$statdb.game WHERE roster_player.name='$playerName' AND schedule.season='$sportSeason[1]'";
			$query = $db->prepare($sql);
			$query->execute();
			while($row = $query->fetchObject()){
				$seasonArray[] = [$row->date_, $row->carries, $row->total_carry_yards, $row->receptions, $row->total_reception_yards, $row->rushing_touchdowns+$row->reception_touchdowns, $row->sacks, $row->interceptions];
				//printf("%s: %s<br>", $game, $row->kills);		
			}
			
		}
		
	}
	printf('<table style = "border-spacing: 9px">');
	for($j = 0; $j < count($seasonArray); $j++){
		if($j%2){//alternate colors doesn't work well with table
			printf('<tr>');
		}else{
			printf('<tr>');
		}
		for($i = 0; $i < count($seasonArray[$j]); $i++){
			if($i == 0){
				printf('<td style="text-align: left">%s</td>', $seasonArray[$j][$i]);
			}else{
				printf('<td style="text-align: right">%s</td>', $seasonArray[$j][$i]);
			}
		}
		printf('</tr>');
	}

	printf("</table>");
	
	
?>



</body>