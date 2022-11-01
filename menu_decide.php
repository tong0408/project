<?php

    #抓取資料庫目前吃的六大類數值
    
    #預設起始值為吃0份
    $now_category=array(0,0,0,0,0,0);
    $now_cal=0;
    $now_glyco=0;
	$now_fat=0;
	$now_protein=0;
    $now_suger=0;
    $now_sodium=0;

    #抓取資料庫紀錄
    #history -> dishID portion
    #recipe -> history.dishID可找到食材iID及一份的portion (*history.portion)
    #ingredients -> 食材iID找到他的營養素(內都是100g為主)
    ###
    #全榖雜糧，每份醣類15g
	#蛋豆魚肉，每份蛋白質7g
	#乳品類，每份蛋白質8g
	#蔬菜類，每份25大卡
	#水果類，每份60大卡
	#油脂與堅果種子類，每份脂肪5g
    ###
    #recipe.portion(*history.portion)*(食材主營養素/100) / 食材主營養素每份數要求 -> 獲得六大類份數
    #ingredients.cal / 100 * recipe.portion(*history.portion)-> 紀錄卡路里
    #ingredients.protien /100 * recipe.portion(*history.portion)-> 記錄蛋白質
    #ingredients.fat /100 * recipe.portion(*history.portion)-> 記錄脂肪
    #ingredients.carbohydrate /100 * recipe.portion(*history.portion)-> 記錄醣類 glyco
    #ingredients.sodium /100 * recipe.portion(*history.portion) ->紀錄鈉含量

    $today = date("Y-m-d");
    $query = "SELECT * FROM `history` WHERE `UID`='$userID' and `date`='$today'";
    $result = $link->query($query);

    #history
    foreach ($result as $row){
        $history_dishID=$row["dishID"];
        $history_portion=$row["portion"];

        #recipe
        $query = "SELECT * FROM `recipe` WHERE `dishID`='$history_dishID'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recipe_iID=$row["iID"];
            $recipe_portion=$row["portion"];

            #ingredients
            $query = "SELECT * FROM `ingredients` WHERE `iID`='$recipe_iID'";
            $result = $link->query($query);
            foreach ($result as $row){

                $ingredients_NID=$row["NID"]; #食材的六大類歸屬
                $ingredients_cal=$row["cal"]; #食材的卡路里
                $ingredients_protein=$row["protein"]; #食材的蛋白質
                $ingredients_fat=$row["fat"]; #食材的脂質
                $ingredients_saturatedfat=$row["saturatedfat"]; #食材的飽和脂肪
                $ingredients_glyco=$row["carbohydrate"]; #食材的醣類
                $ingredients_totalsugar=$row["totalsugar"]; #食材的總糖
                $ingredients_sodium=$row["sodium"]; #食材的鈉含量

                #獲取六大類份數
                if($ingredients_NID==1){ #全榖雜糧 醣類/15
                    $now_category[0]=$now_category[0]+round((($recipe_portion*$history_portion)*($ingredients_glyco/100))/15,1);
                }
                else if($ingredients_NID==2){ #蛋豆魚肉 蛋白質/7
                    $now_category[1]=$now_category[1]+round((($recipe_portion*$history_portion)*($ingredients_protein/100))/7,1);
                }
                else if($ingredients_NID==3){ #乳品類 蛋白質/8
                    $now_category[2]=$now_category[2]+round((($recipe_portion*$history_portion)*($ingredients_protein/100))/8,1);
                }
                else if($ingredients_NID==4){ #蔬菜類 熱量/25
                    $now_category[3]=$now_category[3]+round((($recipe_portion*$history_portion)*($ingredients_cal/100))/25,1);
                }
                else if($ingredients_NID==5){ #水果類 熱量/60
                    $now_category[4]=$now_category[4]+round((($recipe_portion*$history_portion)*($ingredients_cal/100))/60,1);
                }
                else if($ingredients_NID==6){ #油脂與堅果種子 脂肪/5
                    $now_category[5]=$now_category[5]+round((($recipe_portion*$history_portion)*($ingredients_fat/100))/5,1);
                }

                #計算三大營養素+熱量(食材都是100g，先算出1g的營養素，再乘上食譜所用的克數及吃了幾份)
                $now_cal = $now_cal + round(($ingredients_cal / 100) * ($recipe_portion*$history_portion),1);
                $now_glyco = $now_glyco + round(($ingredients_glyco / 100) * ($recipe_portion*$history_portion),1);
                $now_fat = $now_fat + round(($ingredients_fat / 100) * ($recipe_portion*$history_portion),1);
                $now_protein = $now_protein + round(($ingredients_protein / 100) * ($recipe_portion*$history_portion),1);
                $now_suger = $now_suger + round(($ingredients_totalsugar / 100) * ($recipe_portion*$history_portion),1);
                $now_sodium = $now_sodium + round((($ingredients_sodium / 100) * ($recipe_portion*$history_portion)/1000),1);
                
            }
        }
    }

    #陣列先找出份數最小值 （目標是把每一類都能夠吃平均）
    #邏輯是0>1>2>3>4>5 同時有兩個量最少的話會以前面的種類先推薦
    #推薦以那類份數為主的食材
    #先找到ingredients.NID為需求類的食材
    #ingredients.iID=recipe.iID 找到食譜dishID
    #dish.ID=recipe.dishID 推回食譜推薦
    ##1029 更新
    ##如果有些類別的攝取已經達標/超過 就不再推薦有包含那類別的食譜

    $search = array_search(min($now_category),$now_category);
    $recommend_dishID = array();
    $recommend_dishName = array();
    $recommend_method = array();

    if($search==0){ 
    #全榖雜糧量最少
        #撈出食材有包含全榖雜糧類的菜
        $query = "SELECT * FROM `ingredients` WHERE `NID`='1'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];
            #撈出該食材編號
            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];
                ######
                ##假如2超標
                if($now_category[1]>$goal_category[1]){
                    #撈出食材資訊且不能有2的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如2,3超標
                    if($now_category[2]>$goal_category[2]){ 
                        #撈出食材資訊且不能有2.3的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ####
                        ##假如2,3,4超標
                        if($now_category[3]>$goal_category[3]){ 
                            #撈出食材資訊且不能有2,3,4的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3 
                            and `ingredients`.`NID`=4)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }            
                            ####
                            ##假如2,3,4,5超標                
                            if($now_category[4]>$goal_category[4]){ 
                                #撈出食材資訊且不能有2,3,4,5的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3 
                                and `ingredients`.`NID`=4 and `ingredients`.`NID`=5)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }            
                                ####
                                ##假如2,3,4,5,6超標
                                if($now_category[5]>$goal_category[5]){ 
                                    #撈出食材資訊且不能有2,3,4,5,6的食材在內
                                    $query = "SELECT DISTINCT `dish`.*
                                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                    (SELECT `dish`.`ID`
                                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3 
                                    and `ingredients`.`NID`=4 and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                                    $result = $link->query($query);
                                    foreach ($result as $row){
                                        $database_recommend_dish_ID=$row["ID"];
                                        $database_recommend_dish_Name=$row["dishName"];
                                        $database_recommend_dish_method=$row["method"];
                                        array_push($recommend_dishID,$database_recommend_dish_ID);
                                        array_push($recommend_dishName,$database_recommend_dish_Name);
                                        array_push($recommend_method,$database_recommend_dish_method);
                                    }    
                                }
                            }
                        }
                    }
                }
                ##假如3超標
                else if($now_category[2]>$goal_category[2]){ 
                    #撈出食材資訊且不能有3的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如3,4超標
                    if($now_category[3]>$goal_category[3]){ 
                        #撈出食材資訊且不能有3,4的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=4)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ####
                        ##假如3,4,5超標
                        if($now_category[4]>$goal_category[4]){ 
                            #撈出食材資訊且不能有3,4,5的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=4
                            and `ingredients`.`NID`=5)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                            ####
                            ##假如3,4,5,6超標
                            if($now_category[5]>$goal_category[5]){ 
                                #撈出食材資訊且不能有3,4,5,6的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=4
                                and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }                            
                            }
                        }
                    }
                }
                ##假如4超標
                else if($now_category[3]>$goal_category[3]){
                    #撈出食材資訊且不能有4的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }                   
                    ####
                    ##假如4,5超標
                    if($now_category[4]>$goal_category[4]){ 
                        #撈出食材資訊且不能有4,5的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4 and `ingredients`.`NID`=5)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ######假如4,5,6超標
                        if($now_category[5]>$goal_category[5]){ 
                            #撈出食材資訊且不能有4,5,6的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4 and `ingredients`.`NID`=5
                            and `ingredients`.`NID`=6)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                        }
                    }
                }
                ##假如5超標
                else if($now_category[4]>$goal_category[4]){ 
                    #撈出食材資訊且不能有5的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=5)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                    ####
                    ##假如5,6超標
                    if($now_category[5]>$goal_category[5]){ 
                        #撈出食材資訊且不能有5,6的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }  
                    }
                }
                ##假如6超標
                else if($now_category[5]>$goal_category[5]){
                    #撈出食材資訊且不能有6的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=6)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                }
                else{
                    ##假如沒人超標
                    $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                }
            }
        }
    }
    ###
    #蛋豆魚肉量最少
    else if($search==1){
        #撈出食材有包含蛋豆魚肉類的菜
        $query = "SELECT * FROM `ingredients` WHERE `NID`='2'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];
            #撈出該食材編號
            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];
                ######
                ##假如1超標
                if($now_category[0]>$goal_category[0]){
                    #撈出食材資訊且不能有1的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如1,3超標
                    if($now_category[2]>$goal_category[2]){ 
                        #撈出食材資訊且不能有1.3的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=3)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ####
                        ##假如1,3,4超標
                        if($now_category[3]>$goal_category[3]){ 
                            #撈出食材資訊且不能有1,3,4的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=3 
                            and `ingredients`.`NID`=4)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }            
                            ####
                            ##假如1,3,4,5超標                
                            if($now_category[4]>$goal_category[4]){ 
                                #撈出食材資訊且不能有1,3,4,5的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=3 
                                and `ingredients`.`NID`=4 and `ingredients`.`NID`=5)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }            
                                ####
                                ##假如1,3,4,5,6超標
                                if($now_category[5]>$goal_category[5]){ 
                                    #撈出食材資訊且不能有1,3,4,5,6的食材在內
                                    $query = "SELECT DISTINCT `dish`.*
                                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                    (SELECT `dish`.`ID`
                                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=3 
                                    and `ingredients`.`NID`=4 and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                                    $result = $link->query($query);
                                    foreach ($result as $row){
                                        $database_recommend_dish_ID=$row["ID"];
                                        $database_recommend_dish_Name=$row["dishName"];
                                        $database_recommend_dish_method=$row["method"];
                                        array_push($recommend_dishID,$database_recommend_dish_ID);
                                        array_push($recommend_dishName,$database_recommend_dish_Name);
                                        array_push($recommend_method,$database_recommend_dish_method);
                                    }    
                                }
                            }
                        }
                    }
                }
                ##假如3超標
                else if($now_category[2]>$goal_category[2]){ 
                    #撈出食材資訊且不能有3的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如3,4超標
                    if($now_category[3]>$goal_category[3]){ 
                        #撈出食材資訊且不能有3,4的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=4)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ####
                        ##假如3,4,5超標
                        if($now_category[4]>$goal_category[4]){ 
                            #撈出食材資訊且不能有3,4,5的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=4
                            and `ingredients`.`NID`=5)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                            ####
                            ##假如3,4,5,6超標
                            if($now_category[5]>$goal_category[5]){ 
                                #撈出食材資訊且不能有3,4,5,6的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=4
                                and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }                            
                            }
                        }
                    }
                }
                ##假如4超標
                else if($now_category[3]>$goal_category[3]){
                    #撈出食材資訊且不能有4的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }                   
                    ####
                    ##假如4,5超標
                    if($now_category[4]>$goal_category[4]){ 
                        #撈出食材資訊且不能有4,5的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4 and `ingredients`.`NID`=5)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ######假如4,5,6超標
                        if($now_category[5]>$goal_category[5]){ 
                            #撈出食材資訊且不能有4,5,6的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4 and `ingredients`.`NID`=5
                            and `ingredients`.`NID`=6)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                        }
                    }
                }
                ##假如5超標
                else if($now_category[4]>$goal_category[4]){ 
                    #撈出食材資訊且不能有5的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=5)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                    ####
                    ##假如5,6超標
                    if($now_category[5]>$goal_category[5]){ 
                        #撈出食材資訊且不能有5,6的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }  
                    }
                }
                ##假如6超標
                else if($now_category[5]>$goal_category[5]){
                    #撈出食材資訊且不能有6的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=6)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                }
                else{
                    ##假如沒人超標
                    $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                }
            }
        }
    }
    ###
    #乳品類量最少
    else if($search==2){
        #撈出食材有包含乳品類的菜
        $query = "SELECT * FROM `ingredients` WHERE `NID`='3'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];
            #撈出該食材編號
            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];
                ######
                ##假如1超標
                if($now_category[0]>$goal_category[0]){
                    #撈出食材資訊且不能有1的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如1,2超標
                    if($now_category[1]>$goal_category[1]){ 
                        #撈出食材資訊且不能有1.2的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        echo $database_recommend_dish_Name."<br>";
                        ####
                        ##假如1,2,4超標
                        if($now_category[3]>$goal_category[3]){
                            #撈出食材資訊且不能有1,2,4的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                            and `ingredients`.`NID`=4)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }            
                            ####
                            ##假如1,2,4,5超標                
                            if($now_category[4]>$goal_category[4]){ 
                                #撈出食材資訊且不能有1,2,4,5的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                                and `ingredients`.`NID`=4 and `ingredients`.`NID`=5)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }            
                                ####
                                ##假如1,2,4,5,6超標
                                if($now_category[5]>$goal_category[5]){ 
                                    #撈出食材資訊且不能有1,2,4,5,6的食材在內
                                    $query = "SELECT DISTINCT `dish`.*
                                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                    (SELECT `dish`.`ID`
                                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                                    and `ingredients`.`NID`=4 and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                                    $result = $link->query($query);
                                    foreach ($result as $row){
                                        $database_recommend_dish_ID=$row["ID"];
                                        $database_recommend_dish_Name=$row["dishName"];
                                        $database_recommend_dish_method=$row["method"];
                                        array_push($recommend_dishID,$database_recommend_dish_ID);
                                        array_push($recommend_dishName,$database_recommend_dish_Name);
                                        array_push($recommend_method,$database_recommend_dish_method);
                                    }    
                                }
                            }
                        }
                    }
                }
                ##假如2超標
                else if($now_category[1]>$goal_category[1]){ 
                    #撈出食材資訊且不能有2的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如2,4超標
                    if($now_category[3]>$goal_category[3]){ 
                        #撈出食材資訊且不能有3,4的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=4)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ####
                        ##假如2,4,5超標
                        if($now_category[4]>$goal_category[4]){ 
                            #撈出食材資訊且不能有2,4,5的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=4
                            and `ingredients`.`NID`=5)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                            ####
                            ##假如2,4,5,6超標
                            if($now_category[5]>$goal_category[5]){ 
                                #撈出食材資訊且不能有2,4,5,6的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=4
                                and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }                            
                            }
                        }
                    }
                }
                ##假如4超標
                else if($now_category[3]>$goal_category[3]){
                    #撈出食材資訊且不能有4的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }                   
                    ####
                    ##假如4,5超標
                    if($now_category[4]>$goal_category[4]){ 
                        #撈出食材資訊且不能有4,5的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4 and `ingredients`.`NID`=5)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ######假如4,5,6超標
                        if($now_category[5]>$goal_category[5]){ 
                            #撈出食材資訊且不能有4,5,6的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4 and `ingredients`.`NID`=5
                            and `ingredients`.`NID`=6)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                        }
                    }
                }
                ##假如5超標
                else if($now_category[4]>$goal_category[4]){ 
                    #撈出食材資訊且不能有5的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=5)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                    ####
                    ##假如5,6超標
                    if($now_category[5]>$goal_category[5]){ 
                        #撈出食材資訊且不能有5,6的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }  
                    }
                }
                ##假如6超標
                else if($now_category[5]>$goal_category[5]){
                    #撈出食材資訊且不能有6的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=6)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                }
                else{
                    ##假如沒人超標
                    $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                }
            }
        }
    }
    ###
    #蔬菜類量最少
    else if($search==3){
        #撈出食材有包含蔬菜類的菜
        $query = "SELECT * FROM `ingredients` WHERE `NID`='4'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];
            #撈出該食材編號
            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];
                ######
                ##假如1超標
                if($now_category[0]>$goal_category[0]){
                    #撈出食材資訊且不能有1的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如1,2超標
                    if($now_category[1]>$goal_category[1]){ 
                        #撈出食材資訊且不能有1.2的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ####
                        ##假如1,2,3超標
                        if($now_category[2]>$goal_category[2]){ 
                            #撈出食材資訊且不能有1,2,3的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                            and `ingredients`.`NID`=3)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }            
                            ####
                            ##假如1,2,3,5超標                
                            if($now_category[4]>$goal_category[4]){ 
                                #撈出食材資訊且不能有1,2,3,5的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                                and `ingredients`.`NID`=3 and `ingredients`.`NID`=5)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }            
                                ####
                                ##假如1,2,3,5,6超標
                                if($now_category[5]>$goal_category[5]){ 
                                    #撈出食材資訊且不能有1,2,3,5,6的食材在內
                                    $query = "SELECT DISTINCT `dish`.*
                                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                    (SELECT `dish`.`ID`
                                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                                    and `ingredients`.`NID`=3 and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                                    $result = $link->query($query);
                                    foreach ($result as $row){
                                        $database_recommend_dish_ID=$row["ID"];
                                        $database_recommend_dish_Name=$row["dishName"];
                                        $database_recommend_dish_method=$row["method"];
                                        array_push($recommend_dishID,$database_recommend_dish_ID);
                                        array_push($recommend_dishName,$database_recommend_dish_Name);
                                        array_push($recommend_method,$database_recommend_dish_method);
                                    }    
                                }
                            }
                        }
                    }
                }
                ##假如2超標
                else if($now_category[1]>$goal_category[1]){ 
                    #撈出食材資訊且不能有2的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如2,3超標
                    if($now_category[2]>$goal_category[2]){ 
                        #撈出食材資訊且不能有2,3的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ####
                        ##假如2,3,5超標
                        if($now_category[4]>$goal_category[4]){ 
                            #撈出食材資訊且不能有2,3,5的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3
                            and `ingredients`.`NID`=5)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                            ####
                            ##假如2,3,5,6超標
                            if($now_category[5]>$goal_category[5]){ 
                                #撈出食材資訊且不能有2,3,5,6的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3
                                and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }                            
                            }
                        }
                    }
                }
                ##假如3超標
                else if($now_category[2]>$goal_category[2]){
                    #撈出食材資訊且不能有3的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }                   
                    ####
                    ##假如3,5超標
                    if($now_category[4]>$goal_category[4]){ 
                        #撈出食材資訊且不能有3,5的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=5)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ######假如3,5,6超標
                        if($now_category[5]>$goal_category[5]){ 
                            #撈出食材資訊且不能有3,5,6的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=5
                            and `ingredients`.`NID`=6)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                        }
                    }
                }
                ##假如5超標
                else if($now_category[4]>$goal_category[4]){ 
                    #撈出食材資訊且不能有5的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=5)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                    ####
                    ##假如5,6超標
                    if($now_category[5]>$goal_category[5]){ 
                        #撈出食材資訊且不能有5,6的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=5 and `ingredients`.`NID`=6)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }  
                    }
                }
                ##假如6超標
                else if($now_category[5]>$goal_category[5]){
                    #撈出食材資訊且不能有6的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=6)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                }
                else{
                    ##假如沒人超標
                    $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                }
            }
        }
    }
    ###
    #水果類量最少
    else if($search==4){
        #撈出食材有包含水果類的菜
        $query = "SELECT * FROM `ingredients` WHERE `NID`='5'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];
            #撈出該食材編號
            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];
                ######
                ##假如1超標
                if($now_category[0]>$goal_category[0]){
                    #撈出食材資訊且不能有1的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如1,2超標
                    if($now_category[1]>$goal_category[1]){ 
                        #撈出食材資訊且不能有1.2的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ####
                        ##假如1,2,3超標
                        if($now_category[2]>$goal_category[2]){ 
                            #撈出食材資訊且不能有1,2,3的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                            and `ingredients`.`NID`=3)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }            
                            ####
                            ##假如1,2,3,4超標                
                            if($now_category[3]>$goal_category[3]){ 
                                #撈出食材資訊且不能有1,2,4,5的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                                and `ingredients`.`NID`=3 and `ingredients`.`NID`=4)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }            
                                ####
                                ##假如1,2,3,4,6超標
                                if($now_category[5]>$goal_category[5]){ 
                                    #撈出食材資訊且不能有1,2,3,5,6的食材在內
                                    $query = "SELECT DISTINCT `dish`.*
                                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                    (SELECT `dish`.`ID`
                                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                                    and `ingredients`.`NID`=3 and `ingredients`.`NID`=4 and `ingredients`.`NID`=6)";
                                    $result = $link->query($query);
                                    foreach ($result as $row){
                                        $database_recommend_dish_ID=$row["ID"];
                                        $database_recommend_dish_Name=$row["dishName"];
                                        $database_recommend_dish_method=$row["method"];
                                        array_push($recommend_dishID,$database_recommend_dish_ID);
                                        array_push($recommend_dishName,$database_recommend_dish_Name);
                                        array_push($recommend_method,$database_recommend_dish_method);
                                    }    
                                }
                            }
                        }
                    }
                }
                ##假如2超標
                else if($now_category[1]>$goal_category[1]){ 
                    #撈出食材資訊且不能有2的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如2,3超標
                    if($now_category[2]>$goal_category[2]){ 
                        #撈出食材資訊且不能有2,3的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ####
                        ##假如2,3,4超標
                        if($now_category[3]>$goal_category[3]){ 
                            #撈出食材資訊且不能有2,3,4的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3
                            and `ingredients`.`NID`=4)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                            ####
                            ##假如2,3,4,6超標
                            if($now_category[5]>$goal_category[5]){ 
                                #撈出食材資訊且不能有2,3,4,6的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3
                                and `ingredients`.`NID`=4 and `ingredients`.`NID`=6)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }                            
                            }
                        }
                    }
                }
                ##假如3超標
                else if($now_category[2]>$goal_category[2]){
                    #撈出食材資訊且不能有3的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }                   
                    ####
                    ##假如3,4超標
                    if($now_category[3]>$goal_category[3]){ 
                        #撈出食材資訊且不能有3,4的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=4)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ######假如3,4,6超標
                        if($now_category[5]>$goal_category[5]){ 
                            #撈出食材資訊且不能有3,5,6的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=4
                            and `ingredients`.`NID`=6)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                        }
                    }
                }
                ##假如4超標
                else if($now_category[3]>$goal_category[3]){ 
                    #撈出食材資訊且不能有4的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                    ####
                    ##假如4,6超標
                    if($now_category[5]>$goal_category[5]){ 
                        #撈出食材資訊且不能有4,6的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4 and `ingredients`.`NID`=6)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }  
                    }
                }
                ##假如6超標
                else if($now_category[5]>$goal_category[5]){
                    #撈出食材資訊且不能有6的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=6)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                }
                else{
                    ##假如沒人超標
                    $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                }
            }
        }
    }
    ###
    #油脂與堅果種子類量最少
    else if($search==5){  
        #撈出食材有包含油脂與堅果種子類量的菜
        $query = "SELECT * FROM `ingredients` WHERE `NID`='6'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];
            #撈出該食材編號
            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];
                ######
                ##假如1超標
                if($now_category[0]>$goal_category[0]){
                    #撈出食材資訊且不能有1的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如1,2超標
                    if($now_category[1]>$goal_category[1]){ 
                        #撈出食材資訊且不能有1.2的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ####
                        ##假如1,2,3超標
                        if($now_category[2]>$goal_category[2]){ 
                            #撈出食材資訊且不能有1,2,3的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                            and `ingredients`.`NID`=3)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }            
                            ####
                            ##假如1,2,3,4超標                
                            if($now_category[3]>$goal_category[3]){ 
                                #撈出食材資訊且不能有1,2,4,5的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                                and `ingredients`.`NID`=3 and `ingredients`.`NID`=4)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }            
                                ####
                                ##假如1,2,3,4,5超標
                                if($now_category[5]>$goal_category[5]){ 
                                    #撈出食材資訊且不能有1,2,3,4,5的食材在內
                                    $query = "SELECT DISTINCT `dish`.*
                                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                    (SELECT `dish`.`ID`
                                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=1 and `ingredients`.`NID`=2 
                                    and `ingredients`.`NID`=3 and `ingredients`.`NID`=4 and `ingredients`.`NID`=5)";
                                    $result = $link->query($query);
                                    foreach ($result as $row){
                                        $database_recommend_dish_ID=$row["ID"];
                                        $database_recommend_dish_Name=$row["dishName"];
                                        $database_recommend_dish_method=$row["method"];
                                        array_push($recommend_dishID,$database_recommend_dish_ID);
                                        array_push($recommend_dishName,$database_recommend_dish_Name);
                                        array_push($recommend_method,$database_recommend_dish_method);
                                    }    
                                }
                            }
                        }
                    }
                }
                ##假如2超標
                else if($now_category[1]>$goal_category[1]){ 
                    #撈出食材資訊且不能有2的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                    ####
                    ##假如2,3超標
                    if($now_category[2]>$goal_category[2]){ 
                        #撈出食材資訊且不能有2,3的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ####
                        ##假如2,3,4超標
                        if($now_category[3]>$goal_category[3]){ 
                            #撈出食材資訊且不能有2,3,4的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3
                            and `ingredients`.`NID`=4)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                            ####
                            ##假如2,3,4,5超標
                            if($now_category[5]>$goal_category[5]){ 
                                #撈出食材資訊且不能有2,3,4,5的食材在內
                                $query = "SELECT DISTINCT `dish`.*
                                FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                                (SELECT `dish`.`ID`
                                FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                                INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                                WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=2 and `ingredients`.`NID`=3
                                and `ingredients`.`NID`=4 and `ingredients`.`NID`=5)";
                                $result = $link->query($query);
                                foreach ($result as $row){
                                    $database_recommend_dish_ID=$row["ID"];
                                    $database_recommend_dish_Name=$row["dishName"];
                                    $database_recommend_dish_method=$row["method"];
                                    array_push($recommend_dishID,$database_recommend_dish_ID);
                                    array_push($recommend_dishName,$database_recommend_dish_Name);
                                    array_push($recommend_method,$database_recommend_dish_method);
                                }                            
                            }
                        }
                    }
                }
                ##假如3超標
                else if($now_category[2]>$goal_category[2]){
                    #撈出食材資訊且不能有3的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }                   
                    ####
                    ##假如3,4超標
                    if($now_category[3]>$goal_category[3]){ 
                        #撈出食材資訊且不能有3,4的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=4)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }
                        ######假如3,4,5超標
                        if($now_category[5]>$goal_category[5]){ 
                            #撈出食材資訊且不能有3,5,6的食材在內
                            $query = "SELECT DISTINCT `dish`.*
                            FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                            (SELECT `dish`.`ID`
                            FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                            INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                            WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=3 and `ingredients`.`NID`=4
                            and `ingredients`.`NID`=5)";
                            $result = $link->query($query);
                            foreach ($result as $row){
                                $database_recommend_dish_ID=$row["ID"];
                                $database_recommend_dish_Name=$row["dishName"];
                                $database_recommend_dish_method=$row["method"];
                                array_push($recommend_dishID,$database_recommend_dish_ID);
                                array_push($recommend_dishName,$database_recommend_dish_Name);
                                array_push($recommend_method,$database_recommend_dish_method);
                            }
                        }
                    }
                }
                ##假如4超標
                else if($now_category[3]>$goal_category[3]){ 
                    #撈出食材資訊且不能有4的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                    ####
                    ##假如4,5超標
                    if($now_category[5]>$goal_category[5]){ 
                        #撈出食材資訊且不能有4,6的食材在內
                        $query = "SELECT DISTINCT `dish`.*
                        FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                        (SELECT `dish`.`ID`
                        FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                        INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                        WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=4 and `ingredients`.`NID`=5)";
                        $result = $link->query($query);
                        foreach ($result as $row){
                            $database_recommend_dish_ID=$row["ID"];
                            $database_recommend_dish_Name=$row["dishName"];
                            $database_recommend_dish_method=$row["method"];
                            array_push($recommend_dishID,$database_recommend_dish_ID);
                            array_push($recommend_dishName,$database_recommend_dish_Name);
                            array_push($recommend_method,$database_recommend_dish_method);
                        }  
                    }
                }
                ##假如5超標
                else if($now_category[4]>$goal_category[4]){
                    #撈出食材資訊且不能有5的食材在內
                    $query = "SELECT DISTINCT `dish`.*
                    FROM `dish` INNER JOIN `recipe` ON `dish`.`ID` = `recipe`.`dishID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `dish`.`ID` NOT IN
                    (SELECT `dish`.`ID`
                    FROM `dish` INNER JOIN `recipe` ON `recipe`.`dishID`=`dish`.`ID`
                    INNER JOIN `ingredients` ON `ingredients`.`iID`=`recipe`.`iID`
                    WHERE `dish`.`ID`='$recommend_recipe_dishID' and `ingredients`.`NID`=5)";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }  
                }
                else{
                    ##假如沒人超標
                    $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                    $result = $link->query($query);
                    foreach ($result as $row){
                        $database_recommend_dish_ID=$row["ID"];
                        $database_recommend_dish_Name=$row["dishName"];
                        $database_recommend_dish_method=$row["method"];
                        array_push($recommend_dishID,$database_recommend_dish_ID);
                        array_push($recommend_dishName,$database_recommend_dish_Name);
                        array_push($recommend_method,$database_recommend_dish_method);
                    }
                }
            }
        }        
    }

    ######疾病
    #高血壓 
        #紅肉代替白肉
        #鈉含量極高ㄉ食物不推薦(標準是??)
    #慢性下呼吸道疾病
        #高脂肪含量的肥肉(標準是??)
        #海產品
        #生禽肉
        #辛辣或過甜過鹹食物
    #慢性腎臟疾病
        #肉臟類
        #全穀類
        #含鉀量較高之水果(標準??)
        #少豆類
        #少麵筋
        #少乳製品
        #少堅果
    #肝硬化
        #澱粉類攝取 450g 薯類60g
		#蔬菜類攝取 黃綠色蔬菜100g 淺色蔬菜200g
		#水果類調整 200g
        #紅蘿蔔 罐頭 加工食品 紫菜 鈉含量較高的(標準??)

    #$use_recommend_dishID = array_unique($recommend_dishID);
    #$use_recommend_dishName = array_unique($recommend_dishName);
    #$use_recommend_method = array_unique($recommend_method);
    #$count_use_recommend_dishID = count($use_recommend_dishID);
    $count_recommend_dishID = count($recommend_dishID);
    # print_r($recommend_dishName);
    # 迴圈跑出推薦菜單的ID
    # foreach ($use_recommend_dishID as $use_recommend_dishID){
    #    echo $use_recommend_dishID."<br>";
    # }
    # 獲取陣列內有多少個
    # echo count($use_recommend_dishID);
    # 使用rand()去抓10個ID去推薦

?>
