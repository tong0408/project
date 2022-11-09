  <!--刪除歷史紀錄 -->
  <?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	$hisid = isset($_GET["id"]) ? $_GET["id"] : null; //在歷史表裡面的ID
    $userid= $_SESSION['userID'];//userid
	$dishname=$_SESSION['dish_Name']; //要找的菜名

    //用歷史紀錄ID去找歷史紀錄的蔡，然後去看他有多少個
    echo $hisid;

    
	/*for($i=0;$i<count($ingredients)-1;$i++){
        //ECHO $new_portion[$i].$i;
        //搜尋食材名稱
        $query = "SELECT iID FROM `ingredients` WHERE `name`='$ingredients[$i]'";
		$result = $link->query($query);
        foreach($result as $row){
            $iID=$row["iID"];

            //新增至t_user_histroy_modify
            $query = "INSERT INTO `t_user_histroy_modify`(`UID`, `dishID`, `iID`, `portion`) 
            VALUES('$userid',$dishid,$iID,$new_portion[$i])";
            $count = $link->exec($query);
        }
        
    }*/

	//header("Location: record.php");
  ?>