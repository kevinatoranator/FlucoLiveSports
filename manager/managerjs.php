<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="stylesheet.css">
</head>


<body>
Manager 2,0
<br>

<br>
<br>
<br>
<button id="gameLoad">Load Game</button>
<br>
Game Info:<div id="gameInfo"></div>


<div id="scoreTable"></div>

<br>
<br>
<br>
<div class="flex justify-between"><div id="homeRoster"></div><div id ="awayRoster"></div></div>

<hr>


<br>
<br>
<div id="timerr" class="hidden">Game Time: <span id = "timer"></span><br><br>
<button id="timerControl">Start</button>
<button id="timerReset">Reset</button></div>
<br>
<div id="goalpitch" class="flex justify-between hidden">Home Goalie/Pitcher:<div id="homegoalpitch"></div>Away Goalie/Pitcher:<div id="awaygoalpitch"></div></div>
<br>
<br>
<div id="manager"></div>
<br>
<br>
<hr>
<h3>-Manual-</h3>

<div id="scoreTableManual"></div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="manager2.js"></script>
</body>
