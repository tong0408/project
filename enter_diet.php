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
	
	$userid= $_SESSION['userID'];
	$n = isset($_SESSION['n']) ? $_SESSION['n'] : null;
	FOR($i=0;$i<count($dishname);$i++){
		
		//搜尋相對應的dishID
		$query = "SELECT `ID` FROM `dish` WHERE `dishName`='$dishname[$i]'";
		$result = $link->query($query);
		
		foreach($result as $row){
			$dishID=$row["ID"];
			for($m=0;$m<$n;$m++){
				if($new_portion[$m]!=null){
					//新增至使用者history
					$query = "INSERT INTO `history`(`UID`, `date`, `time`, `dishID`, `portion`)
					VALUES('$userid','$new_date','$new_time',$dishID,$new_portion[$m])";
					$count = $link->exec($query);
					break;
				}else{
					continue;
				}
			}
			
		}
	}
	
	header("Location: record.php");
		
  ?>