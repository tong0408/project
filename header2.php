<!DOCTYPE html>
<html>
	<head>
		<title>疾時養身</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="image/logo.png" rel="icon" type="image/x-icon" />
		<link rel="stylesheet" href="css/w3.css">
		<link rel="stylesheet" href="css/bootstrap-3.3.7.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<style>
		
			
			.top{
				font-size:16px;
			}
			
			
		</style>
	</head>
	<body>	
	<!-- Navbar (sit on top) -->
	<div class="w3-top">
	<div class="w3-bar w3-white w3-card" id="myNavbar">
		<a href="#home" class="w3-bar-item w3-button w3-wide"><img src="image/logo.png" height='40px'/></a>
		<!-- Right-sided navbar links -->
		<div class="w3-right w3-hide-small top">
		<a href="user_login.php" class="w3-bar-item w3-button"><i class="glyphicon glyphicon-bell"></i>登入</a>
		<a href="user_create.php" class="w3-bar-item w3-button"><i class="glyphicon glyphicon-tag"></i>註冊</a>
		</div>
		<!-- Hide right-floated links on small screens and replace them with a menu icon -->
	
		<a href="javascript:void(0)" class="w3-bar-item w3-button w3-right w3-hide-large w3-hide-medium" onclick="w3_open()">
		<i class="fa fa-bars"></i>
		</a>
	</div>
	</div>
	
	<!-- Sidebar on small screens when clicking the menu icon -->
	<nav class="w3-sidebar w3-bar-block w3-black w3-card w3-animate-left w3-hide-medium w3-hide-large" style="display:none" id="mySidebar">
	<a href="javascript:void(0)" onclick="w3_close()" class="w3-bar-item w3-button w3-padding-16">×</a>
	<a href="user_login.php" onclick="w3_close()" class="w3-bar-item w3-button">登入</a>
	<a href="user_create.php" onclick="w3_close()" class="w3-bar-item w3-button">註冊</a>
	</nav>
	
	
	
	<script>
		// Modal Image Gallery
		function onClick(element) {
		document.getElementById("img01").src = element.src;
		document.getElementById("modal01").style.display = "block";
		var captionText = document.getElementById("caption");
		captionText.innerHTML = element.alt;
		}
		
		
		// Toggle between showing and hiding the sidebar when clicking the menu icon
		var mySidebar = document.getElementById("mySidebar");
		
		function w3_open() {
		if (mySidebar.style.display === 'block') {
			mySidebar.style.display = 'none';
		} else {
			mySidebar.style.display = 'block';
		}
		}
		
		// Close the sidebar with the close button
		function w3_close() {
			mySidebar.style.display = "none";
		}
	</script>
	
	</body>
</html>
