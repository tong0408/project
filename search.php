<!DOCTYPE html>
<?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	//這個要等整合才會有效
	//$userid= $_SESSION['userID'];
?>
<html>
<head>
<title>疾時養身</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="image/logo.png" rel="icon" type="image/x-icon" />
<link rel="stylesheet" href="css/bootstrap-3.3.7.css" type="text/css">
<link rel="stylesheet" href="css/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

<body>

    <div class="form1">
		<form method="POST" action="enter_diet.php">
			<table style="margin:auto; width:80%;">
				<tr><td>日期：</td><td><input type="date" name="date" required></td><td>時間：</td><td><input type="time" name="time" required></td></tr>
				<tr><td colspan="4" style="text-align:left;"><button type="button" class="btn" onclick="location.href='recipe.html'">新增食譜</button></td></tr>
				<tr><td colspan="4" style="text-align:left;">以下沒有，請自行新增食譜</td></tr>
				<tr><td colspan="4">
					<table style="margin:auto; width:80%; ">
						<tr><td>料理</td><td>份量</td></tr>
						<?PHP
							$diD = isset($_SESSION['dID']) ? $_SESSION['dID'] : null;
							if ($diD!= null) { // 如果user有新增食物
								$dishID=$_SESSION['dID'];
								$query = "SELECT * FROM dish WHERE ID ='$dishID'";
								$result = $link->query($query);
							
								// 搜尋有資料時顯示搜尋結果
								foreach($result as $row){
									echo "<tr>";
									echo '<td style="text-align:left;"><input type="checkbox" name="dish[]" style="margin-right:20px" value="'.$row['dishName'].'" checked="checked">' . $row['dishName'] . '</td></td><td><input type="number" step="0.1" min="0.1" max="1000.0" name="new_portion[]"></td>';									
									echo "</tr>";
								}
							}
							//抓取全部dish
							$query = "SELECT * FROM dish";
							$result = $link->query($query);
							
							//判斷使用者是否有輸入
							if (isset($_GET['s'])) {
								$s = $_GET['s'];
								foreach ($result as $row) {
								//透過strpos函數判斷是否包含使用者輸入
									if (strpos($row["dishName"], $s) !== false) {
										echo "<tr>";
										echo '<td style="text-align:left;"><input type="checkbox" id="checkbox' . $row['ID'] . '" name="dish[]" style="margin-right:20px" value="' . $row['dishName'] . '">' . $row['dishName'] . '</td></td><td><input type="number" step="0.1" min="0.1" max="1000.0" name="new_portion[]"></td>';
										echo "</tr>";
									}
								}
							} else {
								$i = 0;
								//透過$i++ 強制迴圈十次
								foreach ($result as $row) {
									$i++;
									echo "<tr>";
									echo '<td style="text-align:left;"><input type="checkbox" id="checkbox' . $row['ID'] . '" name="dish[]" style="margin-right:20px" value="' . $row['dishName'] . '">' . $row['dishName'] . '</td></td><td><input type="number" step="0.1" min="0.1" max="1000.0" name="new_portion[]"></td>';
									echo "</tr>";
									if ($i == 10) break;
								}
							}
						?>
					</table>
				</td></tr>				
			</table>
			<input type="submit" class="btn" value="新增" style="margin:10px 10px 10px 10px; width:70%;">
		</form>	
	</div>
 <script>

</script>
</body>
</html>
