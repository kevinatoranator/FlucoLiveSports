<?php
include '../include/database.php';

	$table = $_GET['table'];
	$gameID = $_GET['gameID'];
	$scoreArray = $_GET['scores'];
	$sql = "";
	
	
	$message = "";
	if($table == "soccer"){//half sport
		$hTotal = $scoreArray[0] + $scoreArray[1] + $scoreArray[2];
		$aTotal = $scoreArray[3] + $scoreArray[4] + $scoreArray[5];
		
		$sql = "UPDATE $table SET home_half1 = '$scoreArray[0]', home_half2 = '$scoreArray[1]', home_OT = '$scoreArray[2]', home_total = '$hTotal', 
			away_half1 = '$scoreArray[3]', away_half2 = '$scoreArray[4]', away_OT = '$scoreArray[5]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
	}else if($table == "batball"){
		$hTotal = $scoreArray[0] + $scoreArray[1] + $scoreArray[2] + $scoreArray[3] + $scoreArray[4] + $scoreArray[5] + $scoreArray[6] + $scoreArray[7];
		$aTotal = $scoreArray[8] + $scoreArray[9] + $scoreArray[10] + $scoreArray[11] + $scoreArray[12] + $scoreArray[13] + $scoreArray[14]+ $scoreArray[15];
		
		$sql = "UPDATE $table SET home_i1 = '$scoreArray[0]', home_i2 = '$scoreArray[1]', home_i3 = '$scoreArray[2]', home_i4 = '$scoreArray[3]', home_i5 = '$scoreArray[4]', home_i6 = '$scoreArray[5]', home_i7 = '$scoreArray[6]', home_ex = '$scoreArray[7]', home_total = '$hTotal', 
			away_i1 = '$scoreArray[8]', away_i2 = '$scoreArray[9]', away_i3 = '$scoreArray[10]', away_i4 = '$scoreArray[11]', away_i5 = '$scoreArray[12]', away_i6 = '$scoreArray[13]', away_i7 = '$scoreArray[14]', away_ex = '$scoreArray[15]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		
	}else if($table == "blax" or $table == "glax" or $table == "field_hockey"){
		$hTotal = $scoreArray[0] + $scoreArray[1] + $scoreArray[2] + $scoreArray[3] + $scoreArray[4];
		$aTotal = $scoreArray[5] + $scoreArray[6] + $scoreArray[7] + $scoreArray[8] + $scoreArray[9];
		
		$sql = "UPDATE $table SET home_quarter1 = '$scoreArray[0]', home_quarter2 = '$scoreArray[1]', home_quarter3 = '$scoreArray[2]', home_quarter4 = '$scoreArray[3]', home_ot = '$scoreArray[4]', home_total = '$hTotal', 
			away_quarter1 = '$scoreArray[5]', away_quarter2 = '$scoreArray[6]', away_quarter3 = '$scoreArray[7]', away_quarter4 = '$scoreArray[8]', away_ot = '$scoreArray[9]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		
	}
	$query = $db->prepare($sql);
	$query->execute();

	$message = "Scores Manually Updated";
	
	echo json_encode($message);	
?>