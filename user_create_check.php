  <!--註冊-->
  <?php

	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	
	$new_userid = isset($_POST["new_userid"]) ? $_POST["new_userid"] : null;
	$new_name = isset($_POST["new_name"]) ? $_POST["new_name"] : null;
	$new_gender = isset($_POST["new_gender"]) ? $_POST["new_gender"] : null;
	$new_BD = isset($_POST["new_BD"]) ? $_POST["new_BD"] : null;
	$new_height = isset($_POST["new_height"]) ? $_POST["new_height"] : null;
	$new_weight = isset($_POST["new_weight"]) ? $_POST["new_weight"] : null;
	$new_sport = isset($_POST["new_sport"]) ? $_POST["new_sport"] : null;
	$new_disease = isset($_POST["new_disease"]) ? $_POST["new_disease"] : null;
	$tmp_disease=array();

	$query = "SELECT userid FROM `user` ";
	$result = $link->query($query);
	$n=0;
	
	foreach ($result as $row) {

		$data_userid = $row["userid"]; //資料庫內的userid
		
		if ($new_userid != null) {
			#如果身分證已被註冊
			if ($new_userid == $data_userid) {
				$n=1;
				break;
			}
		}
	}
	if($n==0){
		#當身分證沒有被註冊過
		#當有資訊為空值
		if ($new_userid == null || $new_name == null || $new_gender == null || $new_BD == null || $new_height == null || $new_weight == null || $new_sport == null || $new_gender == null || count($new_disease,0) == 0) {
			//echo "<script>alert('請填寫完整資訊！')</script>";
			//echo "<meta http-equiv=REFRESH CONTENT=0;url='user_create.php'>";
			echo $new_BD;
      
		}
		else{
		#註冊至資料庫內
			switch ($new_gender) {
			case 1:
				$tmp_gender ="生理男";
				break;
			case 2:
				$tmp_gender ="生理女";
				break;
			}
			
			switch ($new_sport) {
			case 1:
				$tmp_sport ="輕度活動";
				break;
			case 2:
				$tmp_sport ="中度活動";
				break;
			case 3:
				$tmp_sport ="重度活動";
				break;
			}
			for($i=0;$i<count($new_disease,0);$i++){
				
				switch ($new_disease[$i]) {
				case 0:
					$tmp_disease[$i] ="無";
				break;
        case 1:
					$tmp_disease[$i] ="肺炎";
				break;
				case 2:
					$tmp_disease[$i] ="糖尿病";
				break;
				case 3:
					$tmp_disease[$i] ="高血壓";
				break;
				case 4:
					$tmp_disease[$i] ="慢性下呼吸道疾病";
				break;
				case 5:
					$tmp_disease[$i] ="慢性腎臟疾病";
				break;
				case 6:
					$tmp_disease[$i] ="肝硬化";
				break;
				}
			}
			
			$new_BMI = $new_weight / (($new_height/100)*($new_height/100));
			function birthday($birthday){

				list($year,$month,$day) = explode("-",$birthday);
				$new_age = date("Y") - $year;
				$month_diff = date("m") - $month;
				$day_diff  = date("d") - $day;
				
				if ($day_diff < 0 || $month_diff < 0){
					$new_age--;
				}
				return $new_age;
			}
			$new_age=birthday($new_BD);
			
			if(count($new_disease,0)==1){
				$query = "INSERT INTO `user`(`userid`,`name`,`gender`,`date`,`age`,`height`,`weight`,`BMI`,`sport`,`disease`,`disease2`,`disease3`,`disease4`,`disease5`,`disease6`,`disease7`)
				VALUES('$new_userid','$new_name','$tmp_gender','$new_BD',$new_age,$new_height,$new_weight,$new_BMI,'$tmp_sport','$tmp_disease[0]','0','0','0','0','0','0')";
				$count = $link->exec($query);
			}else if(count($new_disease,0)==2){
				$query = "INSERT INTO `user`(`userid`,`name`,`gender`,`date`,`age`,`height`,`weight`,`BMI`,`sport`,`disease`,`disease2`,`disease3`,`disease4`,`disease5`,`disease6`,`disease7`)
				VALUES('$new_userid','$new_name','$tmp_gender','$new_BD',$new_age,$new_height,$new_weight,$new_BMI,'$tmp_sport','$tmp_disease[0]','$tmp_disease[1]','0','0','0','0','0')";
				$count = $link->exec($query);
			}else if(count($new_disease,0)==3){
				$query = "INSERT INTO `user`(`userid`,`name`,`gender`,`date`,`age`,`height`,`weight`,`BMI`,`sport`,`disease`,`disease2`,`disease3`,`disease4`,`disease5`,`disease6`,`disease7`)
				VALUES('$new_userid','$new_name','$tmp_gender','$new_BD',$new_age,$new_height,$new_weight,$new_BMI,'$tmp_sport','$tmp_disease[0]','$tmp_disease[1]','$tmp_disease[2]','0','0','0','0')";
				$count = $link->exec($query);
			}else if(count($new_disease,0)==4){
				$query = "INSERT INTO `user`(`userid`,`name`,`gender`,`date`,`age`,`height`,`weight`,`BMI`,`sport`,`disease`,`disease2`,`disease3`,`disease4`,`disease5`,`disease6`,`disease7`)
				VALUES('$new_userid','$new_name','$tmp_gender','$new_BD',$new_age,$new_height,$new_weight,$new_BMI,'$tmp_sport','$tmp_disease[0]','$tmp_disease[1]','$tmp_disease[2]','$tmp_disease[3]','0','0','0')";
				$count = $link->exec($query);
			}else if(count($new_disease,0)==5){
				$query = "INSERT INTO `user`(`userid`,`name`,`gender`,`date`,`age`,`height`,`weight`,`BMI`,`sport`,`disease`,`disease2`,`disease3`,`disease4`,`disease5`,`disease6`,`disease7`)
				VALUES('$new_userid','$new_name','$tmp_gender','$new_BD',$new_age,$new_height,$new_weight,$new_BMI,'$tmp_sport','$tmp_disease[0]','$tmp_disease[1]','$tmp_disease[2]','$tmp_disease[3]','$tmp_disease[4]','0','0')";
				$count = $link->exec($query);
			}else if(count($new_disease,0)==6){
				$query = "INSERT INTO `user`(`userid`,`name`,`gender`,`date`,`age`,`height`,`weight`,`BMI`,`sport`,`disease`,`disease2`,`disease3`,`disease4`,`disease5`,`disease6`,`disease7`)
				VALUES('$new_userid','$new_name','$tmp_gender','$new_BD',$new_age,$new_height,$new_weight,$new_BMI,'$tmp_sport','$tmp_disease[0]','$tmp_disease[1]','$tmp_disease[2]','$tmp_disease[3]','$tmp_disease[4]','$tmp_disease[5]','0')";
				$count = $link->exec($query);
			}else{
				$query = "INSERT INTO `user`(`userid`,`name`,`gender`,`date`,`age`,`height`,`weight`,`BMI`,`sport`,`disease`,`disease2`,`disease3`,`disease4`,`disease5`,`disease6`,`disease7`)
				VALUES('$new_userid','$new_name','$tmp_gender','$new_BD',$new_age,$new_height,$new_weight,$new_BMI,'$tmp_sport','$tmp_disease[0]','$tmp_disease[1]','$tmp_disease[2]','$tmp_disease[3]','$tmp_disease[4]','$tmp_disease[5]','$tmp_disease[6]')";
				$count = $link->exec($query);
			}
			
			echo "<script>alert('註冊成功！請重新登入！')</script>";
			echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
		}
	}else{
		echo "<script>alert('此帳號已被註冊！')</script>";
		echo "<meta http-equiv=REFRESH CONTENT=0;url='user_create.php'>";
	}
			
  ?>