<?php
	session_start();
?>

<!doctype html>
<html lang="en">
	<head>
		<title>建立帳號</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="image/logo.png" rel="icon" type="image/x-icon" />
		<link rel="stylesheet" href="css/w3.css">
		<link rel="stylesheet" href="css/bootstrap-3.3.7.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<script src="https://code.jquery.com/jquery-3.5.0.js"></script>
		<style>
			body,h1,h2,h3,h4,h5,h6 {font-family: "微軟正黑體", sans-serif}
			
			body, html {
				height: 100%;
				line-height: 1.8;
				text-align: center;
				font-size:16px;
				padding-top:5px;
			}
			
			input[type=text]{margin:10px 0px 10px 0px; width:100%;}
			input[type=number]{margin:10px 0px 10px 0px; width:100%;}
			input[type=date]{margin:10px 0px 10px 0px; width:100%;}
			select{margin:10px 0px 10px 0px; width:100%; height:35px;}
			button{margin:0px 10px 0px 10px;}
			img{margin:10px 0px 10px 0px;}
			
			.form1{
				background-color: #FFD79B;
				border-radius:30px;
				width:50%;
				margin:auto;
				padding:30px 0px;
			}
			
			.btn{
				border-radius:10px;
				background-color: #FFF;
			}
			
			.btn:hover{
				background-color: #FFB03B;
			}
		</style>
	</head>	
	
	<body>
		<a href="index.php"><img src="image/logo.png" height='80px'/></a>
		<div class="form1">
			<form method="POST" action="user_create_check.php">
				<table style="margin:auto;">
				<tr><td>帳號：</td><td><input type="text" name="new_userid" maxlength="10" required></td></tr>
				<tr><td>姓名：</td><td><input type="text" name="new_name" required></td></tr>
				<tr><td>性別：<!--下拉式選單-->
				<td><select name="new_gender" required>
					<option value="1">生理男</option>
					<option value="2">生理女</option><br>
				</select></td></tr>
				<tr><td>生日：</td><td><input type="date" name="new_BD" required></td></tr>
				<tr><td>身高（公分cm）：</td><td><input type="text" name="new_height"></td></tr>
				<tr><td>體重（公斤kg）：</td><td><input type="text" name="new_weight"></td></tr>
				<tr><td>活動強度：</td><!--下拉式選單-->
				<td><select name="new_sport" required>
					<option value="1">輕度活動</option>
					<option value="2">中度活動</option>
					<option value="3">重度活動</option>
				</select></td></tr>
				<tr><td>疾病：</td>
				<td style="text-align:left; height:250px;">
					<input type="checkbox" value="0" name="new_disease[]">無<br>
					<input type="checkbox" value="1" name="new_disease[]">肺炎<br>
					<input type="checkbox" value="2" name="new_disease[]">糖尿病<br>
					<input type="checkbox" value="3" name="new_disease[]">高血壓<br>
					<input type="checkbox" value="4" name="new_disease[]">慢性下呼吸道疾病<br>
					<input type="checkbox" value="5" name="new_disease[]">慢性腎臟疾病<br>
					<input type="checkbox" value="6" name="new_disease[]">肝硬化				
				</td>
				</tr>
				</table>
				<input type="submit" class="btn" value="註冊" id="button" style="margin:10px 10px 10px 10px;">
				<button type="button" class="btn" onclick="location.href='user_login.php'">取消</button>
			</form>	
		</div>
	</body>
	<script>
	$("#button").click(function(){
			var check=$("input[name='new_disease[]']:checked").length;//判斷有多少個方框被勾選
			if(check==0){
				alert("您尚未勾選任何項目");
				return false;//不要提交表單
			}else{				
				return true;//提交表單
			}
		})
	</script>
</html>