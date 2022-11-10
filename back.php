  <!--輸入每日飲食 -->
  <?php
  session_start();
  include("configure.php");
  $link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
  $userid = $_SESSION['userID']; //userid
  $dishname = $_SESSION['dish_Name']; //要找的菜名    
  //刪除t_newrecipe
  $sql = "DELETE FROM `t_newrecipe` WHERE `UID`='$userid'";
  // 用mysqli_query方法執行(sql語法)將結果存在變數中
  $count = $link->exec($sql);
  //搜尋t_user_add找到使用者新增的料理名稱
  $query = "SELECT * FROM `t_user_add` where `UID`= '$userid'";
  $result = $link->query($query);
  foreach ($result as $row) {
      $dishname = $row['dishName'];
      //用料理名稱去找dish的名稱
      $query = "SELECT * FROM `dish` WHERE `dishName`='$dishname'";
      $res = $link->query($query);

      foreach ($res as $r) {
          $dishID = $r['ID'];

          //刪除dish
          $sql = "DELETE FROM `dish` WHERE `ID`=$dishID";
          // 用mysqli_query方法執行(sql語法)將結果存在變數中
          $count = $link->exec($sql);

          //刪除recipe
          $sql = "DELETE FROM `recipe` WHERE `dishID`=$dishID";
          // 用mysqli_query方法執行(sql語法)將結果存在變數中
          $count = $link->exec($sql);

      }


      //刪除t_user_add
      $sql = "DELETE FROM `t_user_add` WHERE `UID`='$userid'";
      // 用mysqli_query方法執行(sql語法)將結果存在變數中
      $count = $link->exec($sql);      
  }
  
      //刪除t_user_histroy_modify
		$sql2 = "DELETE FROM `t_user_histroy_modify` WHERE `UID`='$userid'";
		// 用mysqli_query方法執行(sql語法)將結果存在變數中
		$count1 = $link->exec($sql2); 
    // header("Location: daily_diet.php");
    ?>