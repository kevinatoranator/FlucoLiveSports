<?php
include '../include/database.php';

	$table = $_GET['table'];
	$gameID = $_GET['gameID'];
	$game_time = $_GET['time'];
	$period = $_GET['period'];
	$info = $_GET['liveinfo'];
	$sql = "";
	
	
	$message = "";
	$sql = "UPDATE live_games SET period = '$period', game_time = '$game_time', info_1 = '$info[0]', info_2 = '$info[1]', info_3 = '$info[2]', info_4 = '$info[3]', info_5 = '$info[4]', info_6 = '$info[5]', info_7 = '$info[6]', info_8 = '$info[7]', info_9 = '$info[8]', last_data = NOW() WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();

	$message = "Livegame Updated";
	
	echo json_encode($message);	
?>