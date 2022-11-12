  <!--刪除歷史紀錄 -->
  <?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	$hisid = isset($_GET["ID"]) ? $_GET["ID"] : null; //在歷史表裡面的ID
  $t_hisid = isset($_GET["ID1"]) ? $_GET["ID1"] : null; //在歷史表裡面的ID
  $t_time = isset($_GET["time"]) ? $_GET["time"] : null;
  $userid= $_SESSION['userID'];//userid
  date_default_timezone_set('Asia/Taipei');
    

    //用歷史紀錄ID去找歷史紀錄的蔡，然後去看他有多少個
    if($hisid!=null){
        //將符合ID的資料刪除
  	    $query = "DELETE FROM `history` where `UID`='$userid' and`ID`='$hisid';";
        //刪除資料
        $count = $link->exec($query); 
    }
    elseif($t_hisid!=null){
        echo $t_date= $_SESSION['t_date'];  
        echo  $_GET["time"];
        //將符合ID的資料刪除
  	    $query = "DELETE FROM `user_histroy_modify` where `UID`='$userid' and `dishID`='$t_hisid' and `date`='$t_date' and `time`='$t_time';";
        //刪除資料
        $count = $link->exec($query); 
    }    
	

	//header("Location: record.php");
  ?>