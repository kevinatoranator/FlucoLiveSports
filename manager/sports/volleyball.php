<?php
	
	
	$actionList = [
	'<option value="Kill by ">Kill by </option>',
	'<option value="Assist by ">Assist by </option>',
	'<option value="Attack error by ">Attack error by </option>',
	'<option value="Service ace by ">Service ace by </option>',
	'<option value="Service error by ">Service error by </option>',
	'<option value="Block by ">Block by </option>',
	'<option value="Sub: ">Sub: </option>'];
	
	$actionRadio = [
	'<input type="radio" onclick="bonusAction(this)" id="kill" name="plays" value="Kill by "><label for="kill">Kill by </label><br>',
	'<input type="radio" onclick="bonusAction(this)" id="attack" name="plays" value="Attack error by "><label for="attack">Attack error by </label><br>',
	'<input type="radio" id="ace" name="plays" value="Service ace by "><label for="ace">Service ace by </label><br>',
	'<input type="radio" id="error" name="plays" value="Service error by "><label for="error">Service error by </label><br>'];

	
?>