<!--輸入每日飲食 -->
<?php
  session_start();
  include("configure.php");
  $link = new PDO('mysql:host=' . $hostname . ';dbname=' . $database . ';charset=utf8', $username, $password);
  //新增資料庫沒有的飲食
  $new_nutrient = isset($_POST["new_nutrient"]) ? $_POST["new_nutrient"] : null; //新增六大類
  $new_ingredients = isset($_POST["new_ingredients"]) ? $_POST["new_ingredients"] : null; //新增食材
  $new_portion = isset($_POST["new_portion"]) ? $_POST["new_portion"] : null; //新增份量
  $NutritionalContent1 = isset($_POST["NutritionalContent1"]) ? $_POST["NutritionalContent1"] : null; //新增醣類
  $NutritionalContent2 = isset($_POST["NutritionalContent2"]) ? $_POST["NutritionalContent2"] : null; //新增蛋白質
  $NutritionalContent3 = isset($_POST["NutritionalContent3"]) ? $_POST["NutritionalContent3"] : null; //新增大卡
  $NutritionalContent4 = isset($_POST["NutritionalContent4"]) ? $_POST["NutritionalContent4"] : null; //新增大卡
  
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
      $query = "SELECT iID,count(name) FROM `ingredients` WHERE `name`='$new_ingredients[$i]'";
      $result = $link->query($query);
      $count = $result->fetchColumn();

        if($count==0){
            if($category==1){
                //全榖雜糧 NutritionalContent1=醣類 新增至ingredients
                $query = "INSERT INTO `ingredients`(`name`,`NutritionalContent`, `NID`) 
                VALUES ('$new_ingredients[$i]',$NutritionalContent1[$i],$category)";
                $count = $link->exec($query);
            }else if($category==2){
                //蛋豆魚肉 NutritionalContent3=蛋白質 新增至ingredients
                $query = "INSERT INTO `ingredients`(`name`,`NutritionalContent`, `NID`) 
                VALUES ('$new_ingredients[$i]',$NutritionalContent3[$i],$category)";
                $count = $link->exec($query);
            }else if($category==3){
                //乳品 NutritionalContent3=蛋白質 新增至ingredients
                $query = "INSERT INTO `ingredients`(`name`,`NutritionalContent`, `NID`) 
                VALUES ('$new_ingredients[$i]',$NutritionalContent3[$i],$category)";
            }else if($category==4){
                //蔬菜 NutritionalContent4=熱量 新增至ingredients
                $query = "INSERT INTO `ingredients`(`name`,`NutritionalContent`, `NID`) 
                VALUES ('$new_ingredients[$i]',$NutritionalContent4[$i],$category)";
            }else if($category==5){
                //水果 NutritionalContent4=熱量 新增至ingredients
                $query = "INSERT INTO `ingredients`(`name`,`NutritionalContent`, `NID`) 
                VALUES ('$new_ingredients[$i]',$NutritionalContent4[$i],$category)";
            }else if($category==6){
                //堅果 NutritionalContent2=脂肪 新增至ingredients
                $query = "INSERT INTO `ingredients`(`name`,`NutritionalContent`, `NID`) 
                VALUES ('$new_ingredients[$i]',$NutritionalContent2[$i],$category)";
            }else if($category==7){

            }

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