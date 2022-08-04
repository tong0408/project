<?php
    session_start();
	include ("configure.php");

    unset($_SESSION['userID']);

    echo "<script>alert('登出成功！')</script>";
    echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>"; //登出後要跳轉的畫面
?>