<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="stylesheet.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
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

	$sql = "SELECT s.id AS gameID, s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id, s.away_id, s.team_id, t.formattedName, t.urlName AS sport FROM schedule AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id";

    try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		if($row->game_date == $date){
			$sport = $row->sport;
			if($sport=="gsoccer" or  $sport=="bsoccer" or $sport=="jvgsoccer" or $sport=="jvbsoccer"){
				printf("<form action='./manager/soccermanager.php' method='get'><input type ='hidden' id='gameID' name='gameID' value='%s'><input type ='hidden' id='district' name='district' value='false'><input type='submit' class='gameID' value='%s\n%s vs. %s'></form><br>", $row->gameID, $row->formattedName, $row->home, $row->away);
			}else if($sport=="softball" or  $sport=="baseball" or $sport=="jvsoftball" or $sport=="jvbaseball"){
				printf("<form action='./manager/ballmanager.php' method='get'><input type ='hidden' id='gameID' name='gameID' value='%s'><input type ='hidden' id='district' name='district' value='false'><input type='submit' class='gameID' value='%s\n%s vs. %s'></form><br>", $row->gameID, $row->formattedName, $row->home, $row->away);
			}else if($sport=="blax" or  $sport=="jvblax"){
				printf("<form action='./manager/blaxmanager.php' method='get'><input type ='hidden' id='gameID' name='gameID' value='%s'><input type ='hidden' id='district' name='district' value='false'><input type='submit' class='gameID' value='%s\n%s vs. %s'></form><br>", $row->gameID, $row->formattedName, $row->home, $row->away);
			}else if($sport=="glax" or  $sport=="jvglax"){
				printf("<form action='./manager/glaxmanager.php' method='get'><input type ='hidden' id='gameID' name='gameID' value='%s'><input type ='hidden' id='district' name='district' value='false'><input type='submit' class='gameID' value='%s\n%s vs. %s'></form><br>", $row->gameID, $row->formattedName, $row->home, $row->away);
			}else if($sport=="football" or  $sport=="jvfootball"){
				printf("<form action='./manager/footballmanager.php' method='get'><input type ='hidden' id='gameID' name='gameID' value='%s'><input type ='hidden' id='district' name='district' value='false'><input type='submit' class='gameID' value='%s\n%s vs. %s'></form><br>", $row->gameID, $row->formattedName, $row->home, $row->away);
			}else if($sport=="vball" or  $sport=="jvvball"){
				printf("<form action='./manager/vballmanager.php' method='get'><input type ='hidden' id='gameID' name='gameID' value='%s'><input type ='hidden' id='district' name='district' value='false'><input type='submit' class='gameID' value='%s\n%s vs. %s'></form><br>", $row->gameID, $row->formattedName, $row->home, $row->away);
			}else if($sport=="fhockey" or  $sport=="jvfhockey"){
				printf("<form action='./manager/fhockeymanager.php' method='get'><input type ='hidden' id='gameID' name='gameID' value='%s'><input type ='hidden' id='district' name='district' value='false'><input type='submit' class='gameID' value='%s\n%s vs. %s'></form><br>", $row->gameID, $row->formattedName, $row->home, $row->away);
			}
		}
	}
	
	
?>
</body>