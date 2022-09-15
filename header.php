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
	
	.top{
		font-size:16px;
	}
	
	ul { /* 取消ul預設的內縮及樣式 */
        margin: 0;
        padding: 0;
        list-style: none;
    }
	

    ul.drop-down-menu {
        display: inline-block;
    }

    ul.drop-down-menu li {
        position: relative;
        white-space: nowrap;   }    

    ul.drop-down-menu > li {
        float: left; /* 只有第一層是靠左對齊*/
    }

     ul.drop-down-menu a {
        background-color: #fff;
        display: block;
        padding: 0 30px;
        text-decoration: none;
        line-height: 56px;

    }
    
    ul.drop-down-menu li:hover > a { /* 滑鼠移入次選單上層按鈕保持變色*/
        background-color: #CCC;
    }
	 ul.drop-down-menu ul {
        position: absolute;
        z-index: 99;
        left: -1px;
        top: 100%;
       min-width: 180px;
    }   

    ul.drop-down-menu ul ul { /*第三層以後的選單出現位置與第二層不同*/
        z-index: 999;
        top: 10px;
        left: 90%;
    }
	  ul.drop-down-menu ul { /*隱藏次選單*/
        display: none;
    }

    ul.drop-down-menu li:hover > ul { /* 滑鼠滑入展開次選單*/
        display: block;
    }
</style>
</head>
<body>

<!-- Navbar (sit on top) -->
<div class="w3-top">
  <div class="w3-white w3-card" id="myNavbar">
    <a href="index.php" class="w3-bar-item w3-button w3-wide"><img src="image/logo.png" height='40px'/></a>
    <!-- Right-sided navbar links -->
	
    <div class="w3-right w3-hide-small top">
	<ul class="drop-down-menu">
      <li><a href="daily_diet.html" class="w3-bar-item w3-button"><i class="glyphicon glyphicon-cutlery"></i>每日飲食紀錄</a></li>
      <li><a href="chart.php" class="w3-bar-item w3-button"><i class="glyphicon glyphicon-search"></i>營養素圖表</a></li>
      <li><a href="menu.php" class="w3-bar-item w3-button"><i class="glyphicon glyphicon-book"></i>推薦菜單</a></li>   
      <li><a class="w3-bar-item w3-button"><i class="glyphicon glyphicon-user"></i>個人檔案</a>
		<ul>
			<li><a href="user_user.php" class="w3-bar-item w3-button">修改檔案</a></li>
			<li><a href="user_logout.php" class="w3-bar-item w3-button">登出</a></li>
		</ul>
	  </li>
	</ul>
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
  <a href="daily_diet.html" onclick="w3_close()" class="w3-bar-item w3-button">每日飲食紀錄</a>
  <a href="chart.php" onclick="w3_close()" class="w3-bar-item w3-button">營養素圖表</a>
  <a href="menu.php" onclick="w3_close()" class="w3-bar-item w3-button">推薦菜單</a>
  <a href="user_user.php" onclick="w3_close()" class="w3-bar-item w3-button">個人檔案</a>
  <a href="user_logout.php" onclick="w3_close()" class="w3-bar-item w3-button">登出</a>
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
