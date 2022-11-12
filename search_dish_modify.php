  <!--輸入每日飲食 -->
  <?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	//修改分量與所含食材
    $userid= $_SESSION['userID'];//userid
	$dishname=$_SESSION['dish_Name']; //修改的菜名
    $dishid=$_SESSION['dishID'];//修改的菜id
	$ingredients = isset($_POST["ingredients"]) ? $_POST["ingredients"] : null; //修改的食材名稱
	$new_portion = isset($_POST["new_portion"]) ? $_POST["new_portion"] : null; //修改的份量

    $new_ingredients = isset($_POST["new_ingredients"]) ? $_POST["new_ingredients"] : null; //新增修改的食材名稱
	$portion = isset($_POST["portion"]) ? $_POST["portion"] : null; //新增修改的份量


    
    //先查看是否改過
    $query = "SELECT count(ID) FROM t_user_histroy_modify WHERE `UID`='$userid' AND `dishID`='$dishid' ";
    $result = $link->query($query);
    $cou = $result->fetchColumn();

    //代表有改過
    if($cou>0){

        //看有多少食材要被新增到修改的資料表count(ingredients);
        //拿來跑食材陣列的參數
        $a=0;
        //放$a值
        $b=array();
        //給b放參數
        $c=0;

        //去t_user_histroy_modify找該道菜修改過的個別食材ID
        $query = "SELECT iID FROM t_user_histroy_modify WHERE `UID`='$userid' AND `dishID`='$dishid' ";
        $result = $link->query($query);
        
        foreach($result as $row){
            $iID=$row["iID"];

            //從食材ID找到他的名字
            $query = "SELECT `name` FROM ingredients WHERE `iID`= '$iID'";
            $res = $link->query($query);
                
            foreach($res as $r){
                $iname=$r["name"];
                
                for($i=0;$i<$cou;$i++){
                    if($ingredients[$i]==$iname){
                        //ECHO $ingredients[$i].",".$new_portion[$a];
                        $b[$c]=$a;
                        $a=$a+1;
                        $c=$c+1;
                        break;
                    }
                    if($i==$cou-1){
                        $a=$a+1;
                    }
                    
                }
            }
        }

        //刪除t_user_histroy_modify該道料理
        $sql = "DELETE FROM `t_user_histroy_modify` WHERE `UID`='$userid' and `dishID`='$dishid'";
        // 用mysqli_query方法執行(sql語法)將結果存在變數中
        $count = $link->exec($sql);

            
        for($i=0;$i<count($ingredients);$i++){
                
            //echo count($ingredients).count($new_portion).$cou;
            //ECHO $ingredients[$i].$new_portion[$b[$i]];
            $query = "SELECT `iID` FROM ingredients WHERE `name`= '$ingredients[$i]'";
            $res = $link->query($query);
                    
            foreach($res as $r){
                $iID=$r["iID"];
                //ECHO $e;
                //新增至t_user_histroy_modify
                $e=$b[$i];
                $query = "INSERT INTO `t_user_histroy_modify`(`UID`, `dishID`, `iID`, `portion`) 
                VALUES('$userid',$dishid,$iID,$new_portion[$e])";
                $count = $link->exec($query);
            }
                
        }
        //echo count($new_ingredients);

        if(count($new_ingredients)!=1){
            for($i=0;$i<count($new_ingredients)-1;$i++){

                $query = "SELECT `iID` FROM ingredients WHERE `name`= '$new_ingredients[$i]'";
                $res = $link->query($query);
                    
                foreach($res as $r){
                    $iID=$r["iID"];
                    //echo $new_ingredients[$i].$portion[$i];
                    //新增至t_user_histroy_modify
                    $query = "INSERT INTO `t_user_histroy_modify`(`UID`, `dishID`, `iID`, `portion`) 
                    VALUES('$userid',$dishid,$iID,$portion[$i])";
                    $count = $link->exec($query);
                }
            }
            
            
        }

    }
    //沒改過
    else{
        //先去查該道菜有多少個食材
        $query = "SELECT count(ID) FROM recipe WHERE `dishID`='$dishid' ";
        $result = $link->query($query);
        $cou = $result->fetchColumn();
        
        //echo $count;
        //拿來跑食材陣列的參數
        $a=0;

        //去recipe找該道菜的個別食材ID
        $query = "SELECT iID FROM recipe WHERE `dishID`='$dishid' ";
        $result = $link->query($query);
        
        foreach($result as $row){
            $iID=$row["iID"];
            

            //從食材ID找到他的名字
            $query = "SELECT `name` FROM ingredients WHERE `iID`= '$iID'";
            $res = $link->query($query);
                
            foreach($res as $r){
                $iname=$r["name"];
                
                for($i=0;$i<$cou;$i++){
                    
                    if($ingredients[$i]==$iname){
                        //eCHO $ingredients[$i].",".$new_portion[$a].",".$i."<br>";
                        //新增至t_user_histroy_modify
                        $query = "INSERT INTO `t_user_histroy_modify`(`UID`, `dishID`, `iID`, `portion`) 
                        VALUES('$userid',$dishid,$iID,$new_portion[$a])";
                        $count = $link->exec($query);
                        $a=$a+1;
                        break;
                    }
                    if($i==$cou-1){
                        $a=$a+1;
                    }
                    
                }
            }
        }
                if(count($new_ingredients)!=1){
                    for($i=0;$i<count($new_ingredients)-1;$i++){
        
                        $query = "SELECT `iID` FROM ingredients WHERE `name`= '$new_ingredients[$i]'";
                        $res = $link->query($query);
                            
                        foreach($res as $r){
                            $iID=$r["iID"];
                            //echo $new_ingredients[$i].$portion[$i];
                            //新增至t_user_histroy_modify
                            $query = "INSERT INTO `t_user_histroy_modify`(`UID`, `dishID`, `iID`, `portion`) 
                            VALUES('$userid',$dishid,$iID,$portion[$i])";
                            $count = $link->exec($query);
                        }
                    }
                    
                    
                }
                    
            
        
    }

	header("Location: enter_diet_platform.php");
  ?>