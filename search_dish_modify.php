  <!--輸入每日飲食 -->
  <?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	//修改分量與所含食材
    $userid= $_SESSION['userID'];//userid
	$dishname=$_SESSION['dish_Name']; //修改的菜名
    $dishid=$_SESSION['dishID'];//修改的菜名id
	$ingredients = isset($_POST["ingredients"]) ? $_POST["ingredients"] : null; //修改的食材名稱
	$new_portion = isset($_POST["new_portion"]) ? $_POST["new_portion"] : null; //修改的份量

	for($i=0;$i<count($ingredients);$i++){
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