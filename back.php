  <!--輸入每日飲食 -->
  <?php
    session_start();
    include("configure.php");
    $link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
    $userid = $_SESSION['userID']; //userid
    $dishname = $_SESSION['dish_Name']; //要找的菜名       
    //搜尋t_user_add找到使用者新增的料理名稱
    $query = "SELECT * FROM `t_user_add` where `UID`= '$userid'";
    $result = $link->query($query);
    //刪除t_user_add
    $sql = "DELETE FROM `t_user_add` WHERE `UID`='$userid'";
    // 用mysqli_query方法執行(sql語法)將結果存在變數中
    $count = $link->exec($sql);
    //刪除t_user_histroy_modify
    $sql2 = "DELETE FROM `t_user_histroy_modify` WHERE `UID`='$userid'";
    // 用mysqli_query方法執行(sql語法)將結果存在變數中
    $count1 = $link->exec($sql2);

    ?>