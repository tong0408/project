<!--用來確認帳號密碼後紀錄section用的-->
<?php
	session_start();
	include("configure.php");
	
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	
	#login_帳號密碼 = user輸入的帳號密碼
	$login_userid = isset($_POST["login_userid"]) ? $_POST["login_userid"] : null;
	$login_pwd = isset($_POST["login_userpwd"]) ? $_POST["login_userpwd"] : null;
	
	$query = "SELECT * FROM `user` WHERE `userid`='$login_userid'";
	$result = $link->query($query);
	
	$ID = "";
	$userid = "";
	
	
	#$_ID帳號 = 資料庫中的資料
	foreach ($result as $row) {
		$ID = $row["ID"];
		$userid = $row["userid"];
		$password = $row["password"];//Q1 解碼
		$name = $row["name"];
	}
	
	#帳號不為空值的狀態下帳號相同
	if (($userid == $login_userid) && ($userid != "")) {
		//密碼正確與否
		if($password==$login_pwd){
			#利用$_SESSION['userID']紀錄現在登入的帳號
			$_SESSION['userID'] = $userid;
			echo "<script>alert('" . $name . "，登入成功～！')</script>";
			echo "<meta http-equiv=REFRESH CONTENT=0;url='index.php'>";
		}else{
			echo "<script>alert('密碼錯誤，請重新輸入！')</script>";
			echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
		}
	}
	#帳號錯誤 （等於帳號不存在）
	else{
		echo "<script>alert('帳號錯誤或不存在，歡迎註冊！')</script>";
		echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
	}

?>
