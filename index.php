
<!DOCTYPE html>
<html>
<head>
<title>疾時養身</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="image/logo.png" rel="icon" type="image/x-icon" />
<link rel="stylesheet" href="css/bootstrap-3.3.7.css" type="text/css">
<link rel="stylesheet" href="css/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
<style>
	body,h1,h2,h3,h4,h5,h6 {font-family: "微軟正黑體", sans-serif}
	
	body, html {
		height: 100%;
		line-height: 1.8;
	}
	
	/* Full height image header */
	.bgimg-1 {
		background-position: center;
		background-size: cover;
		background-color: #FF9900;
		min-height: 100%;
	}
	
	
	.icon{
		width: 30px;
		height: 30px;
		border-radius: 30px;
		text-align: center;
		border: #FFF solid 1px;
		color: #FFF;
		line-height: 30px;
		transition: ease-in 0.5s;
		font-size: 8px;
		margin: 10px;
		display: inline-block;
	}
	
	.f:hover{background: #0D47FF; border: #0D47FF solid 1px;}
	.l:hover{background: #039E02; border: #039E02 solid 1px;}
	.g:hover{background: #FF0E12; border: #FF0E12 solid 1px;}
	
	.anima>div{
		color: #FFF;
		margin:30px 15px;
		display: inline-block;
		animation: anima 3s infinite;
		font-size: 28px;
	}
		
	@keyframes anima{
		0%,20%,40%,60%,80%,to{animation-timing-function: cubic-bezier(0.215,0.355,0.610,1.000);}
		0%{opacity: 0; transform: scale3d(.3,.3,.3);}
		20%{transform: scale3d(1.03,1.03,1.03);}
		40%{transform: scale3d(.97,.97,.97);}
		60%{opacity: 1; transform: scale3d(1.07,1.07,1.07);}
		80%{transform: scale3d(.93,.93,.93);}
		to{opacity: 1; transform: scale3d(1,1,1);}
	}
	
	.anima>div:nth-child(1){animation-delay: 0.0s;}
	.anima>div:nth-child(2){animation-delay: 0.1s;}
	.anima>div:nth-child(3){animation-delay: 0.2s;}
	.anima>div:nth-child(4){animation-delay: 0.3s;}
	.anima>div:nth-child(5){animation-delay: 0.4s;}
	.anima>div:nth-child(6){animation-delay: 0.5s;}
	.anima>div:nth-child(7){animation-delay: 0.6s;}
	.anima>div:nth-child(8){animation-delay: 0.7s;}
	.anima>div:nth-child(9){animation-delay: 0.8s;}
	.anima>div:nth-child(10){animation-delay: 0.9s;}
	.anima>div:nth-child(11){animation-delay: 1.0s;}
	.anima>div:nth-child(12){animation-delay: 1.1s;}
	
	
</style>
</head>
<body>
<?php
	session_start();
	include("configure.php");

	#假如$_SESSION['userID']為空值表示沒有登入
	if ($_SESSION['userID'] == null) {
		include("header2.php");
	} else {
		include("header.php"); 
	}
?>

<!-- Header with full-height image -->
<header class="bgimg-1 w3-display-container w3-grayscale-min" id="home">
  <div class="w3-display-left w3-text-white" style="padding:48px">
    <span class="w3-jumbo w3-hide-small"><b>疾時養身</b></span><br>
    <span class="w3-xxlarge w3-hide-large w3-hide-medium" ><b>疾時養身</b></span>
	<div class="anima">
	<div>K</div>	  
	<div>E</div>	  
	<div>E</div>	  
	<div>P</div>	  
	<div>&times;</div>	  
	<div>H</div>	  
	<div>E</div>	  
	<div>A</div>	  
	<div>L</div>	  
	<div>T</div>	  
	<div>H</div>	  
	<div>Y</div>	  
	</div>
    <span class="w3-large" >
	主要針對以下國人常見的六大慢性病<br>
	<i class="glyphicon glyphicon-ok"></i>肺炎
	<i class="glyphicon glyphicon-ok"></i>糖尿病
	<i class="glyphicon glyphicon-ok"></i>高血壓
	<i class="glyphicon glyphicon-ok"></i>呼吸道疾病
	<i class="glyphicon glyphicon-ok"></i>腎臟病
	<i class="glyphicon glyphicon-ok"></i>肝硬化<br><br>
	<i class="glyphicon glyphicon-thumbs-up"></i>依使用者的疾病推薦個人化飲食建議
	<i class="glyphicon glyphicon-thumbs-up"></i>協助控制疾病所需攝取的基本營養
	<i class="glyphicon glyphicon-thumbs-up"></i>以圖表化的方式呈現飲食軌跡<br><br>	
	</span>
    </div> 
  <!--
  <div class="w3-display-bottomleft w3-large" style="padding:24px 48px">
    <a href="http://www.facebook.com"><div class="icon f">F</div></a> 
    <a href="http://line.me"><div class="icon l">L</div></a>
    <a href="http://plus.google.com"><div class="icon g">G+</div></a>
  </div>
  -->
</header>



<!-- Footer -->
<footer class="w3-center w3-light-grey w3-padding-64">
  <a href="#home" class="w3-button w3-light-grey"><i class="fa fa-arrow-up w3-margin-right"></i>To the top</a>
  <div class="w3-xlarge w3-section">
    <i class="fa fa-facebook-official w3-hover-opacity"></i>
    <i class="fa fa-instagram w3-hover-opacity"></i>
    <i class="fa fa-snapchat w3-hover-opacity"></i>
    <i class="fa fa-pinterest-p w3-hover-opacity"></i>
    <i class="fa fa-twitter w3-hover-opacity"></i>
    <i class="fa fa-linkedin w3-hover-opacity"></i>
  </div>
  </footer>
 
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
