<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="stylesheet.css">
</head>
<body>
<?php 
$phpURL =  "add.php";

function updateScheduleGame($game_date, $time, $location, $notes, $season, $away_id, $home_id, $team_id, $db){

	$sqls = "INSERT schedule (game_date, time, location, notes, season, away_id, home_id, team_id) VALUES ('$game_date', '$time', '$location', '$notes', '$season', '$away_id', '$home_id', '$team_id')";
	$query = $db->prepare($sqls);
	$query->execute();
	echo "ADDED";
}
?>


<div class="flex justify-between">
        <a href="gmindex.php">Return to Home</a>
        <a href='<?php echo $phpURL?>'>Reload</a>
    </div>
<h1>Add Game Information</h1>


<!--Schedule Body-->

<?php
	include 'include/database.php';

	
	$teamList = array();
	$sql = "SELECT id, formal_name FROM roster_schools";
	$query = $db->prepare($sql);
	$query->execute();
	
	while($row = $query->fetchObject()){
		$teamList[$row->formal_name] = $row->id;
	}
	
	$sportList = array();
	$sql = "SELECT id, formattedName FROM roster_teams";
	$query = $db->prepare($sql);
	$query->execute();
	
	while($row = $query->fetchObject()){
		$sportList[$row->formattedName] = $row->id;
	}

	
	if($_POST && isset($_POST['game_date'])){
		$game_date = $_POST['game_date'];
		$time = $_POST['time'];
		$location = $_POST['location'];
		$notes = $_POST['notes'];
		$season = $_POST['season'];
		$away_id = $_POST['away_id'];
		$home_id = $_POST['home_id'];
		$team_id = $_POST['team_id'];
		updateScheduleGame($game_date, $time, $location, $notes, $season, $away_id, $home_id, $team_id, $db);
	}
?>

<br>
<form action = "<?php echo $phpURL?>" method="POST">

<label for="game_date">game_date:</label>
<input type='date' id='game_date' name = 'game_date'  value="<?php echo date('Y-m-d'); ?>"><br>
<label for="time">time:</label>
<input type='text' id='time' name = 'time' value='12:00pm'><br>
<label for="location">location:</label>
<input type='text' id='location' name = 'location' value='TBD'><br>
<label for="notes">notes:</label>
<input type='text' id='notes' name = 'notes' value=''><br>
<label for="season">season:</label>
<input type='number' id='season' name = 'season' style="width: 4em" value='2024'><br>
<label for="away_id">away_id:</label>
<select name = "away_id">
<?php 
	foreach($teamList as $name => $id){
		if($name == 'TBA'){
				echo("<option value = '$id' selected>$name</option>");
			}else{
				echo("<option value = '$id'>$name</option>");
			}
	}
?>
</select><br>
<label for="home_id">home_id:</label>
<select name = "home_id">
<?php 
	foreach($teamList as $name => $id){
		if($name == 'TBA'){
				echo("<option value = '$id' selected>$name</option>");
			}else{
				echo("<option value = '$id'>$name</option>");
			}
	}
?>
</select><br>
<label for="team_id">team_id:</label>
<select name = "team_id">
<?php 
	foreach($sportList as $name => $id){
		echo("<option value = '$id'>$name</option>");
	}
?>
</select><br>

<input type ="submit" Value="Submit">
</form>

</body>