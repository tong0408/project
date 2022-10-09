<!DOCTYPE html>
<?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
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
							
							$query = "SELECT * FROM t_user_add where `UID`='$userid'";
							$result = $link->query($query);
							
							// 搜尋有資料時顯示搜尋結果
							foreach($result as $row){
								echo "<tr>";
								echo '<td style="text-align:left;"><input type="checkbox" name="dish[]" style="margin-right:20px" value="'.$row["dishName"].'" id="'.$row["dishName"].'" checked="checked">' . $row["dishName"] . '</td></td><td><input type="number" step="0.1" min="0.1" max="1000.0" name="new_portion[]"></td>';									
								echo "</tr>";
							}
							
							//抓取全部dish
							$query = "SELECT * FROM dish";
							$result = $link->query($query);
							
							//判斷使用者是否有輸入
							if (isset($_GET['s'])) {
								$s = $_GET['s'];
								$n=0;
								foreach ($result as $row) {
									$checkdishname=$row["dishName"];
								//透過strpos函數判斷是否包含使用者輸入
									if (strpos($row["dishName"], $s) !== false) {
										echo "<tr>";
										echo '<td style="text-align:left;"><input type="checkbox" id="'.$row["dishName"].'" name="dish[]" style="margin-right:20px" value="' . $row['dishName'] . '">' . $row['dishName'] . '</td></td><td><input type="number" step="0.1" min="0.1" max="1000.0" name="new_portion[]"></td>';
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
									$n++;
									echo "<tr>";
									echo '<td style="text-align:left;"><input type="checkbox" id="'.$row["dishName"].'" name="dish[]" style="margin-right:20px" value="' . $row['dishName'] . '">' . $row['dishName'] . '</td></td><td><input type="number" step="0.1" min="0.1" max="1000.0" name="new_portion[]"></td>';
									echo "</tr>";
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