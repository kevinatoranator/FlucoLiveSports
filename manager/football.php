<?php
	
	//MOST SPORTS (Not bat or vball)
	//$minutes = 0;//currently selected minute
	//$seconds = 0;//currently selected second
	//$maximumMinutes = 99;//Maximum minutes for selection
	
	//$quarter = 1;
	
	//Football
	$homeTimeOuts = 3;
	$awayTimeOuts = 3;
	$qb = '';
	//'<option value="Pass by ">Pass by </option>', use reception as auto pass
	$actionList = [
	'<option value="Run by ">Run by </option>',
	'<option value="Incomplete pass to ">Incomplete pass to </option>',
	'<option value="Reception by ">Reception by </option>',
	'<option value="Kickoff by ">Kick by </option>',
	'<option value="Touchdown by ">Touchdown by </option>',
	'<option value="Touchdown reception by ">Touchdown reception by </option>',
	'<option value="Extra point GOOD by ">Extra point GOOD by </option>',
	'<option value="Extra point MISS by ">Extra point MISS by </option>',
	'<option value="Field goal GOOD by ">Field goal GOOD by </option>',
	'<option value="Field goal MISS by ">Field goal MISS by </option>',
	'<option value="2-point conversion GOOD by ">2-point conversion GOOD by </option>',
	'<option value="2-point conversion FAIL by ">2-point conversion FAIL by </option>',
	'<option value="Safety by ">Safety by </option>',
	'<option value="Penalty on ">Penalty on </option>',
	'<option value="Holding on ">Holding on </option>',
	'<option value="Offsides on ">Offsides on </option>',
	'<option value="Pass interference on ">Pass interference on </option>',
	'<option value="False start on ">False start on </option>',
	'<option value="Intentional grounding on ">Intentional grounding on </option>',
	'<option value="Sack by ">Sack by </option>',
	'<option value="Fumble by ">Fumble by </option>',
	'<option value="Fumble recovered by ">Fumble recovered by </option>',
	'<option value="Interception by ">Interception by </option>',
	'<option value="Punt by ">Punt by </option>',
	'<option value="Return by ">Return by </option>',
	'<option value="Timeout">Timeout</option>',
	'<option value="Touchback">Touchback</option>',
	'<option value="-End of Half-">-End of Half-</option>'];
	//Add throw away, where text = $qb throws away, pass attempts +1 but no player takes a target
	//Receiving vs running touchdown
	
?>