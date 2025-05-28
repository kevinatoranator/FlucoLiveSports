<?php
	
	$actionList = [
	'<option value="Goal scored by ">Goal scored by </option>',
	'<option value="Draw control by ">Draw control by </option>',
	'<option value="Shot by ">Shot by </option>',
	'<option value="Shot on goal by ">Shot on goal by </option>',
	'<option value="Turnover by ">Turnover by </option>',
	'<option value="Ball intercepted by ">Ball intercepted by </option>',
	'<option value="Green card on ">Green card on </option>',
	'<option value="Yellow card on ">Yellow card on </option>',
	'<option value="Ground ball pickup by ">Ground ball pickup by </option>',
	'<option value="Clear attempt by ">Clear attempt by </option>',
	'<option value="">-------------</option>',
	'<option value="Assist by ">Assist by </option>',
	'<option value="Save by ">Save by </option>'];
	
	$actionRadio = [
	'<input type="radio" onclick="bonusAction(this)" id="goal" name="plays" value="Goal scored by "><label for="goal">Goal scored by </label><br>',
	'<input type="radio"  id="draw" name="plays" value="Draw control by "><label for="draw">Draw control by </label><br>',
	'<input type="radio" id="shot" name="plays" value="Shot by "><label for="shot">Shot by </label><br>',
	'<input type="radio" id="sog" name="plays" value="Shot on goal by "><label for="sog">Shot on goal by </label><br>',
	'<input type="radio" id="int" name="plays" value="Ball intercepted by "><label for="int">Ball intercepted by </label><br>',
	'<input type="radio" onclick="bonusAction(this)" id="turn" name="plays" value="Turnover by "><label for="turn">Turnover by </label><br>',
	'<input type="radio" id="green" name="plays" value="Green card on "><label for="green">Green card on </label><br>',
	'<input type="radio" id="yellow" name="plays" value="Yellow card on "><label for="yellow">Yellow card on </label><br>',
	'<input type="radio" id="ground" name="plays" value="Ground ball pickup by "><label for="ground">Ground ball pickup by </label><br>',
	'<input type="radio" id="clear" name="plays" value="Clear attempt by "><label for="clear">Clear attempt by </label><br>'];
	
 
	?>