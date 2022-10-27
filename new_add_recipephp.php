<!--輸入每日飲食 -->
<?php
  session_start();
  include("configure.php");
  $link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
  //新增資料庫沒有的飲食
  $new_nutrient = isset($_POST["new_nutrient"]) ? $_POST["new_nutrient"] : null; //新增六大類
  $new_ingredients = isset($_POST["new_ingredients"]) ? $_POST["new_ingredients"] : null; //新增食材
  $new_portion = isset($_POST["new_portion"]) ? $_POST["new_portion"] : null; //新增份量
  $carbohydrate = isset($_POST["carbohydrate"]) ? $_POST["carbohydrate"] : null; //新增碳水（醣類）
  $fat = isset($_POST["fat"]) ? $_POST["fat"] : null; //新增脂肪
  $cal = isset($_POST["cal"]) ? $_POST["cal"] : null; //新增熱量
  $protein = isset($_POST["protein"]) ? $_POST["protein"] : null; //新增蛋白質
  $totalsugar = isset($_POST["totalsugar"]) ? $_POST["totalsugar"] : null; //新增糖類
  $sodium = isset($_POST["sodium"]) ? $_POST["sodium"] : null; //新增鈉
  
  $userid= $_SESSION['userID'];
  
  //搜尋先加進去的菜名
  $query = "SELECT * FROM `t_newrecipe` WHERE `UID`='$userid'";
  $result = $link->query($query);
  
  foreach($result as $row){
      $dishName=$row["dishName"];
  }

  //改六大類文字變ID
    for($i=0;$i<count($new_ingredients);$i++){
      if($new_nutrient[$i]=="全榖雜糧類"){
          $category=1;
      }else if($new_nutrient[$i]=="豆魚蛋肉類"){
          $category=2;
      }else if($new_nutrient[$i]=="乳品類"){
          $category=3;
      }else if($new_nutrient[$i]=="蔬菜類"){
          $category=4;
      }else if($new_nutrient[$i]=="水果類"){
          $category=5;
      }else if($new_nutrient[$i]=="油脂與堅果種子類"){
          $category=6;
      }else{
          $category=7;
      }
      
      //先至ingredients搜尋name是否存在
      $query = "SELECT count(name) FROM `ingredients` WHERE `name`='$new_ingredients[$i]'";
      $result = $link->query($query);
      $count = $result->fetchColumn();

        if($count==0){
            //新增至ingredients
            $query = "INSERT INTO `ingredients`(`name`, `NID`,`cal`,`protein`,`fat`,`carbohydrate`,`totalsugar`,`sodium`) 
            VALUES ('$new_ingredients[$i]',$category,$cal[$i],$protein[$i],$fat[$i],$carbohydrate[$i],$totalsugar[$i],$sodium[$i])";
            $count = $link->exec($query);

            //新增至t_newrecipe
            $query = "INSERT INTO `t_newrecipe`(`UID`, `dishName`, `ingredients`, `portion`) 
            VALUES('$userid','$dishName','$new_ingredients[$i]','$new_portion[$i]')";
            $count = $link->exec($query);
            
        }else{
            //新增至t_newrecipe
            $query = "INSERT INTO `t_newrecipe`(`UID`, `dishName`, `ingredients`, `portion`) 
            VALUES('$userid','$dishName','$new_ingredients[$i]','$new_portion[$i]')";
            $count = $link->exec($query);
        }
      
    }
header("Location: new_recipe.php");
?>