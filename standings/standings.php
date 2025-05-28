<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="../stylesheet.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
	include '../include/header.php';
	
	$roster = $_GET['sport'];
	$sql = "SELECT formattedName FROM roster_teams WHERE roster_teams.urlName='$roster'";
	
	$query = $db->prepare($sql);
	$query->execute();
	$i = $query->fetchObject();
	$sportFormat = $i->formattedName;
	
	if($roster == "gsoccer" or $roster == "jvgsoccer" or $roster == "bsoccer" or $roster == "jvbsoccer"){
		$sportdb = 'soccer';
	}else if($roster == "softball" or $roster == "jvsoftball" or $roster == "baseball" or $roster == "jvbaseball"){
		$sportdb = 'batball';	
	}else if($roster == "blax" or $roster == "jvblax"){
		$sportdb = 'blax';	
	}else if($roster == "glax" or $roster == "jvglax"){
		$sportdb = 'glax';
	}else if($roster == "bbball" or $roster == "jvbbball" or $roster == "gbball" or $roster == "jvgbball"){
		$sportdb = 'basketball';	
	}else if($roster == "fhockey" or $roster == "jvfhockey"){
		$sportdb = 'field_hockey';
	}else if($roster == "football" or $roster == "jvfootball"){
		$sportdb = 'football';
	}else if($roster == "vball" or $roster == "jvvball"){
		$sportdb = 'volleyball';
	}else{
		$sportdb = '';
	}
	
?>



	<br>
	<div class="flex justify-between">
        <div></div><b> <?php echo $sportFormat ?></b><div></div>
    </div>
	
	
<br>

<!--Current Season Results-->


<!--Standings Body-->

<?php 
$years = [2024 => '24-25', 2023 =>  '23-24', 2022 =>  '22-23', 2021 => '21-22'];
$count = 0;

foreach($years as $year => $label){

?>

<input type="checkbox" name="<?php echo $year;?>" id="<?php echo $year?>" onclick="rtoggle('<?php echo $year;?>')"><label for="<?php echo $year;?>"><b><?php echo $label;?> [+]</b></label>
<br><br>
<div class = "<?php echo $year;  if($count > 0){echo ' hidden';}?>">
<?php
	$current_season = []; // School, url, Win, loss, Streak, last 10, next game
	$current_season[] = ["", "", "W-L-T","", "", "STRK", "L5", "NEXT"];
	
	if($sportdb == 'soccer' or $sportdb == 'glax' or $sportdb == 'blax'){
		array_push($current_season[0],"GD", "GF", "GA");
	}

	$sql = "SELECT * FROM standings JOIN roster_teams ON standings.sport_id=roster_teams.id JOIN roster_schools ON standings.school_id=roster_schools.id WHERE roster_teams.urlName='$roster' AND standings.season='$year' ORDER BY standings.wins DESC, standings.losses";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$current_season[] = [$row->short_name, '<a href = "../teams/roster.php?sport=' . $roster . "&school=" . $row->short_name .'"' . " class='schedule-game'>", $row->wins, $row->losses, $row->ties, "-", "0-0", "v. TBD"];
	}
	
	printf('<table style = "border-spacing: 2px">');
	$count = 0;
	foreach($current_season as $entry){
		if($count > 0){
			$sql = "SELECT sdb.home_total AS ht, sdb.away_total AS at, h.short_name AS home, a.short_name AS away
			FROM $sportdb AS sdb JOIN schedule AS s ON sdb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id 
			WHERE t.urlName='$roster' AND s.season='$year' AND s.notes!='Scrimmage' AND (h.short_name ='$entry[0]' OR a.short_name ='$entry[0]') AND s.game_date <= current_date AND sdb.completed = '1' ORDER BY s.game_date DESC";
			$query = $db->prepare($sql);
			$query->execute();
			$streak = 0;
			while($row = $query->fetchObject()){
				$home = $row->home;
				$away = $row->away;
				$hscore = $row->ht;
				$ascore = $row->at;
				if((($home == $entry[0] and $hscore > $ascore) or ($away == $entry[0] and $ascore > $hscore)) and $streak > -1){
					$streak++;
				}else if((($home == $entry[0] and $hscore < $ascore) or ($away == $entry[0] and $ascore < $hscore)) and $streak < 1){
					$streak--;
				}else{
					break;
				}
			}
			if($streak > 0){
				$entry[5] = "W".$streak;
			}else if($streak < 0){
				$entry[5] = "L".abs($streak);
			}
			$sql = "SELECT sdb.home_total AS ht, sdb.away_total AS at, h.short_name AS home, a.short_name AS away
			FROM $sportdb AS sdb JOIN schedule AS s ON sdb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id 
			WHERE t.urlName='$roster' AND s.season='$year' AND s.notes!='Scrimmage' AND (h.short_name ='$entry[0]' OR a.short_name ='$entry[0]') AND s.game_date <= current_date AND sdb.completed = '1' ORDER BY s.game_date DESC LIMIT 5";
			$query = $db->prepare($sql);
			$query->execute();
			$wins = 0;
			$losses = 0;
			$draws = 0;
			while($row = $query->fetchObject()){
				$home = $row->home;
				$away = $row->away;
				$hscore = $row->ht;
				$ascore = $row->at;
				if(($home == $entry[0] and $hscore > $ascore) or ($away == $entry[0] and $ascore > $hscore)){
					$wins++;
				}else if(($home == $entry[0] and $hscore < $ascore) or ($away == $entry[0] and $ascore < $hscore)){
					$losses++;
				}else{
					$draws++;
				}
			}
			
			$entry[6] = $wins . "-" . $losses ."-" . $draws;
			
			$isLive = false;
			$sql = "SELECT s.id AS id, s.game_date AS date, s.season AS season, h.formal_name AS fhome, a.formal_name AS faway, s.time AS startTime, h.short_name AS home, a.short_name AS away
				FROM schedule AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id JOIN $sportdb AS sdb ON s.id=sdb.schedule_id
				WHERE t.urlName='$roster' AND s.season='$year' AND (h.short_name ='$entry[0]' OR a.short_name ='$entry[0]') AND s.game_date = current_date AND sdb.completed = 0 ORDER BY s.game_date LIMIT 1"; //current_date
			$query = $db->prepare($sql);
			$query->execute();
			while($row = $query->fetchObject()){
				$isLive = true;
				$fhome = $row->fhome;
				$faway = $row->faway;
				$home = $row->home;
				$away = $row->away;
				$date = '<span class="red">LIVE</span>';
				$gameID = $row->id;
			}
			
			if($isLive == true){
				if($home == $entry[0]){
						$entry[7] = "<a style = 'text-decoration: none; color: var(--black);' href='../game/" . $sportdb . ".php?gameID=" . $gameID . "'>v " . $away . " " . $date . "</a>";
					}else{
						$entry[7] = "<a style = 'text-decoration: none; color: var(--black);' href='../game/" . $sportdb . ".php?gameID=" . $gameID . "'>@ " . $home . " " . $date  . "</a>";
					}
			}else{
				$sql = "SELECT s.id AS id, s.game_date AS date, s.season AS season, h.formal_name AS fhome, a.formal_name AS faway, s.time AS startTime, h.short_name AS home, a.short_name AS away
				FROM schedule AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id LEFT JOIN $sportdb AS sdb ON s.id=sdb.schedule_id
				WHERE t.urlName='$roster' AND s.season='$year' AND (h.short_name ='$entry[0]' OR a.short_name ='$entry[0]') AND s.game_date >= current_date AND sdb.schedule_id is NULL ORDER BY s.game_date LIMIT 1"; //current_date 
				$query = $db->prepare($sql);
				$query->execute();
				while($row = $query->fetchObject()){
					$fhome = $row->fhome;
					$faway = $row->faway;
					$home = $row->home;
					$away = $row->away;
					$date = $row->date;
					$date = date_format(date_create($date), "m/d");
					$gameID = $row->id;
					
					if($home == $entry[0]){
						$entry[7] = "<a style = 'text-decoration: none; color: var(--black);' href='../game/" . $sportdb . ".php?gameID=" . $gameID . "'>v " . $away . " " . $date . "</a>";
					}else{
						$entry[7] = "<a style = 'text-decoration: none; color: var(--black);' href='../game/" . $sportdb . ".php?gameID=" . $gameID . "'>@ " . $home . " " . $date  . "</a>";
					}
				}
			}
			
			if($sportdb == 'soccer' or $sportdb == 'glax' or $sportdb == 'blax'){
				$sql = "SELECT sdb.home_total AS ht, sdb.away_total AS at, h.short_name AS home, a.short_name AS away
				FROM $sportdb AS sdb JOIN schedule AS s ON sdb.schedule_id = s.id JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id 
				WHERE t.urlName='$roster' AND s.season='$year' AND s.notes!='Scrimmage' AND (h.short_name ='$entry[0]' OR a.short_name ='$entry[0]') AND s.game_date <= current_date AND sdb.completed = '1'";
				$query = $db->prepare($sql);
				$query->execute();
				$gd = 0;
				$gf = 0;
				$ga = 0;
				while($row = $query->fetchObject()){
					$home = $row->home;
					$away = $row->away;
					$hscore = $row->ht;
					$ascore = $row->at;
					if($home == $entry[0]){
						$gd += ($hscore - $ascore);
						$gf += $hscore;
						$ga += $ascore;
					}else{
						$gd += ($ascore - $hscore);
						$gf += $ascore;
						$ga += $hscore;
					}
				}
				$entry[8] = $gd;
				$entry[9] = $gf;
				$entry[10] = $ga;
			}
			
			
			printf('<tr>');
			printf('<td style="text-align: left">%s %s</a></td>', $entry[1], $entry[0]); //NAME + LINK
			printf('<td style="text-align: right">%s-%s-%s</td>', $entry[2], $entry[3], $entry[4]);//WLT
			if(str_contains($entry[5], "W")){
				printf('<td style="text-align: right;color:green;">%s</td>', $entry[5]);// Streak
			}else if(str_contains($entry[5], "L")){
				printf('<td style="text-align: right;color:red;">%s</td>', $entry[5]);// Streak
			}else{
				printf('<td style="text-align: right;">%s</td>', $entry[5]);
			}
			printf('<td style="text-align: right">%s</td>', $entry[6]); // Last 5
			printf('<td style="text-align: right">%s</td>', $entry[7]); // Next game
			if($sportdb == 'soccer' or $sportdb == 'glax' or $sportdb == 'blax'){
				printf('<td style="text-align: right">%s</td>', $entry[9]);
				printf('<td style="text-align: right">%s</td>', $entry[10]);
				if($entry[8] > 0){
					printf('<td style="text-align: right;color:green;">%s</td>', $entry[8]); // GD
				}else if($entry[8] < 0){
					printf('<td style="text-align: right;color:red;">%s</td>', $entry[8]); // GD
				}else{
					printf('<td style="text-align: right">%s</td>', $entry[8]); // GD
				}
			}
			printf('</tr>');
		}else{//LABEL ROW
			printf('<tr>');
			printf('<td style="text-align: left"></td>'); //NAME + LINK
			printf('<td style="text-align: center">%s</td>', $entry[2]);//WLT
			printf('<td style="text-align: right">%s</td>', $entry[5]);// Streak
			printf('<td style="text-align: center">%s</td>', $entry[6]); // Last 5
			printf('<td style="text-align: center">%s</td>', $entry[7]); // Next game
			if($sportdb == 'soccer' or $sportdb == 'glax' or $sportdb == 'blax'){
				printf('<td style="text-align: right">%s</td>', $entry[9]); //GF
				printf('<td style="text-align: right">%s</td>', $entry[10]); //GA
				printf('<td style="text-align: right">%s</td>', $entry[8]); // GD
			}
			printf('</tr>');
			$count++;
		}
	}
	printf("</table>");
?>
</div>
<br><br>

<?php
$count = $count + 1;
}
?>

</body>