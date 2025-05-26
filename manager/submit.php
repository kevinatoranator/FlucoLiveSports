<?php
include '../include/database.php';

	$table = $_GET['table'];
	$tablePBP = $table . "_pbp";
	$tableStats = $table . "_stats";
	$gameID = $_GET['gameID'];
	$infoArray = $_GET['infoArray'];
	$action = $infoArray[0];
	$team = $infoArray[1];
	$player = $infoArray[2];
	$period = $infoArray[3];
	$time = $infoArray[4];
	$completed = 0;
	$sql = "";
	
	$pbp = "$team $action$player";
	
	$message = "";
	if($table == "soccer"){//half sport
		$sql = "INSERT INTO $tablePBP (text, half, time, game_id) VALUES ('$pbp', '$period', '$time', (SELECT id FROM schedule where id='$gameID'))";
	}else if($table == "batball"){
		$sql = "INSERT INTO $tablePBP (text, inning, game_id) VALUES ('$pbp', '$period', (SELECT id FROM schedule where id='$gameID'))";
	}
	$query = $db->prepare($sql);
	$query->execute();

	$message = "Play Added to DB";
	
	echo json_encode($message);	
?>