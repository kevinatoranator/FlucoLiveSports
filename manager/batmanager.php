<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="stylesheet.css">
</head>


<body>
Manager 2,0

<div class="flex justify-between">
    <a href="../gmindex.php">Return to Home</a>
</div>
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
<div id="timing" class="hidden">Inning: <span id = "period"></span> | Inning: <span id = "period"></span> <span id = "side"></span> | <button id="strike">Strike</button> <button id="ball">Ball</button> <button id="foul">Foul</button></div><br>
<br>
<div id="pitching" class="flex justify-between hidden"></div>
<br>
<br>
<div id="manager"></div>
<br>
<br>
<div id="complete" class="hidden"><button id="completeBtn">Complete</button></div>
<br>
<br>
<hr>
<h3>-Manual-</h3>

<div id="scoreTableManual"></div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script type ="module" src="BatballManager.js"></script>
</body>
