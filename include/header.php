 
	<script>
	
	const d = new Date();
	d.setTime(d.getTime() + (365*24*60*60*1000));
	let expires = "expires=" + d.toUTCString() + ";";
	
	if(getCookie("isDarkMode") == ""){
		document.cookie = "isDarkMode=false;" + expires;
	}
	
	let isDarkMode = getCookie("isDarkMode");

	if(isDarkMode == "true"){
		var element = document.body;
		element.classList.add("dark-mode");
	}
	
	function getCookie(cname){
		let name = cname + "=";
		let decodedCookie = decodeURIComponent(document.cookie);
		let ca = decodedCookie.split(';');
		for(let i = 0; i<ca.length; i++){
			let c = ca[i];
			while(c.charAt(0) == ' '){
				c = c.substring(1);
			}
			if(c.indexOf(name) == 0){
				return c.substring(name.length, c.length);
			}
		}
		return"";
	}
	
	function toggleDarkMode() {
		var element = document.body;
		element.classList.toggle("dark-mode");
		
		if(element.classList.contains("dark-mode")){
			document.cookie = "isDarkMode=true;" + expires;
		}else{
			document.cookie = "isDarkMode=false;" + expires;
		}
	}
	
	</script>
 
 
	<div class="flex justify-between">
        <a href ="#" onclick="return toggleDarkMode();">Dark Mode</a>
        <a href ="<?php echo $phpURL?>">Reload</a>
    </div>
	<br>
	<div class="flex justify-between">
        <a href ="/flucolivesports/index.php">Schedule</a>
		<a href ="/flucolivesports/schedule/district/index.php">District Schedule</a>
    </div>
    <br>
    <div class="flex justify-between">
        <a href ="/flucolivesports/teams/index.php">Teams</a>
		<a href ="/flucolivesports/teams/district/index.php">District Teams</a>
	</div>
	<br>
    <div class="flex justify-between">
        <a href ="/flucolivesports/standings/index.php">Standings</a>
		<!--<a href ="/flucolivesports/standings/index.php">TEMP</a>-->
	</div>
	<br>