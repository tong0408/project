<?php
	session_start();
	include("configure.php");

	#假如$_SESSION['userID']為空值表示沒有登入
	if ($_SESSION['userID'] == null) {
		echo "<script>alert('請先登入唷！')</script>";
		echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
	} else {
		$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
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
<style>
	body, html {		
		background-color:#FFD79B;
	}
	
	td{
		width:50px;
		padding:10px;
	}
	tbody:hover {
		background-color: rgba(200,200,200,0.5);
	}
	.container1{
		width:80%; 
		height:auto; 
		background:#FFF; 
		margin:auto;
		padding:30px;
		border-radius:20px;
		
	}	
</style>
</head>
<body>
中間上方<br>
使用者吃了多少營養素<br>
缺少營養素克數<br>
（需要抓資料庫吃的資料及疾病的營養素公式跟使用者的個人資料（疾病部分））<br><br>
<div class="container1">
<?php

    #抓取使用者資料
    $link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	$query = "SELECT * FROM `user` WHERE `userID`='$userID'"; #一開始session抓取的值
	$result = $link->query($query);
	
	#獲取現在登入者的帳號密碼
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

    echo "<b>".$Name."您好！</b><br>
    您的疾病為：「".$user_disease."」，目前BMI為：".$user_BMI."，一天建議攝取".$user_cal."大卡。<br>
    每日建議攝取量：全榖雜糧類：份  蛋豆魚肉類：份 乳品類：份  蔬菜類：份  水果類：份 油脂類：份  堅果種子類：份<br><br>
    <b>目前已攝取的營養素為：全榖雜糧類：份  蛋豆魚肉類：份 乳品類：份  蔬菜類：份  水果類：份 油脂類：份  堅果種子類：份<br>
    缺少的營養素為：全榖雜糧類：份  蛋豆魚肉類：份 乳品類：份  蔬菜類：份  水果類：份 油脂類：份  堅果種子類：份</b><br>
    以下推薦幾道菜單讓您選擇！";
?>
	<table style="margin:auto; width:80%; text-align:center;"  border="1px solid #CCC">
		<tr><td >菜名</td><td style="width:130px;">作法</td><td>食材</td><td>克數</td></tr>
			<?PHP			
				$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
				$query = "SELECT DISTINCT recipe.dishID,dish.dishname,dish.method FROM recipe INNER JOIN dish on recipe.dishID = dish.ID LIMIT 10";
				$result = $link->query($query);	
				
				//取得所有需要的資料
				foreach ($result as $row){
					
					//取得欄位數量
					$dishID = $row['dishID'];
					$dishname = $row['dishname'];
					$method = $row['method'];
					$count = $link->prepare("SELECT * FROM recipe WHERE `dishID`='$dishID'");   
					$count->execute();   
					$count_rows=$count->rowCount(); 
					
					//顯示結果
					echo '<tbody><tr>'.
					//菜名
					'<td style="height:50px;" rowspan="'.$count_rows.'">'.$dishname.'</td>';					
					echo '<td style="height:50px;" rowspan="'.$count_rows.'">'.$method.'</td>';

					//取得菜ID使用的食材ID
					$query = "SELECT * FROM recipe where dishID='$dishID'";
					$re = $link->query($query);
					foreach ($re as $r){
						$dish_iID=$r['iID'];
						$portion=$r['portion'];

						//從食材ID取得食材類別ID和食材名稱<使用>
						$query = "SELECT * FROM ingredients where iID='$dish_iID'";
						$re = $link->query($query);
						foreach ($re as $r){
							$iID_NID=$r['NID'];
							$iID_Name=$r['name'];
							//食材
							echo '<td style="height:50px;">'.$iID_Name.'</td>'.
							//分類
							'<td style="height:50px;">'.$portion.'克</td></tr>';								
															
							}						
						}
					
					}
					echo '</tbody>';
			
			?>
	</table>
</div>
<br>
下方會是依據缺少的營養素去做推薦菜單<br>
（去比對資料庫內該營養素較相符合的，如果吃了導致其他營養素過量，也要篩選）<br>

</body>
</html>
