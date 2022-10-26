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
		$user_disease2 = $row["disease2"];
		$user_disease3 = $row["disease3"];
		$user_disease4 = $row["disease4"];
		$user_disease5 = $row["disease5"];
		$user_disease6 = $row["disease6"];
		$user_disease7 = $row["disease7"];
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
		global $goal_category;
		#設定使用者今天要攝取的六大類(單位:份)(全域變數)
		# 1 = 全穀物雜糧類
		# 2 = 蛋豆魚肉類
		# 3 = 乳品類
		# 4 = 蔬菜類
		# 5 = 水果類
		# 6 = 油脂類與堅果種子類

		if($user_cal<1500){
			#大卡數<1500
			$goal_category=array(1.5,3,1.5,3,2,4);
		}
		else if($user_cal<1800){
			#1500<=大卡數<1800
			$goal_category=array(2.5,4,1.5,3,2,4);
		}
		else if($user_cal<2000){
			#1800<=大卡數<2000
			$goal_category=array(3,5,1.5,3,2,5);
		}
		else if($user_cal<2200){
			#2000<=大卡數<2200
			$goal_category=array(3,6,1.5,4,3,6);
		}
		else if($user_cal<2500){
			#2200<=大卡數<2500
			$goal_category=array(3.5,6,1.5,4,3.5,6);
		}
		else if($user_cal<2700){
			#2500<=大卡數<2700
			$goal_category=array(4,7,1.5,5,4,7);
		}
		else{
			#大卡數=>2700
			$goal_category=array(4,8,2,5,4,8);
		}
	}
	
	
	#預設目標 醣類(碳水化合物)55%/4 蛋白質15%/4 脂質30%/9
	function function_goal($user_cal){
		global $goal_glyco;
	 	global $goal_fat;
	 	global $goal_protein;
		global $goal_suger;
		global $goal_sodium;

		#目標醣類
		$goal_glyco=($user_cal*0.55)/4;
		$goal_glyco=round($goal_glyco);
		#目標蛋白質
		$goal_protein=($user_cal*0.15)/4;
		$goal_protein=round($goal_protein);
		#目標脂質
		$goal_fat=($user_cal*0.3)/9;
		$goal_fat=round($goal_fat);
		#目標糖份攝取
		$goal_suger=($user_cal*0.1)/4;#為生福利部 國人糖攝取量上限
		#目標鈉含量
		$goal_sodium=2.4;#衛生福利部建議每天吃2400mg的鈉(=2.4g)
	}


	#設定普通人的大卡數六大類份數與三大營養素目標
	function_category($user_cal);
	function_goal($user_cal);
	
	#針對疾病及調整的三大營養素目標
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
		#世界衛生組織建議糖份攝取低於所需能量(熱量)的10%
		$goal_suger=($user_cal*0.1)/4;
	}
	else if($user_disease=="高血壓"){
		#白肉代替紅肉
		#低鈉飲食 每日食鹽量<6g!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!待修正
		$goal_sodium=6;
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

	#假如第二種以上疾病患有糖尿病
	#疾病+糖尿病
	if($user_disease2=="糖尿病" || $user_disease3 =="糖尿病" || $user_disease4 =="糖尿病" || 
		$user_disease5 =="糖尿病" ||$user_disease6 =="糖尿病" || $user_disease7 =="糖尿病"){
		#醣類調整 總熱量的50%/4
		$goal_glyco=($user_cal*0.5)/4;
		$goal_glyco=round($goal_glyco);
		#脂質調整 總熱量的25%/9
		$goal_fat=($user_cal*0.25)/9;
		$goal_fat=round($goal_fat);
		
		#疾病+糖尿病+慢性腎臟疾病
		if($user_disease3=="慢性腎臟疾病" || $user_disease4=="慢性腎臟疾病" || $user_disease5=="慢性腎臟疾病" ||
			$user_disease6=="慢性腎臟疾病" || $user_disease7=="慢性腎臟疾病") {
			#蛋白質調整 體重*0.6g
			$goal_protein=$user_weight*0.6;
		}
	}

	#第一個疾病為糖尿病+其他疾病時
	if($user_disease=="糖尿病"){
		#糖尿病+肝硬化疾病
		if($user_disease2=="肝硬化" || $user_disease3=="肝硬化" || $user_disease4=="肝硬化" ||
		$user_disease5=="肝硬化" || $user_disease6=="肝硬化" || $user_disease7=="肝硬化"){
			$goal_protein=100;
		}
		else{
			$goal_protein=($user_cal*0.15)/4;
			$goal_protein=round($goal_protein);
		}
	}

	#假如疾病第二種以上患有慢性腎臟疾病
	#疾病+慢性腎臟疾病
	if($user_disease2=="慢性腎臟疾病" || $user_disease3=="慢性腎臟疾病" || $user_disease4=="慢性腎臟疾病" ||
		$user_disease5=="慢性腎臟疾病" || $user_disease6=="慢性腎臟疾病" || $user_disease7=="慢性腎臟疾病") {
		#蛋白質調整 體重*0.6g
		$goal_protein=$user_weight*0.6;
	}

	#假如疾病第二種以上患有肝硬化
	if($user_disease2=="肝硬化" || $user_disease3=="肝硬化" || $user_disease4=="肝硬化" ||
	$user_disease5=="肝硬化" || $user_disease6=="肝硬化" || $user_disease7=="肝硬化") {
	$goal_fat=($user_cal*0.25)/9;
	$goal_fat=round($goal_fat);
	}

	#最後計算總大卡數和調整六大類營養素
	$user_goal_cal=$goal_glyco*4+$goal_fat*9+$goal_protein*4;
	function_category($user_goal_cal);

	include("menu_decide.php");

	#抓取資料庫獲取目前吃的營養素
	/**$now_cal=0;
	$now_category_1=0; #全榖雜糧，每份醣類15g
	$now_category_2=0; #蛋豆魚肉，每份蛋白質7g
	$now_category_3=0; #乳品類，每份蛋白質8g
	$now_category_4=0; #蔬菜類，每份25大卡
	$now_category_5=0; #水果類，每份60大卡
	$now_category_6=0; #油脂與堅果種子類，每份脂肪5g
	$now_glyco=0;
	$now_fat=0;
	$now_protein=0;**/



    echo "<h3><b>".$Name." 您好！</b></h3><br>
	您的疾病為：「";
	echo $user_disease;
	if($user_disease2!=0){echo "、".$user_disease2;}
	if($user_disease3!=0){echo "、".$user_disease3;}
	if($user_disease4!=0){echo "、".$user_disease4;}
	if($user_disease5!=0){echo "、".$user_disease5;}
	if($user_disease6!=0){echo "、".$user_disease6;}
	echo "」，目前BMI為：".$user_BMI."。一天建議攝取".$user_goal_cal."大卡。<br>
    <hr><h4><b>每日建議攝取量－目標熱量 $now_cal/".$user_goal_cal."大卡</b></h4>
	<table width='95%'>
	<tr>
	<td><b>全榖雜糧類</b></td>
	<td><b>蛋豆魚肉類</b></td>
	<td><b>乳品類</b></td>
	<td><b>蔬菜類</b></td>
	<td><b>水果類</b></td>
	<td><b>油脂與堅果種子類</b></td>
	<td><b>醣類</b></td>
	<td><b>脂質</b></td>
	<td><b>蛋白質</b></td>
	<td><b>總糖份</b></td>
	<td><b>鈉含量</b></td>
	</tr>
	<tr>
	<td><b>$now_category[0]/$goal_category[0] 份</b></td>
	<td><b>$now_category[1]/$goal_category[1] 份</b></td>
	<td><b>$now_category[2]/$goal_category[2] 份</b></td>
	<td><b>$now_category[3]/$goal_category[3] 份</b></td>
	<td><b>$now_category[4]/$goal_category[4] 份</b></td>
	<td><b>$now_category[5]/$goal_category[5] 份</b></td>
	<td><b>$now_glyco/$goal_glyco g</b></td>
	<td><b>$now_fat/$goal_fat g</b></td>
	<td><b>$now_protein/$goal_protein g</b></td>
	<td><b>$now_suger/$goal_suger g</td><b>
	<td><b>$now_sodium/$goal_sodium g</b></td>
	</tr>
	</table>
	<hr>
	<b><font size='5'>以下推薦幾道菜單讓您選擇！</font></b><br>";
?>
	<div class="box" id="left">
		<table width="200">				
			<?PHP			
				$query = "SELECT count(dishID) FROM t_menugetid ";
				$result = $link->query($query);
				$count = $result->fetchColumn();
				if($count==0){
					$query = "SELECT DISTINCT recipe.dishID,dish.dishname,dish.method FROM recipe INNER JOIN dish on recipe.dishID = dish.ID LIMIT 9";
					$result = $link->query($query);	

					//取得所有需要的資料
					foreach ($result as $row){				
						//取得欄位數量
						$i=0;
						while($i<10){
							$tmp = rand(0,$count_use_recommend_dishID);
							$i++;
							break;
						}
						$dishID = $use_recommend_dishID[$tmp];
						$dishname = $use_recommend_dishName[$tmp];
						$method = $use_recommend_method[$tmp];
							
						//顯示結果
						echo '<tr>'.
						//菜名
						'<td class="dish" onClick="showtitle(event);" title="'.nl2br($method).'" id="'.$dishID.'">'.$dishname.'</td></tr>';
					}
				} 
				else {
					$query = "SELECT dishID FROM t_menugetid ";
					$result = $link->query($query);
					foreach ($result as $r){
						
						$t_dishID=$r['dishID'];

						$query = "SELECT DISTINCT recipe.dishID,dish.dishname,dish.method FROM recipe INNER JOIN dish on recipe.dishID = dish.ID WHERE `recipe`.dishID=$t_dishID";
						$result = $link->query($query);	

					//取得所有需要的資料
					foreach ($result as $row){				
							//取得欄位數量
							$i=0;
							while($i<10){
								$tmp = rand(0,$count_use_recommend_dishID);
								$i++;
								break;
							}
							$dishID = $use_recommend_dishID[$tmp];
							$dishname = $use_recommend_dishName[$tmp];
							$method = $use_recommend_method[$tmp];
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
			<b>點選左側菜名查看料理方式</b><br><br>
				<div id="getdish">&nbsp;</div>
				<div class="get" id="gettitle">&nbsp;</div>
			</div>
			<div class="box" id="right">
				<div class="get" id="getid"><br></div>
			</div>
		</div>
	</div>
</div>
<br>

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
