<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="stylesheet.css">
	
	<script>
	loadGame(){
	
	}
	</script>
</head>
<body>
<?php 
$date = date("Y-m-d", strtotime("today" ));
$fdate = date("l, F d", strtotime("today")); ?>



<h1>Today's Games</h1>

<!--Schedule Body-->

<?php
	include './include/database.php';

	$sql = "SELECT s.id AS gameID, s.time, s.game_date AS date, h.short_name AS home, a.short_name AS away, s.location, s.home_id, s.away_id, s.team_id, t.formattedName, t.urlName AS sport FROM schedule AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id";

    try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		printf("<form action='./manager/manager.php' method='get'><input type ='hidden' id='gameID' name='gameID' value='%s'><input type ='hidden' id='district' name='district' value='false'><input type='submit' class='gameID' value='%s\n%s vs. %s \n@%s'></form><br>", $row->gameID, $row->formattedName, $row->home, $row->away, $row->date);
			
	}
	
	
?>
</body>