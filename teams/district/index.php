<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="../../stylesheet.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>



<!--Schedule Header-->

    <br>
    <?php 
	include '../../include/header.php';
	?>

<br>




<!--Team List by Season-->

<?php

	include '../../include/database.php';

	
    try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	
	
	echo "<b>Fall</b><br><br>";
	
	echo "<form action='./roster.php' method='get'>";
	$sql = "SELECT * FROM roster_schools WHERE roster_schools.district='Jefferson' AND NOT roster_schools.formal_name='Fluvanna County'";
	$query = $db->prepare($sql);
	$query->execute();
	echo '<select id="school" name = "school">';
	while($row = $query->fetchObject()){
		printf("<option value = '%s'>%s</option>", $row->short_name, $row->formal_name);
	}
	echo '</select><br>';
	
	
	$sql = "SELECT urlName, formattedName FROM roster_teams WHERE season='Fall'";
	$query = $db->prepare($sql);
	$query->execute();
	echo '<select id="sport" name = "sport">';
	while($row = $query->fetchObject()){
		printf("<option value = '%s'>%s</option>", $row->urlName, $row->formattedName);
	}
	?><input type = "submit" value="Go"></form><br><br><br>
	
	<?php
	
	
	echo "<b>Winter</b><br><br>";
	
	echo "<form action='./roster.php' method='get'>";
	$sql = "SELECT * FROM roster_schools WHERE roster_schools.district='Jefferson' AND NOT roster_schools.formal_name='Fluvanna County'";
	$query = $db->prepare($sql);
	$query->execute();
	echo '<select id="school" name = "school">';
	while($row = $query->fetchObject()){
		printf("<option value = '%s'>%s</option>", $row->short_name, $row->formal_name);
	}
	echo '</select><br>';
	
	
	$sql = "SELECT urlName, formattedName FROM roster_teams WHERE season='Winter'";
	$query = $db->prepare($sql);
	$query->execute();
	echo '<select id="sport" name = "sport">';
	while($row = $query->fetchObject()){
		printf("<option value = '%s'>%s</option>", $row->urlName, $row->formattedName);
	}
	?><input type = "submit" value="Go"></form><br><br><br>
	
	<?php
	
	echo "<b>Spring</b><br><br>";
	
	echo "<form action='./roster.php' method='get'>";
	$sql = "SELECT * FROM roster_schools WHERE roster_schools.district='Jefferson' AND NOT roster_schools.formal_name='Fluvanna County'";
	$query = $db->prepare($sql);
	$query->execute();
	echo '<select id="school" name = "school">';
	while($row = $query->fetchObject()){
		printf("<option value = '%s'>%s</option>", $row->short_name, $row->formal_name);
	}
	echo '</select><br>';
	
	
	$sql = "SELECT urlName, formattedName FROM roster_teams WHERE season='Spring'";
	$query = $db->prepare($sql);
	$query->execute();
	echo '<select id="sport" name = "sport">';
	while($row = $query->fetchObject()){
		printf("<option value = '%s'>%s</option>", $row->urlName, $row->formattedName);
	}
	?><input type = "submit" value="Go"></form><br><br><br>
	
	
	

</body>