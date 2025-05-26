<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../../stylesheet.css">
</head>
<body>
<?php 
$date = date("Y-m-d", strtotime("today" ));
$fdate = date("l, F d", strtotime("today")); 
$schedule = 'schedule';
?>


<!--Schedule Header-->

    <br>
    <?php 
	include '../../include/header.php';
	?>

<br>
<div class="flex justify-between">
<form action='district.php' method='get'><input type ='hidden' id='date' name='date' value='<?php echo date("Y-m-d", strtotime("-1 days", strtotime($fdate))); ?>'><input type='submit' class='schedule' value='< <?php echo date("M. d", strtotime("-1 days", strtotime($fdate)))?>'></form> <b> <?php echo $fdate ?></b> <form action='district.php' method='get'><input type ='hidden' id='date' name='date' value='<?php echo date("Y-m-d", strtotime("+1 days", strtotime($fdate))); ?>'><input type='submit' class='schedule' value='<?php echo date("M. d", strtotime("+1 days", strtotime($fdate)))?> >'></form>
</div>
<div class="flex justify-between">
<b></b><b> District </b><b></b>
</div>
<br>




<!--Schedule Body-->

<?php
	include '../../include/database.php';

	$sql = "SELECT s.id AS gameID, s.time, s.game_date, h.short_name AS home, a.short_name AS away, s.location AS location, s.home_id, s.away_id, s.team_id, s.notes AS notes, t.formattedName, t.urlName AS sport FROM $schedule AS s JOIN roster_schools a ON s.away_id=a.id JOIN roster_schools h ON s.home_id=h.id JOIN roster_teams AS t ON s.team_id=t.id WHERE NOT (a.short_name='FLUV' OR h.short_name='FLUV')
	ORDER BY s.time";
	
	$query = $db->prepare($sql);
	$query->execute();
	
	include '../../include/schedule.php';
	
	
?>

</body>