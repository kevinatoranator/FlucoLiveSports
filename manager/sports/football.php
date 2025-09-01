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
	
	$actionRadio = [
	'<input type="radio" onclick="bonusAction(this)" id="run" name="plays" value="Run by "><label for="run">Run by </label><br>',
	'<input type="radio"  id="ipass" name="plays" value="Incomplete pass to "><label for="ipass">Incomplete pass to </label><br>',
	'<input type="radio" id="rec" name="plays" value="Reception by "><label for="rec">Reception by </label><br>',
	'<input type="radio" onclick="bonusAction(this)" id="kick" name="plays" value="Kick by "><label for="kick">Kick by </label><br>',
	'<input type="radio" id="td" name="plays" value="Touchdown by "><label for="td">Touchdown by </label><br>',
	'<input type="radio" id="tdr" name="plays" value="Touchdown reception by "><label for="tdr">Touchdown reception by </label><br>',
	'<input type="radio" id="epgood" name="plays" value="Extra point GOOD by "><label for="epgood">Extra point GOOD by </label><br>',
	'<input type="radio" id="epmiss" name="plays" value="Extra point MISS by "><label for="epmiss">Extra point MISS by </label><br>',
	'<input type="radio" id="fggood" name="plays" value="Field goal GOOD by "><label for="fggood">Field goal GOOD by </label><br>',
	'<input type="radio" id="fgmiss" name="plays" value="Field goal MISS by "><label for="fgmiss">Field goal MISS by </label><br>',
	'<input type="radio" id="cgood" name="plays" value="2-point conversion GOOD by "><label for="cgood">2-point conversion GOOD by </label><br>',
	'<input type="radio" id="cfail" name="plays" value="2-point conversion FAIL by "><label for="cfail">2-point conversion FAIL by </label><br>',
	'<input type="radio" id="safe" name="plays" value="Safety by "><label for="safe">Safety by </label><br>',
	'<input type="radio" id="pen" name="plays" value="Penalty on "><label for="pen">Penalty on </label><br>',
	'<input type="radio" id="hold" name="plays" value="Holding on "><label for="hold">Holding on </label><br>',
	'<input type="radio" id="off" name="plays" value="Offsides on "><label for="off">Offsides on </label><br>',
	'<input type="radio" id="passi" name="plays" value="Pass interference on "><label for="passi">Pass interference on </label><br>',
	'<input type="radio" id="false" name="plays" value="False start on "><label for="false">False start on </label><br>',
	'<input type="radio" id="ground" name="plays" value="Intentional grounding on "><label for="ground">Intentional grounding on </label><br>',
	'<input type="radio" id="sack" name="plays" value="Sack by "><label for="sack">Sack by </label><br>',
	'<input type="radio" id="fum" name="plays" value="Fumble by "><label for="fum">Fumble by </label><br>',
	'<input type="radio" id="fumr" name="plays" value="Fumble recovered by "><label for="fumr">Fumble recovered by </label><br>',
	'<input type="radio" id="int" name="plays" value="Interception by "><label for="int">Interception by </label><br>',
	'<input type="radio" id="punt" name="plays" value="Punt by "><label for="punt">Punt by </label><br>',
	'<input type="radio" id="ret" name="plays" value="Return by "><label for="ret">Return by </label><br>',
	'<input type="radio" id="time" name="plays" value="Timeout"><label for="time">Timeout</label><br>',
	'<input type="radio" id="tback" name="plays" value="Touchback"><label for="tback">Touchback</label><br>',
	'<input type="radio" id="eoh" name="plays" value="-End of Half-"><label for="eoh">-End of Half-</label><br>'];
	
?>