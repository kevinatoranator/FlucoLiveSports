<!DOCTYPE html>

<head>
	<title>FLS</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="../stylesheet.css">
</head>
<body>
<?php $pagedate = $_GET['date'];
$fdate = date("l, F d", strtotime($pagedate)); 
$baseUrl = '../';?>


<!--Schedule Header-->

    <?php 
	include '../include/database.php';
	include '../include/header.php';
	?>

<br>
<div class="flex justify-between">
<form action='schedule.php' method='get'><input type ='hidden' id='date' name='date' value='<?php echo date("Y-m-d", strtotime("-1 days", strtotime($pagedate))); ?>'><input type='submit' class='schedule' value='< <?php echo date("M. d", strtotime("-1 days", strtotime($pagedate)))?>'></form> <b> <?php echo $fdate ?></b> <form action='schedule.php' method='get'><input type ='hidden' id='date' name='date' value='<?php echo date("Y-m-d", strtotime("+1 days", strtotime($pagedate))); ?>'><input type='submit' class='schedule' value='<?php echo date("M. d", strtotime("+1 days", strtotime($pagedate)))?> >'></form>
</div>
<br>

<div ><input id="fluvanna" type="radio" class="hidden" name="radioheader" value="fluvanna" checked><input id="jefferson" type="radio" class="hidden" name="radioheader" value="jefferson">
<div class="flex justify-evenly"><label for="fluvanna" class="nav" id="fluvlabel">Fluvanna</label><label for="jefferson" class="nav" id="jefflabel">Jefferson</label></div></div>

<br>
<br>



<!--Schedule Body-->
<div id="sched-display"></div>


<script src="//ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
function setSchedule(){
	var date = '<?php echo "$pagedate"; ?>';
	var displaySetting = $("input[name='radioheader']:checked").val();
	if(displaySetting == "jefferson"){
		var filter = "AND (h.district = 'Jefferson' or a.district = 'Jefferson')";
		document.getElementById("jefflabel").innerHTML = "<b>Jefferson</b>";
		document.getElementById("fluvlabel").innerHTML = "Fluvanna";
	}else{
		var filter = "AND (h.short_name = 'FLUV' or a.short_name = 'FLUV')";
		document.getElementById("fluvlabel").innerHTML = "<b>Fluvanna</b>";
		document.getElementById("jefflabel").innerHTML = "Jefferson";
	}
	document.getElementById("sched-display").innerHTML = "";
	var gameInfoText = "";
		$.ajax({
			url: "scheduleFunction.php",
			data: {date: date,
			filter: filter},
			success: function(data){
				var dataArray = $.parseJSON(data);
				//console.log(dataArray);
				gameInfoText = dataArray;
				document.getElementById("sched-display").innerHTML = gameInfoText;
			}});
		document.getElementById("sched-display").innerHTML = gameInfoText;
}
window.onload=function(){
	document.getElementById("fluvanna").addEventListener('click', setSchedule);
	document.getElementById("jefferson").addEventListener('click', setSchedule);
	setSchedule();
	}


</script>
</body>