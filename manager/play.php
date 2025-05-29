<?php
include '../include/database.php';

	$table = $_GET['table'];
	$tablePBP = $table . "_pbp";
	$tableStats = $table . "_stats";
	$gameID = $_GET['gameID'];
	$completed = 0;
	$sql = "";
	
	$message = "";

	if($table == "football"){
		//jv 10 min
		$minutes = 12;
		$seconds = 0;
		$maximumMinutes = 12;
		$info_1 = ""; //Possession
		$info_2 = 3; //Home Tos
		$info_3 = 3; //Away Tos
		$info_4 = "FCHS"; //Side of field
		$info_5 = 40; //Starting yard line
		$info_6 = 10; //yards to go
		$info_7 = 1; //down
		$sportType = "quarter";
		include './sports/football.php'; //Import all football variables
	}else if($table == "field_hockey"){
		include './sports/fieldhockey.php'; //Import all field hockey variables
	}else if($table == "volleyball"){
		$sportType = "set";
		include './sports/volleyball.php'; //Import all volleyball variables
	}else if($table == "basketball"){
		$minutes = 12;
		$seconds = 0;
		$maximumMinutes = 12;
		$sportType = "quarter";
		include './sports/basketball.php'; //Import all basketball variables
	}else if($table == "soccer"){
		include './sports/soccer.php'; //Import all soccer variables
	}else if($table == "glax"){
		include './sports/glax.php'; //Import all glax variables
	}else if($table == "blax"){
		include './sports/blax.php'; //Import all blax variables
	}else if($table == "batball"){
		include './sports/batball.php'; //Import all batball variables
	}

	$message = "Play Completed";
	
	echo json_encode($actionRadio);	
?>