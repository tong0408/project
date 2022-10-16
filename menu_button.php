<?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
    // 放判斷式
    
    
    
    
    
    
    //放進暫存的資料表，讓menu取得id值 $XX為之後要放進去的dishID
    for($i=0;$i<9;$i++){
        $query = "INSERT INTO `t_menugetid`(`dishID`) VALUES ($XX[i]) ";
        $result = $link->query($query);
    }
    header("Location: menu.php");
?>
