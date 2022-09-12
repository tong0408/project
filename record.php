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
<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
<style>
	td{height:80px;}
	
	tbody:hover {
		background-color: rgba(200,200,200,0.5);
	}
</style>
</head>
<body>
<a href="daily_diet.html"><button class="btn1 return">返回</button></a>
     <div class="form1" style="text-align:left;">
		親愛的 <?php echo $Name;?> 您好！以下為您的飲食紀錄：
		<form method="POST" action="record.php">
			日期：<input type="date" name="get_date" value="<?php echo date('Y-m-d'); ?>" required >
			<input type="submit" class="btn" value="查詢" style="margin:10px 10px 10px 10px;">
		</form>	
		<table style="margin:auto; width:80%; text-align:center;"  border="1px solid #CCC">
			<tr><td>時間</td><td>菜名</td><td>食材</td><td>分類</td><td>份量</td></tr>
			<?PHP

				//判斷日期
				$today = date('Y-m-d');
				$get_date=isset($_POST["get_date"]) ? $_POST["get_date"] : $today;

				//連接歷史紀錄資料表（限制日期）
				$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
				$query = "SELECT * FROM `history` WHERE `UID`='$userID' and `date`='$get_date'";
				$result = $link->query($query);	
				

				echo "選擇的日期是：$get_date";
				
				//取得所有需要的資料
				foreach ($result as $row){
					
					//取得欄位數量
					$dishID = $row['dishID'];
					$count = $link->prepare("SELECT * FROM recipe WHERE `dishID`='$dishID'");   
					$count->execute();   
					$count_rows=$count->rowCount(); 
					
					//顯示結果
					echo '<tbody><tr>'.
					//時間
					'<td style="height:50px;" rowspan="'.$count_rows.'">'.$row["time"].'</td>';
					
					//從菜ID取得菜名稱<使用>
					$query = "SELECT * FROM dish where ID='$dishID'";
					$re = $link->query($query);
					foreach ($re as $r){
						$dish_Name=$r['dishName'];
					}
					//菜名
					echo '<td style="height:50px;" rowspan="'.$count_rows.'">'.$dish_Name.'</td>';

					//取得菜ID使用的食材ID
					$query = "SELECT * FROM recipe where dishID='$dishID'";
					$re = $link->query($query);
					foreach ($re as $r){
						$dish_iID=$r['iID'];

						//從食材ID取得食材類別ID和食材名稱<使用>
						$query = "SELECT * FROM ingredients where iID='$dish_iID'";
						$re = $link->query($query);
						foreach ($re as $r){
							$iID_NID=$r['NID'];
							$iID_Name=$r['name'];

							//從食材類別ID取得食材類別名稱<使用>
							$query = "SELECT * FROM nutrient where NID='$iID_NID'";
							$re = $link->query($query);
							foreach ($re as $r){
								$iID_NID_Name=$r['category'];

									//食材
									echo '<td style="height:50px;">'.$iID_NID_Name.'</td>'.
									//分類
									'<td style="height:50px;">'.$iID_Name.'</td>'.
									//份量
									'<td style="height:50px;">'.$row["portion"].'</td></tr>';									
								}							
							}						
						}
					}
					echo '</tbody>';
			
			?>
		</table>
	</div>
 
</body>
<script>
    $(function(){
        var _h = $(document).height();//取得網頁高度
        parent.postMessage({ h: _h}, '*');//將高度值，傳到父層
    });
</script>
</html>
