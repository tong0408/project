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
		$pwd = $row["password"];
		$name = $row["name"];
	}
	
	#password 比較
	//密碼輸入一樣
	if (password_verify($old_pwd,$pwd)) {
		//新舊密碼一樣，重新輸入
		if($old_pwd==$new_pwd){
			echo "<script>alert('新舊密碼一樣，請重新輸入！')</script>";
			echo "<meta http-equiv=REFRESH CONTENT=0;url='user_pwd.php'>";
		}else if($new_pwd==$userid){
			echo "<script>alert('帳號密碼一樣，請重新輸入！')</script>";
			echo "<meta http-equiv=REFRESH CONTENT=0;url='user_pwd.php'>";
		}else{
			//輸入的新密碼寫進資料庫
			if($new_pwd==$check_pwd){
				$pwd_hash = password_hash($new_pwd, PASSWORD_DEFAULT);
				$query = "UPDATE `user` SET `password`='$pwd_hash' WHERE `userID`='$userid'";//Q2加密
				$count=$link->exec($query); 
				echo "<script>alert('" . $name . "，修改成功～！')</script>";
				echo "<meta http-equiv=REFRESH CONTENT=0;url='index.php'>";
			}else{
				echo "<script>alert('密碼兩次輸入不一致，請重新輸入！')</script>";
				echo "<meta http-equiv=REFRESH CONTENT=0;url='user_pwd.php'>";
			}
		}
	}
	#舊密碼錯誤
	else{
		echo "<script>alert('舊密碼輸入錯誤，請重新輸入！')</script>";
		echo "<meta http-equiv=REFRESH CONTENT=0;url='user_pwd.php'>";
	}

?>
