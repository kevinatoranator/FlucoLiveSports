<!-- display schedule -->

<?php
	$entries = 0;
	
	/*$entry_array = array(array("Lacrosse", "Sublinks Here"), array("Soccer", "Sublinks Here"), array("Baseball", "Sublinks Here"), array("Softball", "Sublinks Here"), array("Field Hockey", "Sublinks Here"),
	array("Football", "Sublinks Here"), array("Volleyball", "Sublinks Here"), array("Basketball", "Sublinks Here"), array("Golf", "Sublinks Here"), array("Cross Country", "Sublinks Here"),
	array("Outdoor Track", "Sublinks Here"), array("Indoor Track", "Sublinks Here"), array("Tennis", "Sublinks Here"), array("Wrestling", "Sublinks Here"), array("Scholastic Bowl", "Sublinks Here"),
	array("Swim & Dive", "Sublinks Here"), array("Cheer", "Sublinks Here"));// Level (Gender Level - Boys Varsity), Live/Time, Away Team, Home Team Essentially line by line of display
	*/
	
	$entry_array = array(array(["Lacrosse"], ["blax", "jvblax", "glax", "jvglax"]), array(["Soccer"], ["gsoccer", "jvgsoccer", "bsoccer", "jvbsoccer"]), array(["Baseball"], ["baseball", "jvbaseball"]), array(["Softball"], ["softball", "jvsoftball"]),
	array(["Field Hockey"], ["fhockey", "jvfhockey"]), array(["Football"], ["football", "jvfootball"]), array(["Volleyball"], ["vball", "jvvball"]), array(["Basketball"], ["bbball", "jvbbball", "gbball", "jvgbball"]), 
	array(["Golf"], ["Sublinks Here"]), array(["Cross Country"], ["Sublinks Here"]),
	array(["Outdoor Track"], ["Sublinks Here"]), array(["Indoor Track"], ["Sublinks Here"]), array(["Tennis"], ["Sublinks Here"]), array(["Wrestling"], ["Sublinks Here"]), array(["Scholastic Bowl"], ["Sublinks Here"]),
	array(["Swim & Dive"], ["Sublinks Here"]), array(["Cheer"], ["Sublinks Here"]));// Level (Gender Level - Boys Varsity), Live/Time, Away Team, Home Team Essentially line by line of display
	
	while($row = $query->fetchObject()){
		if($row->game_date == $date){
			$sport = $row->sport;
			$gameID = $row->gameID;
			$formattedName = $row->formattedName;
			$location = $row->location;
			$time = $row->time;
			$home = $row->home;
			$away = $row->away;
			$notes = $row->notes;
			$sportdb = '';
			
			
			if($sport == "gsoccer" or $sport == "jvgsoccer" or $sport == "bsoccer" or $sport == "jvbsoccer"){
				$formattedName = explode(" ", $formattedName);
				$formattedName = $formattedName[0][0] . "." . " " . $formattedName[1];
				$sportdb = 'soccer';
			}else if($sport == "softball" or $sport == "jvsoftball" or $sport == "baseball" or $sport == "jvbaseball"){
				$formattedName = explode(" ", $formattedName)[0];
				$sportdb = 'batball';
				
			}else if($sport == "blax" or $sport == "jvblax"){
				$formattedName = explode(" ", $formattedName);
				$formattedName = $formattedName[0][0] . "." . " " . $formattedName[1];
				$sportdb = 'blax';
				
			}else if($sport == "glax" or $sport == "jvglax"){
				$formattedName = explode(" ", $formattedName);
				$formattedName = $formattedName[0][0] . "." . " " . $formattedName[1];
				$sportdb = 'glax';

			}else if($sport == "bbball" or $sport == "jvbbball" or $sport == "gbball" or $sport == "jvgbball"){
				$formattedName = explode(" ", $formattedName);
				$formattedName = $formattedName[0][0] . "." . " " . $formattedName[1];
				$sportdb = 'basketball';
				
			}else if($sport == "fhockey" or $sport == "jvfhockey"){
				$formattedName = explode(" ", $formattedName)[0];
				$sportdb = 'field_hockey';

			}else if($sport == "football" or $sport == "jvfootball"){
				$formattedName = explode(" ", $formattedName)[0];
				$sportdb = 'football';
			}else if($sport == "vball" or $sport == "jvvball"){
				$formattedName = explode(" ", $formattedName)[1];
				$sportdb = 'volleyball';
			}else if($sport == "gxc" or $sport == "bxc"){
				$formattedName = explode(" ", $formattedName);
				$formattedName = $formattedName[0][0] . "." . " " . $formattedName[1];
				$sportdb = 'xc';
			}else if($sport == "bindoor" or $sport == "gindoor"){
				$formattedName = explode(" ", $formattedName);
				$formattedName = $formattedName[0][0] . "." . " " . $formattedName[1];
				$sportdb = 'indoor';
			}else if($sport == "gtrack" or $sport == "btrack"){
				$formattedName = explode(" ", $formattedName);
				$formattedName = $formattedName[0][0] . "." . " " . $formattedName[1];
				$sportdb = 'track';
			}else if($sport == "gtennis" or $sport == "btennis"){
				$formattedName = explode(" ", $formattedName);
				$formattedName = $formattedName[0][0] . "." . " " . $formattedName[1];
				$sportdb = 'tennis';
			}
			
			
			$newEntry = array($sportdb, $gameID, $formattedName);
			if(str_contains($notes, "Postponed")){
				$newEntry[] = "Postponed";//3
			}else{
				$newEntry[] = $time . " EST";//3
			}
			$newEntry[] = $home;//4
			$newEntry[] = $away;//5
			
			if($sportdb == "football"){		
				//gets live game stats
				$sqlbb = "SELECT * FROM live_games AS lg JOIN $schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);
				$side = "Own";
				$yard_line = 50;

				$sqlbb = "SELECT * FROM $sportdb AS fb JOIN $schedule AS s ON fb.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){//if game hasn't started
					$newEntry[] = 0;//6
				}else{
					$newEntry[] = 1;//6
					$newEntry[] = $rowbb['home_total'];//7
					$newEntry[] = $rowbb['away_total'];//8
					if($rowbb['completed'] == 1){
						$newEntry[] = 1;//9
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
						$newEntry[] = 0;//9
						if($querylg->rowCount() > 0){ 
							$newEntry[] = 1;//10
							$newEntry[] = "Q" . $livegame['period'];//11
							$newEntry[] = $livegame['game_time'];//12
							$newEntry[] = $livegame['info_4'];//13 side
							$newEntry[] = $livegame['info_5'];//14 yardline
							
							if($livegame['info_1'] == $away){
								$newEntry[5] = $newEntry[5] . "&#9666";//127944 == football
							}
							if($livegame['info_1'] == $home){
								$newEntry[4] = $newEntry[4] . "&#9666";//127944 == football
							}
						}else{		
							$newEntry[] = 0;//10
						}
					}
				}
				$entry_array[5][] = $newEntry;
			}else if($sportdb == "volleyball"){
				//gets live game stats
				$sqlbb = "SELECT * FROM live_games AS lg JOIN $schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);
				$serve = ".";
				
				if($querylg->rowCount() > 0){ //If live game doesn't exist
					$serve = $livegame['info_1'];
				}			
				
				$sqlbb = "SELECT * FROM $sportdb AS vb JOIN $schedule AS s ON vb.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
					$newEntry[] = 0;//6
				}else{
					$newEntry[] = 1;//6
					$newEntry[] = $rowbb['home_total'];//7
					$newEntry[] = $rowbb['away_total'];//8
					if($rowbb['completed'] == 1){
						$newEntry[] = 1;//9
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
						$newEntry[] = 0;//9
						if($querylg->rowCount() > 0){ 
							$newEntry[] = 1;//10
							$newEntry[] = "Set";//11
							$newEntry[] = $livegame['period'];//12
							if($serve == $away){
								$newEntry[5] = $newEntry[5] . "&#9666";//127952 = volleyball
							}
							if($serve == $home){
								$newEntry[4] = $newEntry[4] . "&#9666";//127952 = volleyball
							}
						}else{		
							$newEntry[] = 0;//10
						}
					}
				}
				$entry_array[6][] = $newEntry;
			}
			else if($sportdb == "field_hockey" or $sportdb == "glax" or $sportdb == "blax" or $sportdb == "basketball"){
				$sqlbb = "SELECT * FROM live_games AS lg JOIN $schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);
				$sqlbb = "SELECT * FROM $sportdb AS fh JOIN $schedule AS s ON fh.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
					$newEntry[] = 0;//6
				}else{
					$newEntry[] = 1;//6
					$newEntry[] = $rowbb['home_total'];//7
					$newEntry[] = $rowbb['away_total'];//8
					if($rowbb['completed'] == 1){
						$newEntry[] = 1;//9
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
						$newEntry[] = 0;//9
						if($querylg->rowCount() > 0){ //If live game exists
							$newEntry[] = 1;//10
							$newEntry[] = "Q" . $livegame['period'];//11
							$newEntry[] = $livegame['game_time'];//12
						}else{		
							$newEntry[] = 0;//10
						}
					}
				}
				if($sportdb == "field_hockey"){
					$entry_array[4][] = $newEntry;
				}else if($sportdb == "glax" or $sportdb == "blax"){
					$entry_array[0][] = $newEntry;
				}else if($sportdb == "basketball"){
					$entry_array[7][] = $newEntry;
				}
			
			}else if($sportdb == "batball"){
				$sqlbb = "SELECT * FROM live_games AS lg JOIN $schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);

				$sqlbb = "SELECT * FROM $sportdb AS fh JOIN $schedule AS s ON fh.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
					$newEntry[] = 0;//6
				}else{
					$newEntry[] = 1;//6
					$newEntry[] = $rowbb['home_total'];//7
					$newEntry[] = $rowbb['away_total'];//8
					if($rowbb['completed'] == 1){
						$newEntry[] = 1;//9
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
						$newEntry[] = 0;//9
						if($querylg->rowCount() > 0){ //If live game exists
							$newEntry[] = 1;//10
							$newEntry[] = $livegame['game_time'];//11
							$newEntry[] = $livegame['period'];//12
							
						}else{
							$newEntry[] = 0;//10						
						}
							
					}
				}
				if($sport == "baseball" or $sport == "jvbaseball"){
					$entry_array[2][] = $newEntry;
				}else{
					$entry_array[3][] = $newEntry;
				}
			}else if($sportdb == "soccer"){
				$sqlbb = "SELECT * FROM live_games AS lg JOIN $schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);

				$sqlbb = "SELECT * FROM $sportdb AS fh JOIN $schedule AS s ON fh.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
					$newEntry[] = 0;//6
				}else{
					$newEntry[] = 1;//6
					$newEntry[] = $rowbb['home_total'];//7
					$newEntry[] = $rowbb['away_total'];//8
					if($rowbb['completed'] == 1){
						$newEntry[] = 1;//9
					}else{
						$newEntry[] = 0;//9
					//Started not completed, grab current quarter time etc. to replace top line
					
						if($querylg->rowCount() > 0){ //If live game exists
							$newEntry[] = 1;//10
							$newEntry[] = "H" . $livegame['period'];//11
							$newEntry[] = $livegame['game_time'] . "'";//12
							
						}else{
							$newEntry[] = 0;//10						
						}
					}
				}
				$entry_array[1][] = $newEntry;
			}else if($sportdb == "xc" or $sportdb == "indoor" or $sportdb == "track"){
				//gets live game stats
				$sqlsdb = "SELECT * FROM $sportdb AS sdb JOIN $schedule AS s ON sdb.schedule_id = s.id WHERE s.id='$gameID'";
				$querysdb = $db->prepare($sqlsdb);
				$querysdb->execute();
				$game = $querysdb->fetch(PDO::FETCH_ASSOC);
		
				$newEntry[] = 0;//6
				if($game != null){
					$newEntry[] = $game['url'];//7
				}
				$newEntry[5] = $newEntry[4];
				$newEntry[4] = substr($location, 0, 9);
				if($sportdb == 'xc'){
					$entry_array[9][] = $newEntry;
				}else if($sportdb == 'indoor'){
					$entry_array[11][] = $newEntry;
				}
				else if($sportdb == 'track'){
					$entry_array[10][] = $newEntry;
				}
			}else if($sportdb == 'tennis'){
				//gets live game stats
				//$sqlbb = "SELECT * FROM $sportdb AS sdb JOIN $schedule AS s ON sdb.schedule_id = s.id WHERE s.id='$gameID'";
				//$querylg = $db->prepare($sqlbb);
				//$querylg->execute();
				//$livegame = $querylg->fetch(PDO::FETCH_ASSOC);
		
				$newEntry[] = 0;//6
				//if($livegame){
				//	$newEntry[] = $livegame['url'];//7
				//}
				
				
				$entry_array[12][] = $newEntry;
			}
			$entries += 1;
		}
	}
	//if($entries == 0){
	//	printf("<center>-No Scheduled Games-</center>");
	//}
	
	//Spring Season No Entries 0,1,2,3,10,12
	//Fall Season No Entries 4,5,6,8,9, 16
	//Winter Season No Entries 7, 11, 13, 14, 15
	/*if(date('m', strtotime($date)) > 2 and date('m', strtotime($date)) < 7){
		$entry_array = array($entry_array[0],$entry_array[1],$entry_array[2],$entry_array[3],$entry_array[10],$entry_array[12]);
	}else if(date('m', strtotime($date)) > 7 and date('m', strtotime($date)) < 12){
		$entry_array = array($entry_array[4],$entry_array[5],$entry_array[6],$entry_array[9],$entry_array[16]);
	}else if(date('m', strtotime($date)) > 11 and date('m', strtotime($date)) < 3){
		$entry_array = array($entry_array[7],$entry_array[11],$entry_array[13],$entry_array[14],$entry_array[15]);
	}else{
		$entry_array = array(array(["No Sports in Season"]));
	}*/
	foreach($entry_array as $entry){
		if(count($entry) > 2){
			
			for($j = 0; $j < count($entry); $j++){
				if($j == 0){
					printf("<center><b>-%s-</b></center>", $entry[$j][0]);
				}else if($j == 1){
					$linkrow = 'Standings';
					foreach($entry[$j] as $link){
						$label = "";
						$newlink = $link;
						if(str_contains($link,  "jv")){
							$label = "JV";
							$newlink = substr($newlink, 2);
						}
						if($newlink[0] == 'g'){
							$label = "Girls " . $label;
						}else if($newlink[0] == 'b' and $newlink != 'baseball'){
							$label = "Boys " . $label;
						}
						if($label == ''){
							$label = "Varsity";
						}
						
						$linkrow = $linkrow . " | <a href ='/flucolivesports/standings/standings.php?sport=${link}'>${label}</a> ";
					}
					printf('<center>%s |</center>', $linkrow);
				}else{
					if($j % 2 == 0){
						printf("<div class='schedule-row'>");				
					}
					printf("<div class='schedule-box'>");
					if(($entry[$j][0] != "xc" and $entry[$j][0] != "indoor" and $entry[$j][0] != "track") or (($entry[$j][0] == "xc" or $entry[$j][0] == "indoor" or $entry[$j][0] == "track") and count($entry[$j]) < 8)){?>
					<a href="/flucolivesports/game/<?php echo $entry[$j][0]?>.php?gameID=<?php echo $entry[$j][1]?>" class='schedule-game'>
					<?php }else{?>
						<a href="<?php echo $entry[$j][7]?>" class='schedule-game'>
					<?php }?>
					+-----------------+<br>
					<div class='schedule-container'>
					<div class='schedule-parts-left'>|<?php echo $entry[$j][2]?></div>
					<?php
					if(count($entry[$j]) > 10 and $entry[$j][0] == "football"){
						printf(" <div class='schedule-parts-right'>%s %s|</div>",$entry[$j][13],$entry[$j][14]);
					}else{
						printf("<div class='schedule-parts-right'>|</div>");
					}?>				
					</div>
					<?php 
					if($entry[$j][6] == 0){?>	
						<div class='schedule-container'>
						<?php if($entry[$j][0] != "xc" and $entry[$j][0] != "indoor" and $entry[$j][0] != "track"){?>
							<div class='schedule-parts-left'>|<?php echo $entry[$j][3]?></div><div class='schedule-parts-right'>@<?php echo $entry[$j][4]?>|</div>
						<?php }else{ ?>
							<div class='schedule-parts-left'>|<?php echo $entry[$j][3]?></div><div class='schedule-parts-right'>|</div>
						<?php } ?>
						</div>
						<div class='schedule-container'><div class='schedule-parts-left'>|<?php echo $entry[$j][5]?></div><div class='schedule-parts-right'>|</div></div>
						<div class='schedule-container'><div class='schedule-parts-left'>|<?php echo $entry[$j][4]?></div><div class='schedule-parts-right'>|</div></div>
		
						
						<?php
					}else{
						if($entry[$j][9] == 1){
							printf("<div class='schedule-container'><div class='schedule-parts-left'>|</div><div class='schedule-parts-right'><b>FINAL</b>|</div></div>");
							if($entry[$j][7] > $entry[$j][8]){
								printf("<div class='schedule-container'><div class='schedule-parts-left'>|%s</div> <div class='schedule-parts-right'>%d|</div></div>", $entry[$j][5], $entry[$j][8]);
								printf("<div class='schedule-container'><div class='schedule-parts-left'>|<b>%s</div> <div class='schedule-parts-right'>%d</b>|</div></div>", $entry[$j][4], $entry[$j][7]);
							}else if($entry[$j][7] < $entry[$j][8]){
								printf("<div class='schedule-container'><div class='schedule-parts-left'>|<b>%s</div> <div class='schedule-parts-right'>%d</b>|</div></div>", $entry[$j][5], $entry[$j][8]);
								printf("<div class='schedule-container'><div class='schedule-parts-left'>|%s</div> <div class='schedule-parts-right'>%d|</div></div>", $entry[$j][4], $entry[$j][7]);
							}else{
								printf("<div class='schedule-container'><div class='schedule-parts-left'>|%s</div> <div class='schedule-parts-right'>%d|</div></div>", $entry[$j][5], $entry[$j][8]);
								printf("<div class='schedule-container'><div class='schedule-parts-left'>|%s</div> <div class='schedule-parts-right'>%d|</div></div>", $entry[$j][4], $entry[$j][7]);
							}
						}else{
							
							if($entry[$j][10] == 1){
								printf("<div class='schedule-container'><div class='schedule-parts-left'>|<span class='red'>LIVE</span></div> <div class='schedule-parts-right'>%s %s|</div></div>", $entry[$j][11], $entry[$j][12]);
							}else{
								printf("<div class='schedule-container'><div class='schedule-parts-left'>|<span class='red'>LIVE</span></div> <div class='schedule-parts-right'>|</div></div>");
							}
							printf("<div class='schedule-container'><div class='schedule-parts-left'>|%s</div> <div class='schedule-parts-right'>%d|</div></div>", $entry[$j][5], $entry[$j][8]);
							printf("<div class='schedule-container'><div class='schedule-parts-left'>|%s</div> <div class='schedule-parts-right'>%d|</div></div>", $entry[$j][4], $entry[$j][7]);
						}	
					}
					?>+-----------------+</a><br><br></a><?php
					printf("</div>");
					if($j % 2 == 1 or count($entry) <= $j + 1){
						printf("</div>");
					}
				}
			}
			
			echo "<br>";
		}else{
			for($j = 0; $j < count($entry); $j++){
				if($j == 0){
					printf("<center><b>-%s-</b></center>", $entry[$j][0]);
				}else if($j == 1){
					$linkrow = 'Standings';
					foreach($entry[$j] as $link){
						$label = "";
						$newlink = $link;
						if(str_contains($link,  "jv")){
							$label = "JV";
							$newlink = substr($newlink, 2);
						}
						if($newlink[0] == 'g'){
							$label = "Girls " . $label;
						}else if($newlink[0] == 'b' and $newlink != 'baseball'){
							$label = "Boys " . $label;
						}
						if($label == ''){
							$label = "Varsity";
						}
						
						$linkrow = $linkrow . " | <a href ='/flucolivesports/standings/standings.php?sport=${link}'>${label}</a> ";
					}
					printf('<center>%s |</center>', $linkrow);
				}
			}
			printf("<br><br><center>-No Scheduled Games-</center><br><br>");
		}
	}
	
?>