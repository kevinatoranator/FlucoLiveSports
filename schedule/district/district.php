<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../../stylesheet.css">
</head>
<body>
<?php $date = $_GET['date'];
$fdate = date("l, F d", strtotime($date)); ?>


<!--Schedule Header-->

    <br>
    <div class="flex justify-between">
        <a href ="../teams/index.php">Teams</a>
       <form action='../schedule.php' method='get'><input type ='hidden' id='date' name='date' value='<?php echo date("Y-m-d"); ?>'><input type='submit' class='schedule' value='Schedule'></form>
    </div>
    <br>
    <div class="flex justify-between">
        <a href ="../../standings/index.php">Standings</a><a href ="./index.php">District Schedule</a>
    </div>

<br>
<div class="flex justify-between">
<form action='district.php' method='get'><input type ='hidden' id='date' name='date' value='<?php echo date("Y-m-d", strtotime("-1 days", strtotime($date))); ?>'><input type='submit' class='schedule' value='< <?php echo date("M. d", strtotime("-1 days", strtotime($date)))?>'></form> <b> <?php echo $fdate ?></b> <form action='district.php' method='get'><input type ='hidden' id='date' name='date' value='<?php echo date("Y-m-d", strtotime("+1 days", strtotime($date))); ?>'><input type='submit' class='schedule' value='<?php echo date("M. d", strtotime("+1 days", strtotime($date)))?> >'></form>
</div>
<div class="flex justify-between">
<b></b><b> District </b><b></b>
</div>
<br>




<!--Schedule Body-->

<?php
	include '../../include/database.php';

	$sql = "SELECT s.id AS gameID, s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id, s.away_id, s.team_id, t.formattedName, t.urlName AS sport FROM schedule_other AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id
	ORDER BY s.time";

    try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		if($row->game_date == $fdate){
			$sport = $row->sport;
			$gameID = $row->gameID;
			$formattedName = $row->formattedName;
			$time = $row->time;
			$home = $row->home;
			$away = $row->away;
			
			
			if($sport == "gsoccer" or $sport == "jvgsoccer" or $sport == "bsoccer" or $sport == "jvbsoccer"){
				?>			
				<a href="../game/soccer.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div><div>.....|</div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM soccer_other AS sc JOIN schedule_other AS s ON sc.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>@ %s|</div></div>", $home);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "softball" or $sport == "jvsoftball" or $sport == "baseball" or $sport == "jvbaseball"){
				?>			
				<a href="../game/ball.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div><div>.....|</div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM batball_other AS bb JOIN schedule_other AS s ON bb.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>@ %s|</div></div>", $home);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "blax" or $sport == "jvblax"){
				?>			
				<a href="../game/blax.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div><div>.....|</div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM blax_other AS bl JOIN schedule_other AS s ON bl.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>@ %s|</div></div>", $home);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "glax" or $sport == "jvglax"){
				?>			
				<a href="../game/glax.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div><div>.....|</div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM glax_other AS gl JOIN schedule_other AS s ON gl.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>@ %s|</div></div>", $home);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "bbball" or $sport == "jvbbball" or $sport == "gbball" or $sport == "jvgbball"){
				?>
				<a href="../game/basketball.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div><div>.....|</div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM basketball_other AS bb JOIN schedule_other AS s ON bb.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>@ %s|</div></div>", $home);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else{
				printf("+------------------------------+<br>");
				printf("<div class='schedule-container'><div>|<b>%s</b></div></div>", $row->formattedName);
				printf("<div class='schedule-container'><div>|%s EST</div> <div>@ %s|</div></div>", $row->time, $row->home);
				printf("<div class='schedule-container'><div>|%s</div> <div>.....|</div></div>", $row->home);
				printf("<div class='schedule-container'><div>|%s</div> <div>.....|</div></div>", $row->away);
				printf("+------------------------------+</div><br><br>");
			}
		}
	}
	
	
?>

</body>