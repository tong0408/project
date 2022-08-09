<?php
    session_start();
	include ("configure.php");

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
            $age=$row["age"];
            $height=$row["height"];
            $weight=$row["weight"];
            $sport=$row["sport"];
            $disease=$row["disease"];
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
        <br><a href="index.html"><button class="btn1 return">返回</button></a><br><br><br><br>
		<div class="form1" style="width:50%;">
			<form method="POST" action="">
				<table style="margin:auto;">
				<tr><td>會員帳號：</td>
				<td>[ <?php echo $userid ;?> ]</td>
				</tr>
				<tr><td>年齡：</td>
				<td><input type="text" name="new_age" value="<?php echo $age;?>" ></td>
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
				<tr><td>疾病： </td><!--疾病這邊是用下拉式選單-->
				<td><select name="new_disease">
				<option <?php $n="selected='selected'";if($disease=="肺炎"){echo $n;}?>value="1">肺炎</option>
				<option <?php $n="selected='selected'";if($disease=="糖尿病"){echo $n;}?>value="2">糖尿病</option>
				<option <?php $n="selected='selected'";if($disease=="高血壓"){echo $n;}?>value="3">高血壓</option>
				<option <?php $n="selected='selected'";if($disease=="慢性下呼吸道疾病"){echo $n;}?>value="4">慢性下呼吸道疾病</option>
				<option <?php $n="selected='selected'";if($disease=="慢性腎臟疾病"){echo $n;}?>value="5">慢性腎臟疾病</option>
				<option <?php $n="selected='selected'";if($disease=="肝硬化"){echo $n;}?>value="6">肝硬化</option>
				</select></td>
				</tr>
				<tr><td>請輸入身分證做確認：</td>
				<td><input type="text" name="g_userid" required></td>
				</tr>
				<tr><td colspan="2"><button type="submit" class="btn" >修改</button></td>
				</tr>
				</table>				
			</form>
			
		</div>
		

        <!--修改資料的PHP-->
        <?php

            $g_userid=isset($_POST["g_userid"])?$_POST["g_userid"]:null;

            #假如驗證的身分證不為空值
            if($g_userid!=null){
                #假如驗證的身分證和目前登入的身分證相同
                if($g_userid==$userid){
                    #修改age
                    if($new_age!=null){
                        $new_age=isset($_POST["new_age"])?$_POST["new_age"]:null;

                        #獲取現在登入者的age
                        $query = "SELECT * FROM `user` WHERE `userID`='$_SESSION[userID]'";
                        $result = $link->query($query);
                       
                        
                        $query = "UPDATE `user` SET `age`='$new_age' WHERE `userID`='$_SESSION[userID]'";
                        $count=$link->exec($query);
                    }
                    #修改身高
                    if($new_height!=null){
                        $new_height=isset($_POST["new_height"])?$_POST["new_height"]:null;

                        #獲取現在登入者的舊體重
                        $query = "SELECT * FROM `user` WHERE `userID`='$_SESSION[userID]'";
                        $result = $link->query($query);
                        $g_weight="";
                        foreach ($result as $row){
                            $g_weight=$row["height"];
                        }
                        #重新計算BMI
                        $new_BMI = $g_weight / (($new_height/100)*($new_height/100));
                        #寫入新身高與新體重
                        $query = "UPDATE `user` SET `height`='$new_height' WHERE `userID`='$_SESSION[userID]'";
                        $count=$link->exec($query);
                        $query = "UPDATE `BMI` SET `BMI`='$new_BMI' WHERE `userID`='$_SESSION[userID]'";
                        $count=$link->exec($query);
                    }
                    #修改體重
                    if($new_weight!=null){
                        $new_weight=isset($_POST["new_weight"])?$_POST["new_weight"]:null;
                        
                        #獲取現在登入者的舊身高
                        $query = "SELECT * FROM `user` WHERE `userID`='$_SESSION[userID]'";
                        $result = $link->query($query);
                        $g_height="";
                        foreach ($result as $row){
                            $g_height=$row["weight"];
                        }
                        #重新計算BMI
                        $new_BMI = $new_weight / (($g_height/100)*($g_height/100));
                        #寫入新身高與新體重
                        $query = "UPDATE `user` SET `weight`='$new_weight' WHERE `userID`='$_SESSION[userID]'";
                        $count=$link->exec($query); 
                        $query = "UPDATE `BMI` SET `BMI`='$new_BMI' WHERE `userID`='$_SESSION[userID]'";
                        $count=$link->exec($query);
                    }
                    #修改活動強度
                    if($new_disease!=null){
                        $new_disease = $_POST["new_sport"];
                        switch ($new_sport) {
                            case 1:
                                $query = "UPDATE `user` SET `sport`='輕度活動' WHERE `userID`='$_SESSION[userID]'";
                                $count=$link->exec($query); 
                                break;
                            case 2:
                                $query = "UPDATE `user` SET `sport`='中度活動' WHERE `userID`='$_SESSION[userID]'";
                                $count=$link->exec($query); 
                                break;
                            case 3:
                                $query = "UPDATE `user` SET `sport`='重度活動' WHERE `userID`='$_SESSION[userID]'";
                                $count=$link->exec($query); 
                                break;
                            }
                    }
                    #修改疾病
                    if($new_sport!=null){
                        $new_sport = $_POST["new_disease"];
                        switch ($new_disease) {
                            case 1:
                                $query = "UPDATE `user` SET `disease`='肺炎' WHERE `userID`='$_SESSION[userID]'";
                                $count=$link->exec($query); 
                                break;
                            case 2:
                                $query = "UPDATE `user` SET `disease`='糖尿病' WHERE `userID`='$_SESSION[userID]'";
                                $count=$link->exec($query); 
                                break;
                            case 3:
                                $query = "UPDATE `user` SET `disease`='高血壓' WHERE `userID`='$_SESSION[userID]'";
                                $count=$link->exec($query); 
                                break;
                            case 4:
                                $query = "UPDATE `user` SET `disease`='慢性下呼吸道疾病' WHERE `userID`='$_SESSION[userID]'";
                                $count=$link->exec($query); 
                                break;
                            case 5:
                                $query = "UPDATE `user` SET `disease`='慢性腎臟疾病' WHERE `userID`='$_SESSION[userID]'";
                                $count=$link->exec($query); 
                                break;
                            case 6:
                                $query = "UPDATE `user` SET `disease`='肝硬化' WHERE `userID`='$_SESSION[userID]'";
                                $count=$link->exec($query); 
                                break;    
                            }
                    }
                    echo "<script>alert('修改成功！')</script>";
                    echo "<meta http-equiv=REFRESH CONTENT=0;url='user_user.php'>";
                }
                else{
                    echo "<script>alert('身分證輸入錯誤，請重新輸入！')</script>";
                    echo "<meta http-equiv=REFRESH CONTENT=0;url='user_user.php'>";
                }
            }
        ?>

    </body>
</html>
