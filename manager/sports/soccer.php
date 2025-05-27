<?php
	
	$actionList = [
	'<option value="Goal scored by ">Goal scored by </option>',
	'<option value="Assist by ">Assist by </option>',
	'<option value="Corner kick by ">Corner kick by </option>',
	'<option value="Save by ">Save by </option>',
	'<option value="Shot by ">Shot by </option>',
	'<option value="Shot block by ">Shot block by </option>',
	'<option value="Foul on ">Foul on </option>',
	'<option value="Offside ">Offside </option>',
	'<option value="Yellow card on ">Yellow card on </option>',
	'<option value="Red card on ">Red card on </option>'];
	
	$actionRadio = [
	'<input type="radio" onclick="bonusAction(this)" id="goal" name="plays" value="Goal scored by "><label for="goal">Goal scored by </label><br>',
	'<input type="radio" id="corner" name="plays" value="Corner kick by "><label for="corner">Corner kick by </label><br>',
	'<input type="radio" onclick="bonusAction(this)" id="shot" name="plays" value="Shot by "><label for="shot">Shot by </label><br>',
	'<input type="radio" id="foul" name="plays" value="Foul on "><label for="foul">Foul on </label><br>',
	'<input type="radio" id="offside" name="plays" value="Offside "><label for="offside">Offside </label><br>',
	'<input type="radio" id="yellow" name="plays" value="Yellow card on "><label for="yellow">Yellow card on </label><br>',
	'<input type="radio" id="red" name="plays" value="Red card on "><label for="red">Red card on </label><br>'];
 
	?>