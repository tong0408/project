<?php
	session_start();
	include("configure.php");
?>

<!doctype html>
<html lang="en">
	<head>
		<title>登入</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="image/logo.png" rel="icon" type="image/x-icon" />
		<link rel="stylesheet" href="css/w3.css">
		<link rel="stylesheet" href="css/bootstrap-3.3.7.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<style>
			body,h1,h2,h3,h4,h5,h6 {font-family: "微軟正黑體", sans-serif}
			
			body, html {
				height: 100%;
				line-height: 1.8;
				text-align: center;
				font-size:16px;
			}
			
			input{margin:10px 0px 10px 0px;}
			button{margin:10px 10px 10px 10px;}
			img{margin:10px 0px 10px 0px;}
			.form1{
				background-color: #FFD79B;
				border-radius:30px;
				width:40%;
				margin:auto;
				padding:30px 0px 0px;
			}
			.btn{
				border-radius:10px;
				background-color: #FFF;
			}
			
			.btn:hover{
				background-color: #FFB03B;
			}
			.pw { 
			-webkit-text-security: disc; 
			}
		</style>
	</head>	
	<body>
		<a href="index.php"><img src="image/logo.png" height='80px'/></a>
		<div class="form1">
			<form method="POST" action="user_check.php">
				帳號：<input type="text" name="login_userid" maxlength="10" style="width:40%">
				<br>
				密碼：<input type="text" name="login_userpwd" class="pw" style="width:40%">
				<br>
				<button type="submit" class="btn">登入</button>
				<button type="button" class="btn" onclick="location.href='user_create.php'">沒有帳號嗎？註冊</button>
				<br><br>
			</form>
		</div>
	</body>
</html>
