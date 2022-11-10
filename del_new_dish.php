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
   ?>