  <!--輸入每日飲食 -->
  <?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	//新增資料庫沒有的飲食
	$new_dish = isset($_POST["new_dish"]) ? $_POST["new_dish"] : null; //新增菜名
	$new_ingredients = isset($_POST["ingredients"]) ? $_POST["ingredients"] : null; //新增食材
	$new_portion = isset($_POST["portion"]) ? $_POST["portion"] : null; //新增份量
	
	$userid= $_SESSION['userID'];
	
	//新增至dish
	$query = "INSERT INTO `dish`(`dishName`, `method`) 
	VALUES('$new_dish','')";
	$count = $link->exec($query);
	
	//新增至t_user_add
	$query = "INSERT INTO `t_user_add`(`UID`, `dishName`) 
	VALUES('$userid','$new_dish')";
	$count = $link->exec($query);
	
	//先至dish搜尋ID
	$query = "SELECT ID FROM `dish` WHERE `dishName`='$new_dish'";
	$result = $link->query($query);
	
	foreach($result as $row){
		$dishID=$row["ID"];
		$_SESSION['dID']=$dishID;
	}
	
	
	for($i=0;$i<count($new_ingredients);$i++){
		
		//先至ingredients搜尋iID
		$query = "SELECT iID FROM `ingredients` WHERE `name`='$new_ingredients[$i]'";
		$result = $link->query($query);
		
		//新增至recipe
		foreach($result as $row){
			
			$iID=$row["iID"];
			$query = "INSERT INTO `recipe`(`iID`, `portion`, `dishID`) 
			VALUES ($iID,$new_portion[$i],$dishID)";
			$count = $link->exec($query);
		}
		
	}

	$sql = "DELETE FROM `t_newrecipe` WHERE `UID`='$userid'";
	// 用mysqli_query方法執行(sql語法)將結果存在變數中
	$count = $link->exec($sql);

	header("Location: enter_diet_platform.php");
  ?>