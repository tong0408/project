  <!--註冊-->
  <?php

	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	
	$new_userid = isset($_POST["new_userid"]) ? $_POST["new_userid"] : null;
	$new_name = isset($_POST["new_name"]) ? $_POST["new_name"] : null;
	$new_gender = isset($_POST["new_gender"]) ? $_POST["new_gender"] : null;
	$new_age = isset($_POST["new_age"]) ? $_POST["new_age"] : null;
	$new_height = isset($_POST["new_height"]) ? $_POST["new_height"] : null;
	$new_weight = isset($_POST["new_weight"]) ? $_POST["new_weight"] : null;
	$new_sport = isset($_POST["new_sport"]) ? $_POST["new_sport"] : null;
	$new_disease = isset($_POST["new_disease"]) ? $_POST["new_disease"] : null;

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
		if ($new_userid == null || $new_name == null || $new_gender == null || $new_age == null || $new_height == null || $new_weight == null || $new_sport == null || $new_gender == null || $new_disease == null) {
			echo "<script>alert('請填寫完整資訊！')</script>";
			echo "<meta http-equiv=REFRESH CONTENT=0;url='user_create.php'>";
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
			
			switch ($new_disease) {
			case 1:
				$tmp_disease ="肺炎";
				break;
			case 2:
				$tmp_disease ="糖尿病";
				break;
			case 3:
				$tmp_disease ="高血壓";
				break;
			case 4:
				$tmp_disease ="慢性下呼吸道疾病";
				break;
			case 5:
				$tmp_disease ="慢性腎臟疾病";
				break;
			case 6:
				$tmp_disease ="肝硬化";
				break;
			}
			
			$new_BMI = $new_weight / (($new_height/100)*($new_height/100));
			
			$query = "INSERT INTO `user`(`userid`,`name`,`gender`,`age`,`height`,`weight`,`BMI`,`sport`,`disease`)
			VALUES('$new_userid','$new_name','$tmp_gender',$new_age,$new_height,$new_weight,$new_BMI,'$tmp_sport','$tmp_disease')";
			$count = $link->exec($query);
			echo "<script>alert('註冊成功！請重新登入！')</script>";
			echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
		}
	}else{
		echo "<script>alert('此身分證字號已被註冊！')</script>";
		echo "<meta http-equiv=REFRESH CONTENT=0;url='user_create.php'>";
	}
			
  ?>