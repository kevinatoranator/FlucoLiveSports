<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="../stylesheet.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>



<!--Team List by Season-->

<?php
	include '../include/database.php';
	include '../include/header.php';



	echo "<b>Fall</b><br><br>";

	?><div>
	<a class='sport' href ="./standings.php?sport=fhockey">Varsity Field Hockey</a>
	<a class='sport' href ="./standings.php?sport=jvfhockey">JV Field Hockey</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=football">Varsity Football</a>
	<a class='sport' href ="./standings.php?sport=jvfootball">JV Football</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=vball">Varsity Volleyball</a>
	<a class='sport' href ="./standings.php?sport=jvvball">JV Volleyball</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=cheer">Varsity Cheer</a>
	<a class='sport' href ="./standings.php?sport=jvcheer">JV Cheer</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=golf">Varsity Golf</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=gxc">Girls Varsity Cross Country</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=bxc">Boys Varsity Cross Country</a></div><br><br><br>
	
	<?php
	
	
	
	echo "<b>Winter</b><br><br>";
?>
	<div>
	<a class='sport' href ="./standings.php?sport=gbball">Girls Varsity Basketball</a>
	<a class='sport' href ="./standings.php?sport=jvgbball">Girls JV Basketball</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=bbball">Boys Varsity Basketball</a>
	<a class='sport' href ="./standings.php?sport=jvbbball">Boys JV Basketball</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=gindoor">Girls Varsity Indoor Track</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=bindoor">Boys Varsity Indoor Track</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=wrestling">Varsity Wrestling</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=gswim">Girls Varsity Swim & Dive</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=bswim">Boys Varsity Swim & Dive</a><br><br><br>
	</div><br><br><br><?php
	

	echo "<b>Spring</b><br><br>";
?>
	<div>
	<a class='sport' href ="./standings.php?sport=softball">Varsity Softball</a>
	<a class='sport' href ="./standings.php?sport=jvsoftball">JV Softball</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=baseball">Varsity Baseball</a>
	<a class='sport' href ="./standings.php?sport=jvbaseball">JV Baseball</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=gsoccer">Girls Varsity Soccer</a>
	<a class='sport' href ="./standings.php?sport=jvgsoccer">Girls JV Soccer</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=bsoccer">Boys Varsity Soccer</a>
	<a class='sport' href ="./standings.php?sport=jvbsoccer">Boys JV Soccer</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=glax">Girls Varsity Lacrosse</a>
	<a class='sport' href ="./standings.php?sport=jvglax">Girls JV Lacrosse</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=blax">Boys Varsity Lacrosse</a>
	<a class='sport' href ="./standings.php?sport=jvblax">Boys JV Lacrosse</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=gtennis">Girls Varsity Tennis</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=btennis">Boys Varsity Tennis</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=gtrack">Girls Varsity Outdoor Track</a><br><br><br>
	<a class='sport' href ="./standings.php?sport=btrack">Boys Varsity Outdoor Track</a></div>
	
</body>