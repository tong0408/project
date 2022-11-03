  <!--輸入每日飲食 -->
  <?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	//修改分量與所含食材
    $userid= $_SESSION['userID'];//userid
	$dishname=$_SESSION['dish_Name']; //修改的菜名
    $dishid=$_SESSION['dishID'];//修改的菜id
	$ingredients = isset($_POST["ingredients"]) ? $_POST["ingredients"] : null; //修改的食材名稱
	$new_portion = isset($_POST["new_portion"]) ? $_POST["new_portion"] : null; //修改的份量

    
    //先查看是否改過
    $query = "SELECT count(ID) FROM t_user_histroy_modify WHERE `UID`='$userid' AND `dishID`='$dishid' ";
    $result = $link->query($query);
    $count = $result->fetchColumn();

    //代表有改過
    if($count>0){
        //刪除t_user_histroy_modify該道料理
        $sql = "DELETE FROM `t_user_histroy_modify` WHERE `UID`='$userid' and `dishID`='$dishid'";
        // 用mysqli_query方法執行(sql語法)將結果存在變數中
        $count = $link->exec($sql);
    }
	for($i=0;$i<count($ingredients)-1;$i++){
        
        //ECHO count($ingredients).$ingredients[$i].",".$new_portion[$i];

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
        
    }

	header("Location: enter_diet_platform.php");
  ?>