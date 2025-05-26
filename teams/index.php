<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="../stylesheet.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>



<!--Schedule Header-->

    <br>
    <?php 
	include '../include/database.php';
	include '../include/header.php';
	?>

<br>




<!--Team List by Season-->

<?php


	$sql = "SELECT urlName, formattedName FROM roster_teams WHERE season='Fall'";
	echo "<b>Fall</b><br><br>";
	/*$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		printf("<form action='./roster.php' method='get'><input type ='hidden' id='sport' name='sport' value='%s'><input type='submit' class='sport' value='%s'></form>", $row->urlName, $row->formattedName);
	}*/
	?><div>
	<a class='sport' href ="./roster.php?school=FCHS&sport=fhockey">Varsity Field Hockey</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvfhockey">JV Field Hockey</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=football">Varsity Football</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvfootball">JV Football</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=vball">Varsity Volleyball</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvvball">JV Volleyball</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=cheer">Varsity Cheer</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvcheer">JV Cheer</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=golf">Varsity Golf</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=gxc">Girls Varsity Cross Country</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=bxc">Boys Varsity Cross Country</a></div><br><br><br>
	
	<?php
	
	
	
	$sql = "SELECT urlName, formattedName FROM roster_teams WHERE season='Winter'";
	echo "<b>Winter</b><br><br>";
	/*$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		printf("<form action='./roster.php' method='get'><input type ='hidden' id='sport' name='sport' value='%s'><input type='submit' class='sport' value='%s'></form>", $row->urlName, $row->formattedName);
	}*/
	?>
	<div>
	<a class='sport' href ="./roster.php?school=FCHS&sport=gbball">Girls Varsity Basketball</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvgbball">Girls JV Basketball</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=bbball">Boys Varsity Basketball</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvbbball">Boys JV Basketball</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=gindoor">Girls Varsity Indoor Track</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=bindoor">Boys Varsity Indoor Track</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=wrestling">Varsity Wrestling</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=gswim">Girls Varsity Swim & Dive</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=bswim">Boys Varsity Swim & Dive</a><br><br><br>
	</div><br><br><br><?php
	
	$sql = "SELECT urlName, formattedName FROM roster_teams WHERE season='Spring'";
	echo "<b>Spring</b><br><br>";
	/*$query = $db->prepare($sql);
	$query->execute();
	while($row = $query->fetchObject()){
		printf("<form action='./roster.php' method='get'><input type ='hidden' id='sport' name='sport' value='%s'><input type='submit' class='sport' value='%s'></form>", $row->urlName, $row->formattedName);
	}*/
	?>
	<div>
	<a class='sport' href ="./roster.php?school=FCHS&sport=softball">Varsity Softball</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvsoftball">JV Softball</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=baseball">Varsity Baseball</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvbaseball">JV Baseball</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=gsoccer">Girls Varsity Soccer</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvgsoccer">Girls JV Soccer</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=bsoccer">Boys Varsity Soccer</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvbsoccer">Boys JV Soccer</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=glax">Girls Varsity Lacrosse</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvglax">Girls JV Lacrosse</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=blax">Boys Varsity Lacrosse</a>
	<a class='sport' href ="./roster.php?school=FCHS&sport=jvblax">Boys JV Lacrosse</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=gtennis">Girls Varsity Tennis</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=btennis">Boys Varsity Tennis</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=gtrack">Girls Varsity Outdoor Track</a><br><br><br>
	<a class='sport' href ="./roster.php?school=FCHS&sport=btrack">Boys Varsity Outdoor Track</a></div>
	
	

</body>