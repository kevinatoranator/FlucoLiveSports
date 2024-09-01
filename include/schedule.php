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
				$sportdb = 'soccer' . $affix;
				$sql = "SELECT 1 FROM $sportdb AS sc JOIN $schedule AS s ON sc.schedule_id WHERE schedule_id='$gameID'";
				?>			
				<a href="<?php echo $baseUrl?>game/soccer.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div>|<b><?php echo $formattedName?></b></div><div>.....|</div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM $sportdb AS sc JOIN $schedule AS s ON sc.schedule_id = s.id WHERE s.id='$gameID'";
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
				$sqlbb = "SELECT * FROM live_games AS lg JOIN $schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
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
				$sqlbb = "SELECT * FROM batball AS bb JOIN $schedule AS s ON bb.schedule_id = s.id WHERE s.id='$gameID'";
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
				$sqlbb = "SELECT * FROM blax AS bl JOIN $schedule AS s ON bl.schedule_id = s.id WHERE s.id='$gameID'";
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
				$sqlbb = "SELECT * FROM glax AS gl JOIN $schedule AS s ON gl.schedule_id = s.id WHERE s.id='$gameID'";
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
				$sqlbb = "SELECT * FROM basketball AS bb JOIN $schedule AS s ON bb.schedule_id = s.id WHERE s.id='$gameID'";
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
				
				$sportdb = 'football' . $affix;			
				//gets live game stats
				$sqlbb = "SELECT * FROM live_games AS lg JOIN $schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);
				$side = "Own";
				$yard_line = 50;
				?>			
				<a href="<?php echo $baseUrl?>game/football.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div style="float: left;">|<b><?php echo $formattedName?></b></div>
				<?php if($querylg->rowCount() == 0){ //If live game doesn't exist
					printf("<div style='float: right;'>|</div>");
				 }else{ 
					printf("<div style='float: right;'> Q%s %s|</div>", $livegame['period'], $livegame['game_time']);
					$side = $livegame['info_4'];
					$yard_line = $livegame['info_5'];
				}
				?>
				<div class="clear"></div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM $sportdb AS fb JOIN $schedule AS s ON fb.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){//if game hasn't started
				?>	<div class='schedule-container'>		
					<div style="float: left;">|<?php echo $time?> EST </div><div style="float: right;">@<?php echo $home?>|</div>
					<div class="clear"></div>
					</div>
					<div class='schedule-container'><div style="float: left;">|<?php echo $home?></div><div style="float: right;">|</div><div class="clear"></div></div>
					<div class='schedule-container'><div style="float: left;">|<?php echo $away?></div><div style="float: right;">|</div><div class="clear"></div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div style='float: left;'>|</div><div style='float: right;'><b>FINAL</b>|</div><div class='clear'></div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div style='float: left;'>|<b>%s</div> <div style='float: right;'>%d</b>|</div><div class='clear'></div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div style='float: left;'>|%s</div> <div style='float: right;'>%d|</div><div class='clear'></div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div style='float: left;'>|%s</div> <div style='float: right;'>%d|</div><div class='clear'></div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div style='float: left;'>|<b>%s</div> <div style='float: right;'>%d</b>|</div><div class='clear'></div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					printf("<div class='schedule-container'><div style='float: left;'>|<span class='red'>LIVE</span></div> <div style='float: right;'>%s %s|</div><div class='clear'></div></div>", $side, $yard_line);
					printf("<div class='schedule-container'><div style='float: left;'>|%s</div>", $home);
					if($livegame['info_1'] == $home){
						printf("<div style='float: left;'>&#9666;</div>");//127944 == football
					}
					printf("<div style='float: right;'>%d|</div><div class='clear'></div></div>", $rowbb['home_total']);
					
					printf("<div class='schedule-container'><div style='float: left;'>|%s</div>", $away);
					if($livegame['info_1'] == $away){
						printf("<div style='float: left;'>&#9666;</div>");//127944 == football
					}
					printf("<div style='float: right;'>%d|</div><div class='clear'></div></div>", $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "vball" or $sport == "jvvball"){
				//gets live game stats
				$sportdb = 'volleyball' . $affix;
				$sqlbb = "SELECT * FROM live_games AS lg JOIN $schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);
				$serve = ".";
				?>			
				<a href="<?php echo $baseUrl?>game/volleyball.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div style="float: left;">|<b><?php echo $formattedName?></b></div>
				<?php if($querylg->rowCount() > 0){ //If live game doesn't exist
					$serve = $livegame['info_1'];
				}				 
				printf("<div style='float: right;'>|</div>");
				?>		
				<div class="clear"></div>
				</div>
				<?php
				$sqlbb = "SELECT * FROM $sportdb AS vb JOIN $schedule AS s ON vb.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div style="float: left;">|<?php echo $time?> EST </div><div style="float: right;">@<?php echo $home?>|</div>
					<div class="clear"></div>
					</div>
					<div class='schedule-container'><div style="float: left;">|<?php echo $home?></div><div style="float: right;">|</div><div class="clear"></div></div>
					<div class='schedule-container'><div style="float: left;">|<?php echo $away?></div><div style="float: right;">|</div><div class="clear"></div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div style='float: left;'>|</div><div style='float: right;'><b>FINAL</b>|</div><div class='clear'></div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div style='float: left;'>|<b>%s</div> <div style='float: right;'>%d</b>|</div><div class='clear'></div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div style='float: left;'>|%s</div> <div style='float: right;'>%d|</div><div class='clear'></div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div style='float: left;'>|%s</div> <div style='float: right;'>%d|</div><div class='clear'></div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div style='float: left;'>|<b>%s</div> <div style='float: right;'>%d</b>|</div><div class='clear'></div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
						if($querylg->rowCount() > 0){ //If live game doesn't exist
							printf("<div class='schedule-container'><div style='float: left;'>|<span class='red'>LIVE</span></div> <div style='float: right;'>Set %s|</div><div class='clear'></div></div>", $livegame['period']);
						}else{		
							printf("<div class='schedule-container'><div style='float: left;'>|<span class='red'>LIVE</span></div> <div style='float: right;'>|</div><div class='clear'></div></div>");
						}
					printf("<div class='schedule-container'><div style='float: left;'>|%s</div>", $home);
					if($serve == $home){
						printf("<div style='float: left;'>&#9666;</div>");//127952 = volleyball
					}
					printf("<div style='float: right;'>%d|</div><div class='clear'></div></div>", $rowbb['home_total']);
					
					printf("<div class='schedule-container'><div style='float: left;'>|%s</div>", $away);
					if($serve == $away){
						printf("<div style='float: left;'>&#9666;</div>");//127952 = volleyball
					}
					printf("<div style='float: right;'>%d|</div><div class='clear'></div></div>", $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else if($sport == "fhockey" or $sport == "jvfhockey"){
				//gets live game stats
				$sportdb = 'field_hockey' . $affix;
				$sqlbb = "SELECT * FROM live_games AS lg JOIN $schedule AS s ON lg.schedule_id = s.id WHERE s.id='$gameID'";
				$querylg = $db->prepare($sqlbb);
				$querylg->execute();
				$livegame = $querylg->fetch(PDO::FETCH_ASSOC);
				?>			
				<a href="<?php echo $baseUrl?>game/field_hockey.php?gameID=<?php echo $gameID?>" class='schedule-game'>
				+------------------------------+<br>
				<div class='schedule-container'>
				<div style="float: left;">|<b><?php echo $formattedName?></b></div>
				<div style='float: right;'>|</div>
				<div class="clear"></div>				
				</div>
				<?php
				$sqlbb = "SELECT * FROM $sportdb AS fh JOIN $schedule AS s ON fh.schedule_id = s.id WHERE s.id='$gameID'";
				$querybb = $db->prepare($sqlbb);
				$querybb->execute();
				$rowbb = $querybb->fetch(PDO::FETCH_ASSOC);
				if($querybb->rowCount() == 0){
				?>	<div class='schedule-container'>		
					<div style="float: left;">|<?php echo $time?> EST </div><div style="float: right;">@<?php echo $home?>|</div>
					<div class="clear"></div>
					</div>
					<div class='schedule-container'><div style="float: left;">|<?php echo $home?></div><div style="float: right;">|</div><div class="clear"></div></div>
					<div class='schedule-container'><div style="float: left;">|<?php echo $away?></div><div style="float: right;">|</div><div class="clear"></div></div>
				<?php
				}else{
					if($rowbb['completed'] == 1){
						printf("<div class='schedule-container'><div style='float: left;'>|</div><div style='float: right;'><b>FINAL</b>|</div><div class='clear'></div></div>");
						if($rowbb['home_total'] > $rowbb['away_total']){
							printf("<div class='schedule-container'><div style='float: left;'>|<b>%s</div> <div style='float: right;'>%d</b>|</div><div class='clear'></div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div style='float: left;'>|%s</div> <div style='float: right;'>%d|</div><div class='clear'></div></div>", $away, $rowbb['away_total']);
						}else{
							printf("<div class='schedule-container'><div style='float: left;'>|%s</div> <div style='float: right;'>%d|</div><div class='clear'></div></div>", $home, $rowbb['home_total']);
							printf("<div class='schedule-container'><div style='float: left;'>|<b>%s</div> <div style='float: right;'>%d</b>|</div><div class='clear'></div></div>", $away, $rowbb['away_total']);
						}
					}else{
					//Started not completed, grab current quarter time etc. to replace top line
					
					if($querylg->rowCount() > 0){ //If live game doesn't exist
							printf("<div class='schedule-container'><div style='float: left;'>|<span class='red'>LIVE</span></div> <div style='float: right;'>Q%s %s|</div><div class='clear'></div></div>", $livegame['period'], $livegame['game_time']);
						}else{		
							printf("<div class='schedule-container'><div style='float: left;'>|<span class='red'>LIVE</span></div> <div style='float: right;'>|</div><div class='clear'></div></div>");
						}
					printf("<div class='schedule-container'><div style='float: left;'>|%s</div> <div style='float: right;'>%d|</div><div class='clear'></div></div>", $home, $rowbb['home_total']);
					printf("<div class='schedule-container'><div style='float: left;'>|%s</div> <div style='float: right;'>%d|</div><div class='clear'></div></div>", $away, $rowbb['away_total']);
					}
				}
				?>
				+------------------------------+</a><br><br>
				<?php
			}else{
				printf("+------------------------------+<br>");
				printf("<div class='schedule-container'><div style='float: left;'>|<b>%s</b></div><div style='float: right;'>|</div><div class='clear'></div></div>", $row->formattedName);
				printf("<div class='schedule-container'><div style='float: left;'>|%s EST</div> <div style='float: right;'>@%s|</div><div class='clear'></div></div>", $row->time, $row->home);
				printf("<div class='schedule-container'><div style='float: left;'>|%s</div> <div style='float: right;'>|</div><div class='clear'></div></div>", $row->home);
				printf("<div class='schedule-container'><div style='float: left;'>|%s</div> <div style='float: right;'>|</div><div class='clear'></div></div>", $row->away);
				printf("+------------------------------+</div><br><br>");
			}
		}
	}
	
?>