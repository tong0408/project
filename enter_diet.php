  <!--輸入每日飲食-->
  <?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	
	//新增資料庫有的飲食
	$new_date = isset($_POST["date"]) ? $_POST["date"] : null; //日期
	$new_time = isset($_POST["time"]) ? $_POST["time"] : null; //時間
	$dishname = isset($_POST["dish"]) ? $_POST["dish"] : null; //菜名
	$new_portion = isset($_POST["new_portion"]) ? $_POST["new_portion"] : null; //份量
	
	//這個要等整合才能拿掉註解
	$userid= $_SESSION['userID'];

	FOR($i=0;$i<count($dishname);$i++){
		echo $dishname[$i].$new_portion[$i];
		//搜尋相對應的dishID
		$query = "SELECT ID FROM `dish` WHERE `dishName`='$dishname[$i]'";
		$result = $link->query($query);
		
		foreach($result as $row){
			$dishID=$row["ID"];
		}
		//新增至使用者history
		$query = "INSERT INTO `history`(`UID`, `date`, `time`, `dishID`, `portion`)
		VALUES('$userid','$new_date','$new_time',$dishID,$new_portion[$i])";
		$count = $link->exec($query);
	}
	
	
	header("Location: index.html#record");
		
  ?>