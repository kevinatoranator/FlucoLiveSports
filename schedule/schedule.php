<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../stylesheet.css">
</head>
<body>
<?php $date = $_GET['date'];
$fdate = date("l, F d", strtotime($date)); 
$baseUrl = '../';
$schedule = 'schedule';
$affix = ''; ?>


<!--Schedule Header-->

    <?php 
	include '../include/header.php';
	?>

<br>
<div class="flex justify-between">
<form action='schedule.php' method='get'><input type ='hidden' id='date' name='date' value='<?php echo date("Y-m-d", strtotime("-1 days", strtotime($date))); ?>'><input type='submit' class='schedule' value='< <?php echo date("M. d", strtotime("-1 days", strtotime($date)))?>'></form> <b> <?php echo $fdate ?></b> <form action='schedule.php' method='get'><input type ='hidden' id='date' name='date' value='<?php echo date("Y-m-d", strtotime("+1 days", strtotime($date))); ?>'><input type='submit' class='schedule' value='<?php echo date("M. d", strtotime("+1 days", strtotime($date)))?> >'></form>
</div>
<br>




<!--Schedule Body-->

<?php
	include '../include/database.php';

	$sql = "SELECT s.id AS gameID, s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location, s.home_id, s.away_id, s.team_id, t.formattedName, t.urlName AS sport FROM $schedule AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE a.short_name='FCHS' OR h.short_name='FCHS'
	ORDER BY s.time";

    try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	$query = $db->prepare($sql);
	$query->execute();
	include '../include/schedule.php';
	
?>

</body>