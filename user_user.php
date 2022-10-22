<?php
    session_start();
	include ("configure.php");

	$disease=array();
    #假如$_SESSION['userID']為空值表示沒有登入
    if($_SESSION['userID']==null){
        echo "<script>alert('請先登入會員！')</script>";
        echo "<meta http-equiv=REFRESH CONTENT=0;url='user_login.php'>";
    }
    else{
        $userid=$_SESSION['userID'];
        $link = new PDO('mysql:host='.$hostname.';dbname='.$database.';charset=utf8', $username, $password);

        $query = "SELECT * FROM `user` WHERE `userID`='$userid'";
        $result = $link->query($query);
    
        #獲取現在登入者的資訊
        foreach ($result as $row){
            $userid=$row["userid"]; //身分證
			$name=$row["name"];
			$BD=$row["date"];
      $age=$row["age"];
      $height=$row["height"];
      $weight=$row["weight"];
      $sport=$row["sport"];
      $disease[0]=$row["disease"];
			$disease[1]=$row["disease2"];
			$disease[2]=$row["disease3"];
			$disease[3]=$row["disease4"];
			$disease[4]=$row["disease5"];
			$disease[5]=$row["disease6"];
			$disease[6]=$row["disease7"];
        }
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
		$new_age=birthday($BD);
		
		if($new_age!=$age){
			#修改age
			$query = "UPDATE `user` SET `age`='$new_age' WHERE `userID`='$userid'";
			$count=$link->exec($query);
		}
    }

?>

<!doctype html>
<html>
    <head>
		<title>個人檔案</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="image/logo.png" rel="icon" type="image/x-icon" />
		<link rel="stylesheet" href="css/w3.css">
		<link rel="stylesheet" href="css/mine.css">
		<link rel="stylesheet" href="css/bootstrap-3.3.7.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
	<style>
		input[type=text]{margin:10px 0px 10px 0px; width:100%;}
		input[type=number]{margin:10px 0px 10px 0px; width:100%;}
		select{margin:10px 0px 10px 0px; width:100%; height:35px;}			
		img{margin:10px 0px 10px 0px;}
		tr{height:60px;}
	</style>
    <body>
	
<?php include("header.php"); ?>
        <!--登出-->
        
	<div class="form1" style="width:50%;">
		<a href="index.php"><button class="btn" style="position: absolute; left: 27%; border-radius:10px;">返回</button></a><br>			
			<form method="POST" action="">
				<table style="margin:auto;">
				<tr><td>會員名稱：</td>
				<td>[ <?php echo $name ;?> ]</td>
				</tr>
				<tr><td>年齡：</td>
				<td><?php echo $new_age;?></td>
				</tr>
				<tr><td>身高（公分cm）：</td>
				<td><input type="text" name="new_height" value="<?php echo $height;?>" ></td>
				</tr>
				<tr><td>體重（公斤kg）：</td>
				<td><input type="text" name="new_weight" value="<?php echo $weight;?>"></td>
				</tr>
				<tr><td>活動強度：</td><!--也是用下拉式選單-->
				<td><select name="new_sport" >
				<option <?php $n="selected='selected'";if($sport=="輕度活動"){echo $n;}?>value="1">輕度活動</option>
				<option <?php $n="selected='selected'";if($sport=="中度活動"){echo $n;}?>value="2">中度活動</option>
				<option <?php $n="selected='selected'";if($sport=="重度活動"){echo $n;}?>value="3">重度活動</option>
				</select></td>
				</tr>
				<tr><td>疾病：</td>
				<td style="text-align:left; height:200px;">
					<input type="checkbox" value="0" name="new_disease[]" <?php $n="checked='checked'";if($disease[0]=="無"){echo $n;}?>>無<br>
					<input type="checkbox" value="1" name="new_disease[]" <?php $n="checked='checked'";if($disease[0]=="肺炎"){echo $n;}else if($disease[1]=="肺炎"){echo $n;}?>>肺炎<br>
					<input type="checkbox" value="2" name="new_disease[]" <?php $n="checked='checked'";if($disease[0]=="糖尿病"){echo $n;}else if($disease[1]=="糖尿病"){echo $n;}else if($disease[2]=="糖尿病"){echo $n;}?>>糖尿病<br>
					<input type="checkbox" value="3" name="new_disease[]" <?php $n="checked='checked'";if($disease[0]=="高血壓"){echo $n;}else if($disease[1]=="高血壓"){echo $n;}else if($disease[2]=="高血壓"){echo $n;}else if($disease[3]=="高血壓"){echo $n;}?>>高血壓<br>
					<input type="checkbox" value="4" name="new_disease[]" <?php $n="checked='checked'";if($disease[0]=="慢性下呼吸道疾病"){echo $n;}else if($disease[1]=="慢性下呼吸道疾病"){echo $n;}else if($disease[2]=="慢性下呼吸道疾病"){echo $n;}else if($disease[3]=="慢性下呼吸道疾病"){echo $n;}else if($disease[4]=="慢性下呼吸道疾病"){echo $n;}?>>慢性下呼吸道疾病<br>
					<input type="checkbox" value="5" name="new_disease[]" <?php $n="checked='checked'";if($disease[0]=="慢性腎臟疾病"){echo $n;}else if($disease[1]=="慢性腎臟疾病"){echo $n;}else if($disease[2]=="慢性腎臟疾病"){echo $n;}else if($disease[3]=="慢性腎臟疾病"){echo $n;}else if($disease[4]=="慢性腎臟疾病"){echo $n;}else if($disease[5]=="慢性腎臟疾病"){echo $n;}?>>慢性腎臟疾病<br>
					<input type="checkbox" value="6" name="new_disease[]" <?php $n="checked='checked'";if($disease[0]=="肝硬化"){echo $n;}else if($disease[1]=="肝硬化"){echo $n;}else if($disease[2]=="肝硬化"){echo $n;}else if($disease[3]=="肝硬化"){echo $n;}else if($disease[4]=="肝硬化"){echo $n;}else if($disease[5]=="肝硬化"){echo $n;}else if($disease[6]=="肝硬化"){echo $n;}?>>肝硬化				
				</td>
				</tr>
				<tr><td>請輸入帳號做確認：</td>
				<td><input type="text" name="g_userid" required></td>
				</tr>
				<tr><td colspan="2"><button type="submit" class="btn" >修改</button></td>
				</tr>
				</table>				
			</form>
			
		</div>
		<br>
        <br><br><br>

        <!--修改資料的PHP-->
        <?php

            $g_userid=isset($_POST["g_userid"])?$_POST["g_userid"]:null;
			$new_weight=isset($_POST["new_weight"])?$_POST["new_weight"]:null;
			$new_height=isset($_POST["new_height"])?$_POST["new_height"]:null;
			$new_disease=isset($_POST["new_disease"])?$_POST["new_disease"]:null;
			$new_sport=isset($_POST["new_sport"])?$_POST["new_sport"]:null;
			
            #假如驗證的身分證不為空值
            if($g_userid!=null){
                #假如驗證的身分證和目前登入的身分證相同
                if($g_userid==$userid){
					//modify BMI
					$new_BMI = $new_weight / (($new_height/100)*($new_height/100));
					
                    #修改身高,體重
					$query = "UPDATE `user` SET `height`='$new_height',`weight`='$new_weight',`BMI`='$new_BMI' WHERE `userID`='$userid'";
					$count=$link->exec($query);
                    

                    #修改活動強度
                    if($new_sport!=null){
                        switch ($new_sport) {
                            case 1:
                                $query = "UPDATE `user` SET `sport`='輕度活動' WHERE `userID`='$userid'";
                                $count=$link->exec($query); 
                                break;
                            case 2:
                                $query = "UPDATE `user` SET `sport`='中度活動' WHERE `userID`='$userid'";
                                $count=$link->exec($query); 
                                break;
                            case 3:
                                $query = "UPDATE `user` SET `sport`='重度活動' WHERE `userID`='$userid'";
                                $count=$link->exec($query); 
                                break;
                            }
                    }
                    
					#要改！！！！！！！！！！！！！！！！！！！！！！
					#修改疾病
					if(count($new_disease,0)>1 && $new_disease[0]="無"){
						echo "<script>alert('疾病選擇錯誤（選擇無也有選擇疾病），請重新輸入！')</script>";
						echo "<meta http-equiv=REFRESH CONTENT=0;url='user_user.php'>";
		
					}else if(count($new_disease,0)!=0){
						for($i=0;$i<count($new_disease,0);$i++){
							if($i==0){
								switch ($new_disease[$i]) {
								case 0:
									$query = "UPDATE `user` SET `disease`='無',`disease2`='0',`disease3`='0',`disease4`='0',`disease5`='0',`disease6`='0',`disease7`='0' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 1:
									$query = "UPDATE `user` SET `disease`='肺炎',`disease2`='0',`disease3`='0',`disease4`='0',`disease5`='0',`disease6`='0',`disease7`='0' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 2:
									$query = "UPDATE `user` SET `disease`='糖尿病',`disease2`='0',`disease3`='0',`disease4`='0',`disease5`='0',`disease6`='0',`disease7`='0' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 3:
									$query = "UPDATE `user` SET `disease`='高血壓',`disease2`='0',`disease3`='0',`disease4`='0',`disease5`='0',`disease6`='0',`disease7`='0' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 4:
									$query = "UPDATE `user` SET `disease`='慢性下呼吸道疾病',`disease2`='0',`disease3`='0',`disease4`='0',`disease5`='0',`disease6`='0',`disease7`='0' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 5:
									$query = "UPDATE `user` SET `disease`='慢性腎臟疾病',`disease2`='0',`disease3`='0',`disease4`='0',`disease5`='0',`disease6`='0',`disease7`='0' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 6:
									$query = "UPDATE `user` SET `disease`='肝硬化',`disease2`='0',`disease3`='0',`disease4`='0',`disease5`='0',`disease6`='0',`disease7`='0' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								}
							}else if($i==1){
								switch ($new_disease[$i]) {
								case 1:
									$query = "UPDATE `user` SET `disease2`='肺炎' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 2:
									$query = "UPDATE `user` SET `disease2`='糖尿病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 3:
									$query = "UPDATE `user` SET `disease2`='高血壓' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 4:
									$query = "UPDATE `user` SET `disease2`='慢性下呼吸道疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 5:
									$query = "UPDATE `user` SET `disease2`='慢性腎臟疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 6:
									$query = "UPDATE `user` SET `disease2`='肝硬化' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								}
							}else if($i==2){
								switch ($new_disease[$i]) {
								case 1:
									$query = "UPDATE `user` SET `disease3`='肺炎' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 2:
									$query = "UPDATE `user` SET `disease3`='糖尿病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 3:
									$query = "UPDATE `user` SET `disease3`='高血壓' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 4:
									$query = "UPDATE `user` SET `disease3`='慢性下呼吸道疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 5:
									$query = "UPDATE `user` SET `disease3`='慢性腎臟疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 6:
									$query = "UPDATE `user` SET `disease3`='肝硬化' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								}
							}else if($i==3){
								switch ($new_disease[$i]) {
								case 1:
									$query = "UPDATE `user` SET `disease4`='肺炎' WHERE `userID`='$userid'";
									$count=$link->exec($query);         
									break;                              
								case 2:                                 
									$query = "UPDATE `user` SET `disease4`='糖尿病' WHERE `userID`='$userid'";
									$count=$link->exec($query);         
									break;                              
								case 3:                                 
									$query = "UPDATE `user` SET `disease4`='高血壓' WHERE `userID`='$userid'";
									$count=$link->exec($query);        
									break;                             
								case 4:                                
									$query = "UPDATE `user` SET `disease4`='慢性下呼吸道疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query);        
									break;                             
								case 5:                                
									$query = "UPDATE `user` SET `disease4`='慢性腎臟疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query);         
									break;                              
								case 6:                                 
									$query = "UPDATE `user` SET `disease4`='肝硬化' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								}
							}else if($i==4){
								switch ($new_disease[$i]) {
								case 1:
									$query = "UPDATE `user` SET `disease5`='肺炎' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 2:
									$query = "UPDATE `user` SET `disease5`='糖尿病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 3:
									$query = "UPDATE `user` SET `disease5`='高血壓' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 4:
									$query = "UPDATE `user` SET `disease5`='慢性下呼吸道疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 5:
									$query = "UPDATE `user` SET `disease5`='慢性腎臟疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 6:
									$query = "UPDATE `user` SET `disease5`='肝硬化' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								}
							}else if($i==5){
								switch ($new_disease[$i]) {
								case 1:
									$query = "UPDATE `user` SET `disease6`='肺炎' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 2:
									$query = "UPDATE `user` SET `disease6`='糖尿病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 3:
									$query = "UPDATE `user` SET `disease6`='高血壓' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 4:
									$query = "UPDATE `user` SET `disease6`='慢性下呼吸道疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 5:
									$query = "UPDATE `user` SET `disease6`='慢性腎臟疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 6:
									$query = "UPDATE `user` SET `disease6`='肝硬化' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								}
							}else if($i==6){
								switch ($new_disease[$i]) {
								case 1:
									$query = "UPDATE `user` SET `disease7`='肺炎' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 2:
									$query = "UPDATE `user` SET `disease7`='糖尿病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 3:
									$query = "UPDATE `user` SET `disease7`='高血壓' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 4:
									$query = "UPDATE `user` SET `disease7`='慢性下呼吸道疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 5:
									$query = "UPDATE `user` SET `disease7`='慢性腎臟疾病' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								case 6:
									$query = "UPDATE `user` SET `disease7`='肝硬化' WHERE `userID`='$userid'";
									$count=$link->exec($query); 
									break;
								}
							}
						}
						echo "<script>alert('修改成功！')</script>";
                    	echo "<meta http-equiv=REFRESH CONTENT=0;url='user_user.php'>";
                    }
                    
                }else{
                    echo "<script>alert('帳號輸入錯誤，請重新輸入！')</script>";
                    echo "<meta http-equiv=REFRESH CONTENT=0;url='user_user.php'>";
                }
            }
        ?>

    </body>
</html>