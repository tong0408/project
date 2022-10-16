<?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	#假如$_SESSION['userID']為空值表示沒有登入
	if ($_SESSION['userID'] == null) {
		echo "<script>alert('請先登入唷！')</script>";
		echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
	} else {
		
		$userID=$_SESSION['userID'];
		$query = "SELECT * FROM `user` WHERE `userID`='$userID'";
		$result = $link->query($query);
	
		#獲取現在登入者的帳號密碼
		foreach ($result as $row) {
			$userID = $row["userid"];
			$Name = $row["name"];
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
<title>疾時養身</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="image/logo.png" rel="icon" type="image/x-icon" />
<link rel="stylesheet" href="css/bootstrap-3.3.7.css" type="text/css">
<link rel="stylesheet" href="css/w3.css">
<link rel="stylesheet" href="css/mine.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<style type="text/css">	
		body{		
			background-color:#FFD79B;
			padding-top:80px;
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
		
		.check{
			color: #CCC;
			font-size: 16px;
			transition: ease-in 0.5s;
			font-weight:bold;
		}
		
		.check:hover{
			color: #FFB03B;
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
				
		
		
		.container1{
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
		.get::-webkit-scrollbar {
  width: 0.5em;
}
  
.get::-webkit-scrollbar-track {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.1);
}
  
.get::-webkit-scrollbar-thumb {
  background-color: #FFD79B;
}
		
</style>
</head>
<body class="bgimg-1">
<?php include("header.php"); ?>
<div class="container1">
<?php

    #抓取使用者資料
    $query = "SELECT * FROM `user` WHERE `userID`='$userID'"; #一開始session抓取的值
	$result = $link->query($query);
	
	#獲取現在登入者的身體資訊
	foreach ($result as $row) {
        $user_weight = $row["weight"];
		$user_BMI = $row["BMI"];
        $user_sport = $row["sport"];
		$user_disease = $row["disease"];
	}

    $user_cal=0;
    #計算出每日應攝取大卡
    ##先判斷出活動量&BMI型態再去判斷
    if($user_sport=="輕度活動"){
        #體重過輕
        if($user_BMI<18.5){
            $user_cal=35*$user_weight;
        }
        #體重正常
        else if($user_BMI<24){
            $user_cal=30*$user_weight;
        }
        #體重異常
        else if(24<=$user_BMI){
            $user_cal=25*$user_weight;
        }
    }
    else if($user_sport=="中度活動"){
        #體重過輕
        if($user_BMI<18.5){
            $user_cal=40*$user_weight;
        }
        #體重正常
        else if($user_BMI<24){
            $user_cal=35*$user_weight;
        }
        #體重異常
        else if(24<=$user_BMI){
            $user_cal=30*$user_weight;
        }
    }
    else{#重度活動
        #體重過輕
        if($user_BMI<18.5){
            $user_cal=45*$user_weight;
        }
        #體重正常
        else if($user_BMI<24){
            $user_cal=40*$user_weight;
        }
        #體重異常
        else if(24<=$user_BMI){
            $user_cal=35*$user_weight;
        }
    }


	function function_category($user_cal){
		global $category_1;
		global $category_2;
		global $category_3;
		global $category_4;
		global $category_5;
		global $category_6;
		global $category_7;
		#設定使用者今天要攝取的六大類(單位:份)(全域變數)
		# 1 = 全穀物雜糧類
		# 2 = 蛋豆魚肉類
		# 3 = 乳品類
		# 4 = 蔬菜類
		# 5 = 水果類
		# 6 = 油脂類
		# 7 = 堅果種子類
		if($user_cal<1500){
			#大卡數<1500
			$category_1=1.5;
			$category_2=3;
			$category_3=1.5;
			$category_4=3;
			$category_5=2;
			$category_6=3;
			$category_7=1;
		}
		else if($user_cal<1800){
			#1500<=大卡數<1800
			$category_1=2.5;
			$category_2=4;
			$category_3=1.5;
			$category_4=3;
			$category_5=2;
			$category_6=3;
			$category_7=1;
		}
		else if($user_cal<2000){
			#1800<=大卡數<2000
			$category_1=3;
			$category_2=5;
			$category_3=1.5;
			$category_4=3;
			$category_5=2;
			$category_6=4;
			$category_7=1;		
		}
		else if($user_cal<2200){
			#2000<=大卡數<2200
			$category_1=3;
			$category_2=6;
			$category_3=1.5;
			$category_4=4;
			$category_5=3;
			$category_6=5;
			$category_7=1;		
		}
		else if($user_cal<2500){
			#2200<=大卡數<2500
			$category_1=3.5;
			$category_2=6;
			$category_3=1.5;
			$category_4=4;
			$category_5=3.5;
			$category_6=5;
			$category_7=1;	
		}
		else if($user_cal<2700){
			#2500<=大卡數<2700
			$category_1=4;
			$category_2=7;
			$category_3=1.5;
			$category_4=5;
			$category_5=4;
			$category_6=6;
			$category_7=1;	
		}
		else{
			#大卡數>2700
			$category_1=4;
			$category_2=8;
			$category_3=2;
			$category_4=5;
			$category_5=4;
			$category_6=7;
			$category_7=1;	
		}
	}
	
	
	#預設目標 醣類(碳水化合物)55%/4 蛋白質15%/4 脂質30%/9
	function function_goal($user_cal){
		global $goal_glyco;
	 	global $goal_fat;
	 	global $goal_protein;
		#目標醣類
		$goal_glyco=($user_cal*0.55)/4;
		$goal_glyco=round($goal_glyco);
		#目標蛋白質
		$goal_protein=($user_cal*0.15)/4;
		$goal_protein=round($goal_protein);
		#目標脂質
		$goal_fat=($user_cal*0.3)/9;
		$goal_fat=round($goal_fat);

	}


	#設定普通人的大卡數六大類份數與三大營養素目標
	function_category($user_cal);
	function_goal($user_cal);
	
	#針對疾病及調整
	if($user_disease=="肺炎"){
		#大卡數調整 體重*35+250大卡
		$user_cal=($user_weight*35)+250;
		#重新計算大卡數所需六大類數值與目標值
		function_category($user_cal);
		function_goal($user_cal);
		#蛋白質調整 體重*1.5g
		$goal_protein=$user_weight*1.5;
	}
	else if($user_disease=="糖尿病"){
		#醣類調整 總熱量的50%/4
		$goal_glyco=($user_cal*0.5)/4;
		$goal_glyco=round($goal_glyco);
		#蛋白質調整 總熱量的25%/4
		$goal_protein=($user_cal*0.25)/4;
		$goal_protein=round($goal_protein);
		#脂質調整 總熱量的25%/9
		$goal_fat=($user_cal*0.25)/9;
		$goal_fat=round($goal_fat);
	}
	else if($user_disease=="高血壓"){
		#白肉代替紅肉
		#低鈉飲食 每日食鹽量<6g
	}
	else if($user_disease=="慢性下呼吸道疾病"){
		#蛋白質調整 體重*1.5g
		$goal_protein=$user_weight*1.5;
	}
	else if($user_disease=="慢性腎臟疾病"){
		#蛋白質調整 體重*0.6g
		$goal_protein=$user_weight*0.6;
	}
	else if($user_disease=="肝硬化"){
		#蛋白質調整 100g
		$goal_protein=100;
		#澱粉類攝取 450g 薯類60g
		#蔬菜類攝取 黃綠色蔬菜100g 淺色蔬菜200g
		#水果類調整 200g
		#脂肪調整 總熱量的25%/9
		$goal_fat=($user_cal*0.25)/9;
		$goal_fat=round($goal_fat);
	}

    echo "<h3><b>".$Name." 您好！</b></h3><br>
    您的疾病為：「".$user_disease."」，目前BMI為：".$user_BMI."，一天本來建議攝取".$user_cal."大卡。<br>
    每日建議攝取量：全榖雜糧類：".$category_1."份  蛋豆魚肉類：".$category_2."份 乳品類：".$category_3."份  蔬菜類：".$category_4."份  水果類：".$category_5."份 油脂類：".$category_6."份  堅果種子類：".$category_7."份<br>
    根據疾病調整過後，目標醣類：$goal_glyco g、脂質：$goal_fat g、蛋白質：$goal_protein g，加起來是：".($goal_glyco*4+$goal_fat*9+$goal_protein*4)."<br><br>
	<b><font size='5'>以下推薦幾道菜單讓您選擇！</font></b><br>";
?>
			<div class="box" id="left">
				<table width="200">				
				<?PHP			
					$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
					$query = "SELECT count(dishID) FROM t_menugetid ";
					$result = $link->query($query);
					$count = $result->fetchColumn();

					if($count==0){
						$query = "SELECT DISTINCT recipe.dishID,dish.dishname,dish.method FROM recipe INNER JOIN dish on recipe.dishID = dish.ID LIMIT 9";
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
							'<td class="dish" onClick="showtitle(event);" title="'.nl2br($method).'" id="'.$dishID.'">'.$dishname.'</td></tr>';
						}
					}else{
						$query = "SELECT dishID FROM t_menugetid ";
						$result = $link->query($query);
						foreach ($result as $r){
							
							$t_dishID=$r['dishID'];

							$query = "SELECT DISTINCT recipe.dishID,dish.dishname,dish.method FROM recipe INNER JOIN dish on recipe.dishID = dish.ID WHERE `recipe`.dishID=$t_dishID";
							$result = $link->query($query);	

							foreach ($result as $row){				
								//取得欄位數量
								$dishID = $row['dishID'];
								$dishname = $row['dishname'];
								$method = $row['method'];

								//顯示結果
								echo '<tr>'.
								//菜名
								'<td class="dish" onClick="showtitle(event);" title="'.nl2br($method).'" id="'.$dishID.'">'.$dishname.'</td></tr>';
							}
						}
					}
					//刪除t_menugetid的資料
					$sql = "DELETE FROM `t_menugetid` ";
					// 用mysqli_query方法執行(sql語法)將結果存在變數中
					$count = $link->exec($sql);				
				?>
				<tr><td class="check"><a href="menu_button.php">查看更多</a></td></tr>
				</table>
			</div>
			<div class="container2">
				<div class="box" id="right">				
					點選菜名查看料理方式<br><br>
					<div id="getdish">&nbsp;</div>
					<div class="get" id="gettitle">&nbsp;</div>
				</div>
				<div class="box" id="right">
					<div class="get" id="getid"><br></div>
				</div>
			</div>
</div>
<br>
依據缺少的營養素去做推薦菜單<br>
（去比對資料庫內該營養素較相符合的，如果吃了導致其他營養素過量，也要篩選）<br>

</body>
<script>
	
	function showtitle(event){
		var getdish=event.target.innerHTML;//當觸發了 click處而 innerHTML就是指事件發生位置
		var e=document.getElementById("getdish");//用來取得頁面中 getdish id 的值
		e.className=""
		setTimeout(function(){e.className='anima'},0)//設定為0秒的延遲，並抓取style中的anima來當class
		document.getElementById("getdish").innerHTML=getdish;//變更網頁ID(getdish)位置的文字為設定的(var getdish
		
		var gettitle=event.target.title;//當觸發了 click，會抓取title資料
		document.getElementById("gettitle").innerHTML=gettitle;//變更網頁ID(gettitle)位置的文字為設定的(var gettitle)值
		var dishid = event.target.id;
		//document.getElementById("getid").innerHTML=dishid;

		$(document).ready(function () {
			
			load_data(dishid);
				
			function load_data(query) {
				$.ajax({
					url: "menu_load.php",
					method: "GET",
					data: {
						s: query
					},
					success: function (data) {
						$('#getid').html(data);
					}
				});
			}
		});
		
	}
	
</script>
</html>
