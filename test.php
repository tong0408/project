<?php
	session_start();
	include("configure.php");
?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>活動日期</title>
		<link rel="stylesheet" href="css/bootstrap-3.3.7.css" type="text/css">
		<link rel="stylesheet" href="css/w3.css">
		<link rel="stylesheet" href="css/mine.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
	<style type="text/css">	
		body, html {		
		background-color:#FFD79B;
		}
		table{
			height: auto; 
			margin: auto;
			text-align:center;
		}
		td{
			width: 50px; 
			height: 50px;
			border:solid 0px;
		}
		.dish{
			color: #FFB03B;
			font-size: 20px;
			transition: ease-in 0.5s;
			font-weight:bold;
		}
		
		.dish:hover{
			background: #FFB03B;
			color: #FFF;
			cursor: pointer;
		}
		
		.box{
			height: 500px;
			display: inline-block;
			color: #FFF;			
		}
		
		#right{
			background: #FFB03B;
		}
		
		#getdish{
			font-size: 36px;
			height: 150px;
			font-weight:bold;
			line-height: 150px;
		}
		
		#gettitle{
			font-size: 20px;			
		}
		
		.get{			
			height: 300px;
			width:400px;
			overflow: auto;	
			text-align:left;
			padding:10px;
			display:inline-block;
		}		
		.anima{
			animation: anima 1s;
		}
		
		@keyframes anima{
			from{opacity: 0; transform: rotate(-720deg);}
			to{opacity: 1; transform: rotate(0deg);}
		}
				
		
		
		.container{
			width: 1100px; 
			text-align:center;
			background:#FFF; 
			margin:auto;
			padding:20px;
			border-radius:20px;
		}
		
		.container2{
			width: 850px; 
			text-align:center;
			background:#FFB03B; 
			margin:auto;
			padding:20px;
			border-radius:20px;
			display:inline-block;
		}
		
		#getid{
			height:508px;
			color:#FFF;
		}
	</style>
	
	<body>
		
		<div class="container">
			<div class="box" id="left">
			<table width="200">				
			<?PHP			
				$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
				$query = "SELECT DISTINCT recipe.dishID,dish.dishname,dish.method FROM recipe INNER JOIN dish on recipe.dishID = dish.ID LIMIT 5";
				$result = $link->query($query);	
				
				//取得所有需要的資料
				foreach ($result as $row){					
					//取得欄位數量
					$dishID = $row['dishID'];
					$dishname = $row['dishname'];
					$method = $row['method'];					
					//顯示結果
					echo '<tr>'.
					//菜名
					'<td class="dish" onClick="showtitle(event)" title="'.nl2br($method).'" id="'.$dishID.'">'.$dishname.'</td></tr>';
					}					
			?>
			</table>
			</div>
			<div class="container2">
			<div class="box" id="right">				
				點選菜名查看料理方式<br><br>
				<div id="getdish">&nbsp;</div>
				<div class="get" id="gettitle">&nbsp;</div>
			</div>
			<div class="box" id="right">
				<div class="get" id="getid">
				<br>
				<table style="color:#FFF; font-size:20px; width:300px;">
				<?PHP
					$query1 = "SELECT recipe.portion,ingredients.name FROM recipe INNER JOIN ingredients on recipe.iID = ingredients.iID WHERE recipe.dishID=1";
					$result1 = $link->query($query1);	
				
					//取得所有需要的資料
					foreach ($result1 as $row1){
					
					$portion = $row1['portion'];
					$iID_Name=$row1['name'];
					
					
					echo '<tr>'.
					//菜名
					'<td>'.$iID_Name.'</td><td>'.$portion.'克</td></tr>';

					
					}
				?>
				</table>
				</div>
			</div>
			</div>
		</div>
	</body>
	<script>
		function showtitle(event){
			var getdish=event.target.innerHTML;//當觸發了 click處而 innerHTML就是指事件發生位置
			var e=document.getElementById("getdish");//用來取得頁面中 getdish id 的值
			e.className=""
			setTimeout(function(){e.className='anima'},0)//設定為0秒的延遲，並抓取style中的anima來當class
			document.getElementById("getdish").innerHTML=getdish;//變更網頁ID(getdish)位置的文字為設定的(var getdish)值
			
			var gettitle=event.target.title;//當觸發了 click，會抓取title資料
			document.getElementById("gettitle").innerHTML=gettitle;//變更網頁ID(gettitle)位置的文字為設定的(var gettitle)值
			//var dishid = event.target.id;
			//document.getElementById("getid").innerHTML=dishid;
			
		}	
			
		
	</script>
	
</html>
