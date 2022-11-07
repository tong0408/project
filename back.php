  <!--輸入每日飲食 -->
  <?php
	session_start();
	include("configure.php");
	$link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
	$buttonid = isset($_GET["id"]) ? $_GET["id"] : null; //返回鍵的回傳值ID
    // ID=1->new_recipe.php 最後回去 enter_diet_platform.php, ID=2->enter_diet_platform.php 最後回去 daily_diet.php,ID=3->  

    $userid= $_SESSION['userID'];//userid
	$dishname=$_SESSION['dish_Name']; //要找的菜名

    //用歷史紀錄ID去找歷史紀錄的蔡，然後去看他有多少個
    //echo $buttonid;

    if($buttonid==1){
        //刪除t_newrecipe
		$sql = "DELETE FROM `t_newrecipe` WHERE `UID`='$userid'";
		// 用mysqli_query方法執行(sql語法)將結果存在變數中
		$count = $link->exec($sql);
		header("Location: enter_diet_platform.php");

    }else if($buttonid==2){
        //搜尋t_user_add找到使用者新增的料理名稱
        $query = "SELECT * FROM `t_user_add` where `UID`= '$userid'";
		$result = $link->query($query);

        foreach($result as $row){
            $dishname=$row['dishName'];
            //用料理名稱去找dish的名稱
            $query = "SELECT * FROM `dish` WHERE `dishName`='$dishname'";
		    $res = $link->query($query);

            foreach($res as $r){
                $dishID=$r['ID'];
    
                //刪除dish
                $sql = "DELETE FROM `dish` WHERE `ID`=$dishID";
                // 用mysqli_query方法執行(sql語法)將結果存在變數中
                $count = $link->exec($sql);

                //刪除recipe
                $sql = "DELETE FROM `recipe` WHERE `dishID`=$dishID";
                // 用mysqli_query方法執行(sql語法)將結果存在變數中
                 $count = $link->exec($sql);

            }
        }

        //刪除t_user_add
		$sql = "DELETE FROM `t_user_add` WHERE `UID`='$userid'";
		// 用mysqli_query方法執行(sql語法)將結果存在變數中
		$count = $link->exec($sql);

        header("Location: daily_diet.php");
    }
  ?>