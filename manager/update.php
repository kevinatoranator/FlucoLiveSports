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
		
	}else if($table == "blax" or $table == "glax" or $table == "field_hockey" or $table == "basketball" or $table == "football"){
		$hTotal = $scoreArray[0] + $scoreArray[1] + $scoreArray[2] + $scoreArray[3] + $scoreArray[4];
		$aTotal = $scoreArray[5] + $scoreArray[6] + $scoreArray[7] + $scoreArray[8] + $scoreArray[9];
		
		$sql = "UPDATE $table SET home_quarter1 = '$scoreArray[0]', home_quarter2 = '$scoreArray[1]', home_quarter3 = '$scoreArray[2]', home_quarter4 = '$scoreArray[3]', home_ot = '$scoreArray[4]', home_total = '$hTotal', 
			away_quarter1 = '$scoreArray[5]', away_quarter2 = '$scoreArray[6]', away_quarter3 = '$scoreArray[7]', away_quarter4 = '$scoreArray[8]', away_ot = '$scoreArray[9]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		
	}else if($table == "volleyball"){
		$hTotal = 0;
		$aTotal = 0;
		if($scoreArray[0] >= 25 and $scoreArray[0] > $scoreArray[5] + 1){
			$hTotal += 1;
		}else if($scoreArray[5] >= 25 and $scoreArray[5] > $scoreArray[0] + 1){
			$aTotal += 1;
		}
		if($scoreArray[1] >= 25 and $scoreArray[1] > $scoreArray[6] + 1){
			$hTotal += 1;
		}else if($scoreArray[6] >= 25 and $scoreArray[6] > $scoreArray[1] + 1){
			$aTotal += 1;
		}
		if($scoreArray[2] >= 25 and $scoreArray[2] > $scoreArray[7] + 1){
			$hTotal += 1;
		}else if($scoreArray[7] >= 25 and $scoreArray[7] > $scoreArray[2] + 1){
			$aTotal += 1;
		}
		if($scoreArray[3] >= 25 and $scoreArray[3] > $scoreArray[8] + 1){
			$hTotal += 1;
		}else if($scoreArray[8] >= 25 and $scoreArray[8] > $scoreArray[3] + 1){
			$aTotal += 1;
		}
		if($scoreArray[4] >= 25 and $scoreArray[4] > $scoreArray[9] + 1){
			$hTotal += 1;
		}else if($scoreArray[9] >= 25 and $scoreArray[9] > $scoreArray[4] + 1){
			$aTotal += 1;
		}
		
		$sql = "UPDATE $table SET home_set1 = '$scoreArray[0]', home_set2 = '$scoreArray[1]', home_set3 = '$scoreArray[2]', home_set4 = '$scoreArray[3]', home_set5 = '$scoreArray[4]', home_total = '$hTotal', 
			away_set1 = '$scoreArray[5]', away_set2 = '$scoreArray[6]', away_set3 = '$scoreArray[7]', away_set4 = '$scoreArray[8]', away_set5 = '$scoreArray[9]', away_total = '$aTotal' WHERE schedule_id='$gameID'";
		
	}
	$query = $db->prepare($sql);
	$query->execute();

	$message = "Scores Manually Updated";
	
	echo json_encode($message);	
?>