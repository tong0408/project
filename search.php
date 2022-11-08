<!DOCTYPE html>
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
	//這個要等整合才會有效
	$userid= $_SESSION['userID'];
	date_default_timezone_set('Asia/Taipei');
?>
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
	body,h1,h2,h3,h4,h5,h6 {font-family: "微軟正黑體", sans-serif}
	
	body, html {
		height: 100%;
		line-height: 1.8;
	}
	input[type=text]{margin:10px 0px 10px 0px; width:50%;}
	button{margin:0px 10px 0px 10px;}
	.form1{
		background-color: #FFD79B;
		border-radius:30px;
		width:70%;
		margin:auto;
		padding:30px 0px;
		text-align:center;
	}
	
	.btn{
		border-radius:10px;
		background-color: #FFF;
	}
	
	.btn:hover{
		background-color: #FFB03B;
	}
	td{height:50px;}
</style>
</head>
	
</script>

<body>
    <div class="form1">
		<form method="POST" action="enter_diet.php">
			<table style="margin:auto; width:80%;">
				<tr><td>日期：</td><td><input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required></td><td>時間：</td><td><input type="time" name="time" value="<?PHP echo date ("H:i" )  ?>" required></td></tr>
				<tr><td colspan="4" style="text-align:left;"><button type="button" class="btn" id="add" onclick="location.href='new_recipe.php';">新增食譜</button></td></tr>
				<tr><td colspan="4" style="text-align:left;">若以下沒有，請自行新增食譜</td></tr>
				<tr><td colspan="4">
					<table style="margin:auto; width:80%; ">
						<tr><td>料理</td><td>份量</td></tr>
						<?PHP
							//放使用者已經選擇的蔡id
							$did=array();
							$z=0;
							
							
							//-----------------------------------------------------
							//新增菜單t_user_add的暫存資料表，搜尋有沒有東西
							$query = "SELECT * FROM t_user_add where `UID`='$userid'";
							$result = $link->query($query);
							
							$dishname=array();
							$d=0;
							// 如果有新增菜單，搜尋時顯示他新增的且先勾選
							foreach($result as $row){
								$dishname[$d]=$row["dishName"];
								$d=$d+1;
							}

							for($i=0;$i<count($dishname);$i++){
								$query = "SELECT * FROM dish where `dishName`='$dishname[$i]'";
								$result = $link->query($query);

								foreach($result as $row){
									
									echo "<tr>";
									echo '<td style="text-align:left;"><input type="checkbox" name="dish[]" style="margin-right:20px" value="'.$row["dishName"].'" id="'.$row["ID"].'" checked="checked"><a href="dish.php?id='.$row["ID"].'">' . $row["dishName"] . '</a></td></td><td><input type="number" step="0.5" min="0.5" max="1000.0" name="new_portion[]" ></td>';									
									echo "</tr>";
									$did[$z]=$row["ID"];
									$z++;
								}
							}

							//修改菜單t_user_histroy_modify的暫存資料表，搜尋有沒有東西
							$query = "SELECT dishID FROM t_user_histroy_modify where `UID`='$userid'";
							$result = $link->query($query);

							$a=0;
							$sqldishID=array();

							// 如果t_user_histroy_modify有資料，搜尋時顯示他新增的且先勾選
							foreach($result as $row){
								$sqldishID[$a]=$row['dishID'];
								$a=$a+1;
							}
							$b=0;
							//ECHO count($sqldishID);
							for($i=0;$i<count($sqldishID);$i++){
								if($i<count($sqldishID)-1){
									if($sqldishID[$i]==$sqldishID[$i+1]){
										if($i!=0){
											
											if($sqldishID[$i]!=$sqldishID[$i-1]){
												ECHO $sqldishID[$i];
												$query = "SELECT * FROM dish where `ID`=$sqldishID[$i]";
												$result = $link->query($query);
												foreach($result as $row){
													echo "<tr>";
													echo '<td style="text-align:left;"><input type="checkbox" name="dish[]" style="margin-right:20px" value="'.$row["dishName"].'" id="'.$row["ID"].'" checked="checked"><a href="dish.php?id='.$row["ID"].'">' . $row["dishName"] . '</a></td></td><td><input type="number" step="0.5" min="0.5" max="1000.0" name="new_portion[]" ></td>';									
													echo "</tr>";
													$did[$z]=$row["ID"];
													$z++;
												}
												$b=1;
											}
										}
										
									}else {
										
										
										if($b==0){
											ECHO $sqldishID[$i];
												$query = "SELECT * FROM dish where `ID`=$sqldishID[$i]";
												$result = $link->query($query);
												foreach($result as $row){
													echo "<tr>";
													echo '<td style="text-align:left;"><input type="checkbox" name="dish[]" style="margin-right:20px" value="'.$row["dishName"].'" id="'.$row["ID"].'" checked="checked"><a href="dish.php?id='.$row["ID"].'">' . $row["dishName"] . '</a></td></td><td><input type="number" step="0.5" min="0.5" max="1000.0" name="new_portion[]" ></td>';									
													echo "</tr>";
													$did[$z]=$row["ID"];
													$z++;
												}
												$b=1;
											
										}
									}
								}else if($b==0){
									$query = "SELECT * FROM dish where `ID`=$sqldishID[$i]";
									$result = $link->query($query);
									foreach($result as $row){
										echo "<tr>";
										echo '<td style="text-align:left;"><input type="checkbox" name="dish[]" style="margin-right:20px" value="'.$row["dishName"].'" id="'.$row["ID"].'" checked="checked"><a href="dish.php?id='.$row["ID"].'">' . $row["dishName"] . '</a></td></td><td><input type="number" step="0.5" min="0.5" max="1000.0" name="new_portion[]" ></td>';									
										echo "</tr>";
										$did[$z]=$row["ID"];
										$z++;
									}
								}
							}
							
							//抓資料筆數
							
							$query = "SELECT * FROM dish";
							$result = $link->query($query);
							
							//判斷使用者是否有輸入關鍵字
							if (isset($_GET['s'])) {
								$s = $_GET['s'];
								$n=0;
								foreach ($result as $row) {
									$checkdishname=$row["dishName"];
								//透過strpos函數判斷是否包含使用者輸入
									if (strpos($row["dishName"], $s) !== false) {
										echo "<tr>";
										echo '<td style="text-align:left;"><input type="checkbox" id="'.$row["ID"].'" name="dish[]" style="margin-right:20px" value="' . $row['dishName'] . '"><a href="dish.php?id='.$row["ID"].'">' . $row['dishName'] . '</a></td></td><td><input type="number" step="0.5" min="0.5" max="1000.0" name="new_portion[]" ></td>';
										echo "</tr>";
										$n++;
									}
									//if ($n == 10) break;
								}
								$_SESSION['n']=$n;
							} else {
								$n=0;
								
								//$checkboxid=$row["dishName"];
								//透過$i++ 強制迴圈十次
								foreach ($result as $row) {
									
									$y=0;
									//echo count($did);
									for($m=0;$m<count($did);$m++){
										if($did[$m]==$row["ID"]){
											$y=1;
											break;
										}
									}
									$n++;
									if($y==0){
										
										echo "<tr>";
										echo '<td style="text-align:left;"><input type="checkbox" id="'.$row["ID"].'" name="dish[]" style="margin-right:20px" value="' . $row['dishName'] . '"><a href="dish.php?id='.$row["ID"].'">' . $row['dishName'] . '</a></td></td><td><input type="number" step="0.5" min="0.5" max="1000.0" name="new_portion[]"></td>';
										echo "</tr>";
									}
									if ($n == 10) break;
								}
								$_SESSION['n']=$n;
							}
						?>
					</table>
				</td></tr>				
			</table>
			<input type="submit" class="btn" id="newbutton" value="新增" style="margin:10px 10px 10px 10px; width:70%;">
		</form>	
	</div>

</body>
<script>
	$("#newbutton").click(function(){
			var check=$("input[name='dish[]']:checked").length;//判斷有多少個方框被勾選
			if(check==0){
				alert("您尚未勾選任何項目");
				return false;//不要提交表單
			}else{				
				return true;//提交表單
			}
		})
	</script>
</html>
