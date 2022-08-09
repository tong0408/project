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
            <title>會員檔案</title>
    </head>

    <body>
        <!--登出-->
        <button type="buttom" onclick="location.href='user_logout.php'">登出</button>
        <br />
        會員帳號：[ <?php echo $userid ;?> ]
        <br>
        <!--如果有需要修改任何會員的資料庫內資料，從這裡下去修改，語法基本上一樣-->
        修改：<br>
        <form method="POST" action="">
            修改年齡：
            <input type="text" class="form-control" name="new_age" value="<?php echo $age;?>" >
            <br />
            修改身高（公分cm）：
            <input type="text" class="form-control" name="new_height" value="<?php echo $height;?>" >
            <br />
            修改體重（公斤kg）：
            <input type="text" class="form-control" name="new_weight" value="<?php echo $weight;?>">
            <br />
            修改活動強度：<!--也是用下拉式選單-->
            <select name="new_sport" >
            <option <?php $n="selected='selected'";if($sport=="輕度活動"){echo $n;}?>value="1">輕度活動</option>
            <option <?php $n="selected='selected'";if($sport=="中度活動"){echo $n;}?>value="2">中度活動</option>
            <option <?php $n="selected='selected'";if($sport=="重度活動"){echo $n;}?>value="3">重度活動</option>
            </select><br />
            修改疾病： <!--疾病這邊是用下拉式選單-->
            <select name="new_disease">
            <option <?php $n="selected='selected'";if($disease=="肺炎"){echo $n;}?>value="1">肺炎</option>
            <option <?php $n="selected='selected'";if($disease=="糖尿病"){echo $n;}?>value="2">糖尿病</option>
            <option <?php $n="selected='selected'";if($disease=="高血壓"){echo $n;}?>value="3">高血壓</option>
            <option <?php $n="selected='selected'";if($disease=="慢性下呼吸道疾病"){echo $n;}?>value="4">慢性下呼吸道疾病</option>
            <option <?php $n="selected='selected'";if($disease=="慢性腎臟疾病"){echo $n;}?>value="5">慢性腎臟疾病</option>
            <option <?php $n="selected='selected'";if($disease=="肝硬化"){echo $n;}?>value="6">肝硬化</option>
            </select>
            <br>
            請輸入身分證做確認：
            <input type="text" class="form-control" name="g_userid" >
            <br>
            <button type="submit" class="btn btn-outline-light" >確認修改</button>
        </form>
        <br><br><br>

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
