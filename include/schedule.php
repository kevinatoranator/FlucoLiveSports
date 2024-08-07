<!-- display schedule -->

<?php
	while($row = $query->fetchObject()){
		if($row->game_date == $date){
			$sport = $row->sport;
			$gameID = $row->gameID;
			$formattedName = $row->formattedName;
			$time = $row->time;
			$home = $row->home;
			$away = $row->away;
			
			
			if($sport == "gsoccer" or $sport == "jvgsoccer" or $sport == "bsoccer" or $sport == "jvbsoccer"){
				$sql = "SELECT 1 FROM soccer AS sc JOIN schedule AS s ON sc.schedule_id WHERE schedule_id='$gameID'";
				?>			
				<a href="<?php echo $baseUrl?>game/soccer.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div><div>.....|</div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM soccer AS sc JOIN schedule AS s ON sc.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>@ %s|</div></div>", $home);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "softball" or $sport == "jvsoftball" or $sport == "baseball" or $sport == "jvbaseball"){
				
				//gets live game stats
				$sqlbb = "SELECT * FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);
				$outs = 0;
				?>			
				<a href="<?php echo $baseUrl?>game/batball.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div>
				<?php if($querylg->rowCount() == 0){ ?>
				<div>.....|</div>
				<?php }else{ 
					printf("<div> %s %s|</div>", $livegame['info_1'], $livegame['period']);
					$outs = $livegame['game_time'];
				}
				?>

				</div>
				<?php
				$sqlbb = "SELECT * FROM batball AS bb JOIN schedule AS s ON bb.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
						}else{
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);							
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>%s %s|</div></div>", $outs, $livegame['info_4']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "blax" or $sport == "jvblax"){
				?>			
				<a href="<?php echo $baseUrl?>game/blax.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div><div>.....|</div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM blax AS bl JOIN schedule AS s ON bl.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>@ %s|</div></div>", $home);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "glax" or $sport == "jvglax"){
				?>			
				<a href="<?php echo $baseUrl?>game/glax.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div><div>.....|</div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM glax AS gl JOIN schedule AS s ON gl.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>@ %s|</div></div>", $home);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "bbball" or $sport == "jvbbball" or $sport == "gbball" or $sport == "jvgbball"){
				?>
				<a href="<?php echo $baseUrl?>game/basketball.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div><div>.....|</div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM basketball AS bb JOIN schedule AS s ON bb.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>@ %s|</div></div>", $home);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
				
			//FALL SPORTS
			}else if($sport == "football" or $sport == "jvfootball"){
							
				//gets live game stats
				$sqlbb = "SELECT * FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);
				$side = "Own";
				$yard_line = 50;
				?>			
				<a href="<?php echo $baseUrl?>game/football.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div>
				<?php if($querylg->rowCount() == 0){ //If live game doesn't exist
					printf("<div>.....|</div>");
				 }else{ 
					printf("<div> Q%s %s|</div>", $livegame['period'], $livegame['game_time']);
					$side = $livegame['info_4'];
					$yard_line = $livegame['info_5'];
				}
				?>
				</div>
				<?php
				$sqlbb = "SELECT * FROM football AS fb JOIN schedule AS s ON fb.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>%s %s|</div></div>", $side, $yard_line);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "vball" or $sport == "jvvball"){
				//gets live game stats
				$sqlbb = "SELECT * FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);
				$serve = ".";
				?>			
				<a href="<?php echo $baseUrl?>game/vball.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div>
				<?php if($querylg->rowCount() == 0){ //If live game doesn't exist
					printf("<div>.....|</div>");
				 }else{ 
					printf("<div> Set %s|</div>", $livegame['period']);
					$serve = $livegame['info_1'];
				}
				?>			
				</div>
				<?php
				$sqlbb = "SELECT * FROM volleyball AS vb JOIN schedule AS s ON vb.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>Serve: %s|</div></div>", $serve);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "fhockey" or $sport == "jvfhockey"){
				//gets live game stats
				$sqlbb = "SELECT * FROM live_games AS lg JOIN schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);
				?>			
				<a href="<?php echo $baseUrl?>game/fhockey.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div>
				<?php if($querylg->rowCount() == 0){ //If live game doesn't exist
					printf("<div>.....|</div>");
				 }else{ 
					printf("<div> Q%s %s|</div>", $livegame['period'], $livegame['game_time']);
				}
				?>	
				</div>
				<?php
				$sqlbb = "SELECT * FROM field_hockey AS fh JOIN schedule AS s ON fh.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div>|<?php echo $time?> EST </div><div>@<?php echo $home?>|</div>
					</div>
					<div class='schedule-container'><div>|<?php echo $home?></div><div>.....|</div></div>
					<div class='schedule-container'><div>|<?php echo $away?></div><div>.....|</div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div>|</div><div><b>FINAL</b>|</div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div>|<b>%s</div> <div>...%d</b>|</div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div>|<span class='red'>LIVE</span></div> <div>@ %s|</div></div>", $home);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div>|%s</div> <div>...%d|</div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else{
				printf("+------------------------------+<br>");
				printf("<div class='schedule-container'><div>|<b>%s</b></div></div>", $row->formattedName);
				printf("<div class='schedule-container'><div>|%s EST</div> <div>@ %s|</div></div>", $row->time, $row->home);
				printf("<div class='schedule-container'><div>|%s</div> <div>.....|</div></div>", $row->home);
				printf("<div class='schedule-container'><div>|%s</div> <div>.....|</div></div>", $row->away);
				printf("+------------------------------+</div><br><br>");
			}
		}
	}
	
?>