<!--輸入每日飲食-->
<?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	
  	//建立存放t_user_histroy_modify的資料的陣列
	$t_dishID=array();
	$t_iID=array();
	$t_portion=array();

	//新增資料庫有的飲食
	$new_date = isset($_POST["date"]) ? $_POST["date"] : null; //日期
	$new_time = isset($_POST["time"]) ? $_POST["time"] : null; //時間
	$dishname = isset($_POST["dish"]) ? $_POST["dish"] : null; //菜名
	$new_portion = isset($_POST["new_portion"]) ? $_POST["new_portion"] : null; //份量

	//判斷分量是否有輸入
	$d=0;
	$e=0;
	$f=0;

	for($f=0;$f<count($new_portion);$f++){
		if($new_portion[$f]!=null){
			$e=$e+1;
		}
	}

	for($b=0;$b<count($dishname);$b++){
		if($e!=count($dishname)){
			if($b==count($dishname)-1){
				$d=1;
			}
		}
	}
	//echo $e.",".count($dishname).",".$d;

	$userid= $_SESSION['userID'];
	$n = isset($_SESSION['n']) ? $_SESSION['n'] : null;

	if($d==0){
  		//搜尋t_user_histroy_modify裡面有沒有東西
		$query = "SELECT count(`ID`) FROM `t_user_histroy_modify` WHERE `UID`='$userid'";
		$res = $link->query($query);
		$cou = $res->fetchColumn();

		$query = "SELECT `dishID`,`iID`,`portion` FROM `t_user_histroy_modify` WHERE `UID`='$userid'";
		$res = $link->query($query);

		$a=0;

		//存t_user_histroy_modify資料
		if($cou!=0){
			foreach($res as $rw){
				$t_dishID[$a]=$rw["dishID"];
				$t_iID[$a]=$rw["iID"];
				$t_portion[$a]=$rw["portion"];
				
				$a=$a+1;
			}
		}
	
		FOR($i=0;$i<count($dishname);$i++){
			//搜尋相對應的dishID
			$query = "SELECT `ID` FROM `dish` WHERE `dishName`='$dishname[$i]'";
			$result = $link->query($query);

			//拿dish的dishID
			foreach($result as $row){
				$dishID=$row["ID"];

				//比對t_user_histroy_modify的資料
				if($cou!=0){
					for($m=0;$m<count($t_dishID);$m++){
						//當勾選的dishID=t_user_histroy_modify的dishID
						if($dishID==$t_dishID[$m]){
							//新增至使用者user_histroy_modify
							$query = "INSERT INTO `user_histroy_modify`(`UID`, `date`, `time`, `dishID`, `iID`, `iportion`, `portion`)
							VALUES('$userid','$new_date','$new_time',$t_dishID[$m],$t_iID[$m],$t_portion[$m],$new_portion[$i])";
							$count = $link->exec($query);
						}
					}
				}else{
					for($m=0;$m<$n;$m++){
						if($new_portion[$m]!=null){
							echo $dishID;
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
		}
		
		//刪除t_user_add
		$sql = "DELETE FROM `t_user_add` WHERE `UID`='$userid'";
		// 用mysqli_query方法執行(sql語法)將結果存在變數中
		$count = $link->exec($sql);

		//刪除t_user_histroy_modify
		$sql = "DELETE FROM `t_user_histroy_modify` WHERE `UID`='$userid'";
		// 用mysqli_query方法執行(sql語法)將結果存在變數中
		$count = $link->exec($sql);
		$d=0;
		header("Location: record.php");
		
	}else{
		echo "<script>alert('勾選料理數量與輸入的份量數量不同喔！')</script>";
	    echo "<meta http-equiv=REFRESH CONTENT=0;url='enter_diet_platform.php'>";
		
	}
	
  ?>