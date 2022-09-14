  <!--輸入每日飲食 -->
  <?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	//新增資料庫沒有的飲食
	$new_dish = isset($_POST["new_dish"]) ? $_POST["new_dish"] : null; //新增菜名
	$new_category = isset($_POST["new_category"]) ? $_POST["new_category"] : null; //新增六大類
	$new_ingredients = isset($_POST["new_ingredients"]) ? $_POST["new_ingredients"] : null; //新增食材
	$new_portion = isset($_POST["new_portion"]) ? $_POST["new_portion"] : null; //新增份量
	
	$userid= $_SESSION['userID'];
	
	//新增至dish
	$query = "INSERT INTO `dish`(`dishName`, `method`) 
	VALUES('$new_dish','')";
	$count = $link->exec($query);
	
	//新增至user_add
	$query = "INSERT INTO `user_add`(`UID`, `dishName`) 
	VALUES('$userid','$new_dish')";
	$count = $link->exec($query);
	
	//先至dish搜尋ID
	$query = "SELECT ID FROM `dish` WHERE `dishName`='$new_dish'";
	$result = $link->query($query);
	
	foreach($result as $row){
		$dishID=$row["ID"];
		$_SESSION['dID']=$dishID;
	}
	
	//改六大類文字變ID
	for($i=0;$i<count($new_ingredients);$i++){
		
		if($new_category[$i]=="全榖雜糧類"){
			$category=1;
		}else if($new_category[$i]=="豆魚蛋肉類"){
			$category=2;
		}else if($new_category[$i]=="乳品類"){
			$category=3;
		}else if($new_category[$i]=="蔬菜類"){
			$category=4;
		}else if($new_category[$i]=="水果類"){
			$category=5;
		}else if($new_category[$i]=="油脂與堅果種子類"){
			$category=6;
		}else{
			$category=7;
		}
		
		//先至ingredients搜尋name是否存在
		$query = "SELECT iID,count(name) FROM `ingredients` WHERE `name`='$new_ingredients[$i]'";
		$result = $link->query($query);
		$count = $result->fetchColumn();

		if($count==""){
			
			//新增至ingredients
			$query = "INSERT INTO `ingredients`(`name`, `NID`) 
			VALUES ('$new_ingredients[$i]',$category)";
			$count = $link->exec($query);
			
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
		}else{
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
		
	}
	header("Location: enter_diet_platform.php");
  ?>