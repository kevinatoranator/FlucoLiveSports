<?php
	
	$actionList = [
	'<option value="Goal scored by ">Goal scored by </option>',
	'<option value="Assist by ">Assist by </option>',
	'<option value="Penalty corner by ">Penalty corner by </option>',
	'<option value="Save by ">Save by </option>',
	'<option value="Shot by ">Shot by </option>',
	'<option value="Shot on goal by ">Shot on goal by </option>',
	'<option value="Penalty on ">Penalty on </option>',
	'<option value="Green card on ">Green card on </option>',
	'<option value="Yellow card on ">Yellow card on </option>'];
	
	$actionRadio = [
	'<input type="radio" onclick="bonusAction(this)" id="goal" name="plays" value="Goal scored by "><label for="goal">Goal scored by </label><br>',
	'<input type="radio"  id="corner" name="plays" value="Penalty corner by "><label for="corner">Penalty corner by </label><br>',
	'<input type="radio" id="shot" name="plays" value="Shot by "><label for="shot">Shot by </label><br>',
	'<input type="radio" onclick="bonusAction(this)" id="sog" name="plays" value="Shot on goal by "><label for="sog">Shot on goal by </label><br>',
	'<input type="radio" id="pen" name="plays" value="Penalty on "><label for="pen">Penalty on </label><br>',
	'<input type="radio" id="green" name="plays" value="Green card on "><label for="green">Green card on </label><br>',
	'<input type="radio" id="yellow" name="plays" value="Yellow card on "><label for="yellow">Yellow card on </label><br>'];
	
 
	?>