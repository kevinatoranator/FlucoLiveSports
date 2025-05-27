<?php
include '../include/database.php';

	$table = $_GET['table'];
	$gameID = $_GET['gameID'];
	$game_time = $_GET['time'];
	$completed = 0;
	$sql = "";
	
	$info_1 = "";
	$info_2 = "";
	$info_3 = "";
	$info_4 = "";
	$info_5 = "";
	$info_6 = "";
	$info_7 = "";
	$info_8 = "";
	$info_9 = "";
	
	$message = "";
	
	$sql = "SELECT 1 FROM $table AS game JOIN schedule ON game.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	if($query->rowCount() == 0){
		if($table == "football" or $table == "field_hockey" or $table == "basketball"  or $table == "glax" or $table == "blax"){
			$sql = "INSERT INTO $table (home_quarter1, home_quarter2, home_quarter3, home_quarter4, home_ot, away_quarter1, away_quarter2, away_quarter3, away_quarter4, away_ot, home_total, away_total, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM schedule WHERE id='$gameID'))";
		}
		else if($table == "volleyball"){
			$sql = "INSERT INTO $table (home_set1, home_set2, home_set3, home_set4, home_set5, away_set1, away_set2, away_set3, away_set4, away_set5, home_total, away_total, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM schedule WHERE id='$gameID'))";
		}
		else if($table == "soccer"){
			$sql = "INSERT INTO $table (home_half1, home_half2, home_OT, home_total, away_half1, away_half2, away_OT, away_total, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM schedule WHERE id='$gameID'))";
		}
		else if($table == "batball"){
			$sql = "INSERT INTO $table (home_i1, home_i2, home_i3, home_i4, home_i5, home_i6, home_i7, home_ex, home_total, away_i1, away_i2, away_i3, away_i4, away_i5, away_i6, away_i7, away_ex, away_total, home_hits, away_hits, home_errors, away_errors, completed, schedule_id) VALUES (0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, (SELECT id FROM schedule WHERE id='$gameID'))";
		}
		$query = $db->prepare($sql);
		$query->execute();
		$message = "Game Created \n";
	}else{
		$message = "Game Already Started \n";	
	}
	
	$sql = "SELECT * FROM live_games AS game JOIN schedule AS s ON game.schedule_id WHERE schedule_id='$gameID'";
	$query = $db->prepare($sql);
	$query->execute();
	
	if($query->rowCount() == 0 && $completed == 0){//TODO REFACTOR var names
		$sql = "INSERT INTO live_games (period, game_time, info_1, info_2, info_3, info_4, info_5, info_6, info_7, info_8, info_9, schedule_id) VALUES (1, '$game_time', '$info_1', '$info_2', '$info_3', '$info_4', '$info_5', '$info_6', '$info_7', '$info_8', '$info_9', (SELECT id FROM schedule WHERE id='$gameID'))";
		$query = $db->prepare($sql);
		$query->execute();
		$message = "Live Game Created";
	}else{
		$message = "Live Already Game Created";
	}
	echo json_encode($message);	
?>