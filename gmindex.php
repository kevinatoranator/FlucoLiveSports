<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="stylesheet.css">
	

</head>
<body>
<?php 
$date = date("Y-m-d", strtotime("today" ));
$fdate = date("l, F d", strtotime("today")); 

function removeGame($gameID, $db){
	$sql = "DELETE FROM schedule WHERE id = $gameID";
	$query = $db->prepare($sql);
	$query->execute();
}?>



<h1>Today's Games</h1>

<!--Schedule Body-->

<?php
	include './include/database.php';

	
    try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	if(isset($_POST['gameID'])){
        removeGame($_POST['gameID'], $db);
	}
	
	$teams = array();
	
	$sql="SELECT formal_name, short_name FROM roster_schools WHERE district='Jefferson'";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		$teams[$row->formal_name] = $row->short_name;
	}
?>	
	<form name="filter" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<?php
	$school = 'FCHS';
	
	$sql = "SELECT s.id AS gameID, s.time, s.game_date AS date, h.short_name AS home, a.short_name AS away, s.location, s.home_id, s.away_id, s.team_id, t.formattedName, t.urlName AS sport FROM schedule AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE (h.short_name='FCHS' or a.short_name = 'FCHS') && s.game_date = '$date'";

	if(!empty($_POST)){
		$school = $_POST['school'];
		switch($_POST['filterDate']){
			case "today":
				$sql = "SELECT s.id AS gameID, s.time, s.game_date AS date, h.short_name AS home, a.short_name AS away, s.location, s.home_id, s.away_id, s.team_id, t.formattedName, t.urlName AS sport FROM schedule AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE (h.short_name='$school' or a.short_name = '$school') && s.game_date = '$date'";
				break;
			case "all":
				$sql = "SELECT s.id AS gameID, s.time, s.game_date AS date, h.short_name AS home, a.short_name AS away, s.location, s.home_id, s.away_id, s.team_id, t.formattedName, t.urlName AS sport FROM schedule AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE (h.short_name='$school' or a.short_name = '$school')";
				break;
		}
	}
	?>
	<input type="radio" name="filterDate" value="today" checked>Today<br>
	<input type="radio" name="filterDate" value="all">All<br>
	
	<select name = "school">
	<?php
	foreach($teams as $long=>$short){
		if($short == $school){
			printf("<option value = '$short' selected>$long</option>");
		}else{
			printf("<option value = '$short'>$long</option>");
		}
	}
	?>
	</select>
	<button type="submit" name="filter">Filter</button>
	</form>
	<hr>
<?php	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		printf("<form action='./manager/manager.php' method='get'><input type ='hidden' id='gameID' name='gameID' value='%s'><input type='submit' class='gameID' value='%s\n%s vs. %s \n@%s'></form>", $row->gameID, $row->formattedName, $row->home, $row->away, $row->date);
		printf("<form action='edit.php' method='get'><input type ='hidden' id='gameID' name='gameID' value='%s'><input type='submit' class='gameID' value='Edit'></form>", $row->gameID);
		printf("<form action='gmindex.php' method='post'><input type ='hidden' id='gameID' name='gameID' value='%s'><input type='submit' class='gameID' value='Remove'></form><br>", $row->gameID);	
	}
	
	
?>
</body>