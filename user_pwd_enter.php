<!--用來確認帳號密碼後紀錄section用的-->
<?php
	session_start();
	include("configure.php");
	$userid= $_SESSION['userID'];
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	
	#login_帳號密碼 = user輸入的帳號密碼
	$old_pwd = isset($_POST["old_pwd"]) ? $_POST["old_pwd"] : null;
	$new_pwd = isset($_POST["new_pwd"]) ? $_POST["new_pwd"] : null;
	$check_pwd = isset($_POST["check_pwd"]) ? $_POST["check_pwd"] : null;
	
	$query = "SELECT * FROM `user` WHERE `userid`='$userid'";
	$result = $link->query($query);
	
	#資料庫中的資料
	foreach ($result as $row) {
		$ID = $row["ID"];
		$userid = $row["userid"];
		$password = $row["password"];//Q1 解碼
		$name = $row["name"];
	}
	
	#password 比較
	if ($old_pwd == $password) {
		//密碼正確，輸入的新密碼寫進資料庫
		if($new_pwd==$check_pwd){
			$query = "UPDATE `user` SET `password`='$new_pwd' WHERE `userID`='$userid'";//Q2加密
			$count=$link->exec($query); 
			echo "<script>alert('" . $name . "，修改成功～！')</script>";
			echo "<meta http-equiv=REFRESH CONTENT=0;url='index.php'>";
		}else{
			echo "<script>alert('密碼兩次輸入不一致，請重新輸入！')</script>";
			echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
		}
	}
	#舊密碼錯誤
	else{
		echo "<script>alert('舊密碼輸入錯誤，請重新輸入！')</script>";
		echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
	}

?>
