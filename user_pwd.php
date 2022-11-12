<?php
    session_start();
	include ("configure.php");
?>

<!doctype html>
<html>
    <head>
		<title>修改密碼</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="image/icon.png" rel="icon" type="image/x-icon" />
		<link rel="stylesheet" href="css/w3.css">
		<link rel="stylesheet" href="css/mine.css">
		<link rel="stylesheet" href="css/bootstrap-3.3.7.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
	<style>
		input[type=text]{margin:10px 0px 10px 0px; width:100%;}
		input[type=number]{margin:10px 0px 10px 0px; width:100%;}
		select{margin:10px 0px 10px 0px; width:100%; height:35px;}			
		img{margin:10px 0px 10px 0px;}
		tr{height:60px;}
		.pw { 
			-webkit-text-security: disc; 
			}
	</style>
    <body>
	
<?php include("header.php"); ?>
        <!--登出-->
        
	<div class="form1" style="width:50%;">
		<a href="index.php"><button class="btn" style="position: absolute; left: 27%; border-radius:10px;">返回</button></a><br>			
			<form method="POST" action="user_pwd_enter.php">
				<table style="margin:auto;">				
				<tr><td>目前密碼：</td>
				<td><input type="text" class="pw"  name="old_pwd"></td>
				</tr>
				<tr><td>新密碼：</td>
				<td><input type="text" class="pw"  name="new_pwd"></td>
				</tr>
				<tr><td>再次輸入新密碼：</td>
				<td><input type="text"  class="pw" name="check_pwd"></td>
				</tr>
				
				<tr><td colspan="2"><button type="submit" class="btn" >修改</button></td>
				</tr>
				</table>				
			</form>
			
		</div>
		


    </body>
</html>