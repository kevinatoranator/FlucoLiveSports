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
	include '../include/header.php';
	include '../include/database.php';
	
	$roster = $_GET['sport'];
	$sql = "SELECT formattedName FROM roster_teams WHERE roster_teams.urlName='$roster'";
	
	 try {
      $db = new PDO("mysql:host=$host_name; dbname=$database;", $user_name, $password);
    } catch (PDOException $e) {
      echo "Error!:" . $e->getMessage() . "<br/>";
      die();
    }
	$query = $db->prepare($sql);
	$query->execute();
	$i = $query->fetchObject();
	$sportFormat = $i->formattedName;
	
?>



	<br>
	<div class="flex justify-between">
        <div></div><b> <?php echo $sportFormat ?></b><div></div>
    </div>
	
	
<br>

<!--Current Season Results-->


<!--Standings Body-->

<input type="checkbox" name="2024" id="2024" onclick="rtoggle('2024')"><label for="2024"><b>2024 [+]</b></label>
<br><br>
<div class = "2024">
<?php
	$sql = "SELECT * FROM standings JOIN roster_teams ON standings.sport_id=roster_teams.id JOIN roster_schools ON standings.school_id=roster_schools.id WHERE roster_teams.urlName='$roster' AND standings.season='2024' ORDER BY standings.wins DESC";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		if($row->short_name == "FCHS"){
			?><a href = "../teams/roster.php?sport=<?php echo $roster?>" class='schedule-game'> <?php printf("%s-%s-%s %s<br>", $row->wins, $row->losses, $row->ties, $row->formal_name); ?></a><?php
		}else{
			?><a href = "../teams/district/roster.php?sport=<?php echo $roster?>&school=<?php echo $row->short_name?>" class='schedule-game'> <?php printf("%s-%s-%s %s<br>", $row->wins, $row->losses, $row->ties, $row->formal_name); ?></a><?php
		}
	}
?>
</div>
<br><br>

<input type="checkbox" name="2023" id="2023" onclick="rtoggle('2023')"><label for="2023"><b>2023 [+]</b></label>
<br><br>
<div class = "2023 hidden">
<?php
	$sql = "SELECT * FROM standings JOIN roster_teams ON standings.sport_id=roster_teams.id JOIN roster_schools ON standings.school_id=roster_schools.id WHERE roster_teams.urlName='$roster' AND standings.season='2023' ORDER BY standings.wins DESC";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		if($row->short_name == "FCHS"){
			?><a href = "../teams/roster.php?sport=<?php echo $roster?>" class='schedule-game'> <?php printf("%s-%s-%s %s<br>", $row->wins, $row->losses, $row->ties, $row->formal_name); ?></a><?php
		}else{
			printf("%s-%s-%s %s<br>", $row->wins, $row->losses, $row->ties, $row->formal_name);
		}
	}
?>
</div>
<br><br>

<input type="checkbox" name="2022" id="2022" onclick="rtoggle('2022')"><label for="2022"><b>2022 [+]</b></label>
<br><br>
<div class = "2022 hidden">
<?php
	$sql = "SELECT * FROM standings JOIN roster_teams ON standings.sport_id=roster_teams.id JOIN roster_schools ON standings.school_id=roster_schools.id WHERE roster_teams.urlName='$roster' AND standings.season='2022' ORDER BY standings.wins DESC";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		printf("%s-%s-%s %s<br>", $row->wins, $row->losses, $row->ties, $row->formal_name);
	}
?>
</div>
<br><br>

<input type="checkbox" name="2021" id="2021" onclick="rtoggle('2021')"><label for="2021"><b>2021 [+]</b></label>
<br><br>
<div class = "2021 hidden">
<?php
	$sql = "SELECT * FROM standings JOIN roster_teams ON standings.sport_id=roster_teams.id JOIN roster_schools ON standings.school_id=roster_schools.id WHERE roster_teams.urlName='$roster' AND standings.season='2021' ORDER BY standings.wins DESC";
	$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		printf("%s-%s-%s %s<br>", $row->wins, $row->losses, $row->ties, $row->formal_name);
	}
?>
</div>
</body>