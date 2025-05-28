<?php
include '../include/database.php';

	$table = $_GET['table'];
	$gameID = $_GET['gameID'];
	$game_time = $_GET['time'];
	$period = $_GET['period'];
	$sql = "";
	
	
	$message = "";
	if($table == "soccer" or $table == "blax" or $table == "glax" or $table == "field_hockey"){//time sport
		
		//$sql = "UPDATE live_games SET period = '$period', game_time = '$time', info_1 = '$info_1', info_2 = '$info_2', info_3 = '$info_3', info_4 = '$info_4', info_5 = '$info_5', info_6 = '$info_6', info_7 = '$info_7', info_8 = '$info_8', info_9 = '$info_9', last_data = NOW() WHERE schedule_id='$gameID'";
		$sql = "UPDATE live_games SET period = '$period', game_time = '$game_time', last_data = NOW() WHERE schedule_id='$gameID'";
	}
	$query = $db->prepare($sql);
	$query->execute();

	$message = "Livegame Updated";
	
	echo json_encode($message);	
?>