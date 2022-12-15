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

    ## history紀錄
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

    ## user_histroy_modify 紀錄
    $query = "SELECT * FROM `user_histroy_modify` WHERE `UID`='$userID' and `date`='$today'";
    $result = $link->query($query);

    #user_histroy_modify
    foreach ($result as $row){
        $modify_history_dishID=$row["dishID"];
        $modify_history_iID=$row["iID"];
        $modify_history_iprotion=$row["iportion"];
        $modify_history_portion=$row["portion"];

        #ingredients
        $query = "SELECT * FROM `ingredients` WHERE `iID`='$modify_history_iID'";
        $result = $link->query($query);
        foreach ($result as $row){

            $modify_ingredients_NID=$row["NID"]; #食材的六大類歸屬
            $modify_ingredients_cal=$row["cal"]; #食材的卡路里
            $modify_ingredients_protein=$row["protein"]; #食材的蛋白質
            $modify_ingredients_fat=$row["fat"]; #食材的脂質
            $modify_ingredients_saturatedfat=$row["saturatedfat"]; #食材的飽和脂肪
            $modify_ingredients_glyco=$row["carbohydrate"]; #食材的醣類
            $modify_ingredients_totalsugar=$row["totalsugar"]; #食材的總糖
            $modify_ingredients_sodium=$row["sodium"]; #食材的鈉含量

            #獲取六大類份數
            if($modify_ingredients_NID==1){ #全榖雜糧 醣類/15
                $now_category[0]=$now_category[0]+round((($modify_history_iprotion*$modify_history_portion)*($modify_ingredients_glyco/100))/15,1);
            }
            else if($modify_ingredients_NID==2){ #蛋豆魚肉 蛋白質/7
                $now_category[1]=$now_category[1]+round((($modify_history_iprotion*$modify_history_portion)*($modify_ingredients_protein/100))/7,1);
            }
            else if($modify_ingredients_NID==3){ #乳品類 蛋白質/8
                $now_category[2]=$now_category[2]+round((($modify_history_iprotion*$modify_history_portion)*($modify_ingredients_protein/100))/8,1);
            }
            else if($modify_ingredients_NID==4){ #蔬菜類 熱量/25
                $now_category[3]=$now_category[3]+round((($modify_history_iprotion*$modify_history_portion)*($modify_ingredients_cal/100))/25,1);
            }
            else if($modify_ingredients_NID==5){ #水果類 熱量/60
                $now_category[4]=$now_category[4]+round((($modify_history_iprotion*$modify_history_portion)*($modify_ingredients_cal/100))/60,1);
            }
            else if($modify_ingredients_NID==6){ #油脂與堅果種子 脂肪/5
                $now_category[5]=$now_category[5]+round((($modify_history_iprotion*$modify_history_portion)*($modify_ingredients_fat/100))/5,1);
            }

            #計算三大營養素+熱量(食材都是100g，先算出1g的營養素，再乘上食譜所用的克數及吃了幾份)
            $now_cal = $now_cal + round(($modify_ingredients_cal / 100) * ($modify_history_iprotion*$modify_history_portion),1);
            $now_glyco = $now_glyco + round(($modify_ingredients_glyco / 100) * ($modify_history_iprotion*$modify_history_portion),1);
            $now_fat = $now_fat + round(($modify_ingredients_fat / 100) * ($modify_history_iprotion*$modify_history_portion),1);
            $now_protein = $now_protein + round(($modify_ingredients_protein / 100) * ($modify_history_iprotion*$modify_history_portion),1);
            $now_suger = $now_suger + round(($modify_ingredients_totalsugar / 100) * ($modify_history_iprotion*$modify_history_portion),1);
            $now_sodium = $now_sodium + round((($modify_ingredients_sodium / 100) * ($modify_history_iprotion*$modify_history_portion)/1000),1);
                
        }
    }
    #陣列先找出份數/目標%數最小值 （目標是把每一類都能夠吃平均）
    #邏輯是0>1>2>3>4>5 同時有兩個量最少的話會以前面的種類先推薦
    #推薦以那類份數為主的食材
    #先找到ingredients.NID為需求類的食材
    #ingredients.iID=recipe.iID 找到食譜dishID
    #dish.ID=recipe.dishID 推回食譜推薦
    ##1029 更新
    ##如果有些類別的攝取已經達標/超過 就不再推薦有包含那類別的食譜

    ##找%數最低
    $p_now_category = array();
    for($i=0;$i<6;$i++){
        $p_now_category[$i] = round($now_category[$i] / $goal_category[$i],2);
    }
    $search = array_search(min($p_now_category),$p_now_category);
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
    $filter_HighBlood = array("鮮雞精","烹大師鰹魚粉","雞湯塊","雞高湯","鰹魚粉","乾麒麟菜","香菇粉","雞粉","高湯塊(排骨)","高鮮味精","味精","梅乾菜","泡打粉","梅子粉",
                        "乾裙帶菜根","蝦醬","魚露","鹽昆布","蝦油","海蜇皮","乾裙帶菜","鮮味露","玫瑰鹽","沖泡濃湯(海鮮)","豆豉","蠔油","裙帶菜","淡色醬油","韭花醬","辣蒜蓉醬",
                        "豆瓣醬","白曝油","醬油","辣椒醬","薏仁醬油","香菇昆布醬油露","高湯(豬大骨)","辣豆瓣醬","蝦皮","鹹小卷","乾海茸芯","排骨醬","咖哩塊","玉米濃湯調理包","味噌",
                        "安食小品黑豆醬油","黑豆醬油","醬油膏","甘藍乾","炸雞粉","醃漬冬瓜","豆腐乳","炸醬","薄鹽醬油","素食炸醬","低鹽豉油","菜脯","薄鹽醬油(低鈉高鉀)","臘肉(腿肉)",
                        "膽肝","蝦米","乾海帶","蒜味豆豉醬","豆酥","乾小干貝","黑豆油膏","薄鹽黑豆醬油膏","羊肉爐醬","炸排粉","長麵線","龍蝦卵(調味)","鴨賞","和風沙拉醬","薑粉",
                        "魩仔魚(加工,小)","榨菜","花椒粒","干貝(乾)平均值","樹子（破布子）罐頭","紅麴醬","烤肉醬","鳳尾藻","薤","金鉤蝦乾","雞絲麵","干貝(乾)");
    $filter_breath = array("臘肉(五花肉)","山豬肉","牛五花肉火鍋片","培根","鯖魚(炸)","雞尾椎","鯖魚(炒)","鯖魚(蒸)","鯖魚(煎)","鯖魚(煮)","茶鵝","大西洋鮭魚腹肉",
                    "虱目魚腹肉(虱目魚肚)","薄鹽鯖魚","太空鴨","油魚卵(加工)","犬牙南極魚切片","鴨肉","鯖魚(醃製)","熟鵝腿肉","秋刀魚","鯖魚","鯖魚排","雞",
                    "海鱺魚片","日本鰻鱺魚片(生)","鯛魚下巴","日本鰻鱺魚片(蒲燒)","鲭魚","鴨賞","櫻桃鴨胸","鴨胸薄片","鹽漬小鱗脂眼鯡(鹹馧仔)","二節翅(肉雞)",
                    "二節翅(肉雞)","二節翅平均值","三節翅(肉雞)","星斑真鯧","番鴨","鰈魚切片","鯔魚卵","二節翅(土雞)","雞心(肉雞)","雞腿排","紅面番鴨蛋","大西洋鮭魚生魚片",
                    "竹絲雞","骨腿(肉雞)","大西洋鮭魚平均值(去皮)","翅腿(肉雞)","三節翅平均值","鴨頭","冷凍烤雞翅","烤雞","鬍鯰","鵝肉","雞腳(肉雞)","鴨舌",
                    "三節翅(土雞)","大西洋鮭魚切片(尾段)","布氏鯧鰺(含皮)","土雞塊","鹿野土雞","金錢魚","康氏馬加鰆切片(去皮)","無骨雞腿排","虱目魚皮","布氏鯧鰺(去皮)",
                    "大口鰜切片(含皮)","海鱺","清腿(肉雞)","康氏馬加鰆切片(含皮)","臺灣馬加鰆","醃漬鮭魚卵","鰹魚卵","鰆魚卵","鮸魚卵(加工)","棒棒腿(肉雞)","帶骨雞腿",
                    "虱目魚","虱目魚肚","大西洋鮭魚切片(中段)","去骨雞腿","雞骨肉","黑棘鯛(含皮)","褐臭肚魚","鯔切片","刺鯧(含皮)","白帶魚","鮟鱇魚肝","雞排(土雞)",
                    "草魚切片","鞍帶石斑魚片","花身鯻","鴨翅","黑或(或字加魚邊)","扁魚干","大雞腿","雞腿肉","雞腿","豬心","麥奇鈎吻鮭(去皮)","大黃魚","牡蠣干","香魚",
                    "鯔(含皮)","多鱗四指馬鮁","鱅","大眼金梭魚","鯔(去皮)","台灣鯛魚片(油煎)","鱒魚","銀鯧(含皮)","去骨鴨掌","條紋狼鱸","杜氏刺尾鯛","胭脂蝦","清腿(土雞)",
                    "白肉鮭魚切片","鮭魚","黃鰭棘鯛","大西洋鮭魚(台灣養殖)","棒棒腿(土雞)","鴕腩肉","日本銀帶鯡魚干(丁香魚脯)","鷹嘴豆","火雞肉","正鰹","黑棘鯛(去皮)",
                    "三線磯鱸","長體油胡瓜魚(柳葉魚)(裹粉未炸)","鯷魚","阿部牙鯛","日本花鱸(含皮)","尼羅口孵非鯽(含皮)","大口黑鱸","花腹鯖","斑鱧","小魚干","魚卵",
                    "銀鯧(去皮)","長體油胡瓜魚","平鯛","鵝腿肉","北方長額蝦(加工)","土雞腿","花身副麗魚","高麗馬加鰆","鵝肝","鴨腸","龍虎石斑魚","台灣鯛魚片(烤)","台灣鯛魚片(微波)",
                    "棕點石斑魚","葉唇笛鯛","台灣鯛魚片(生)","紅蟳","鯛魚","鯛魚片","雞肝(肉雞)","青嘴龍占魚","花尾唇指翁(翁加魚邊)","金線魚","紅色吳郭魚","台灣鯛魚片(清蒸)",
                    "台灣鯛魚片(水煮)","鰂魚","鯉","正櫻蝦(加工)","鴕腱肉","眼眶魚","尼羅口孵非鯽(去皮)","雞睪丸","金鉤蝦乾","鵝腸","尖嘴鱸(含皮)","鱈魚","真鯛(去皮)","薔薇項鰭魚(去皮)",
                    "烤鴨","雉雞","雙帶鰺","杜氏鰤","鱗鰭叫姑魚","鵝胗","松鯛","去皮清肉(土雞)","蝦米","乾小干貝","綠殼菜蛤","雞膝軟骨(肉雞)","鵝胸肉","小卷干",
                    "日本銀帶鯡(加工)","范氏副葉鰺","去皮雞胸肉","切片雞胸肉","日本竹筴魚","阿根廷魷","雞胗(肉雞)","草魚(含皮)","無斑圓鰺","帶殼真牡蠣(生蠔)",
                    "長鰭鰤魚","小黃魚(含皮)","單斑笛鯛","去皮清肉(肉雞)","龍蝦卵(調味)","薔薇項鰭魚(含皮)","白姑魚","白緣星鱠","太空鴨(去皮)","黑斑海緋鯉","魩仔魚(加工,小)",
                    "低眼無齒芒魚片(芒加魚邊)","後刺尾鯛","泰勃圓鰺","鯔魚精囊","刺鯧(去皮)","牡蠣","小黃魚(去皮)","大甲鰺","狗母魚(蛇鯔)","雞絞肉","黑齒牡蠣","日本玻璃蝦","大白蝦",
                    "尖鎖管","日本紅目大眼鯛","日本花鱸","鬼頭刀","鱖","干貝(乾)平均值","正櫻蝦(熟)","鋸尾鯛","黃鱔","黃金蜆","蝦皮","鹹小卷","魚乾","小魚乾","日本銀帶鯡",
                    "正櫻蝦(生)","藍圓鰺","蝦夷海扇蛤","真牡蠣","多鱗沙鮻","密點少棘胡椒鯛","星雞魚","眼斑擬石首魚","金目鱸魚","旗魚腹肉","沙丁魚","鴨胗","大口逆鈎鰺","魩仔魚(加工,大)",
                    "環文蛤","波紋橫簾蛤","九孔螺","蝦子","白蝦","鮮蝦","宏都拉斯白蝦","網紋龍占魚","鰻魚","白馬頭魚","吻仔魚","銀魚","雞胸肉","雞胸絞肉","真烏賊(小)",
                    "魩仔魚(加工)","軟翅仔","鳳尾蝦仁","日本對蝦(小)","雙線鬚鰨","前鱗笛鯛","鴕鳥菲力肉排","鴕鳥沙朗肉排","青星九刺鮨魚片","干貝(乾)","香魚片","象牙鳳螺",
                    "日本對蝦平均值","鬚赤蝦仁","草對蝦","史氏紅諧魚","黃姑魚","鮪魚","章魚","單角革單棘魨(去皮)","藍對蝦","血斑異大眼鯛","駝背鱸","日本馬頭魚","里肌肉(土雞)",
                    "雞肉","雞肉絲","里肌肉(肉雞)","魚翅唇","蛤蜊","菲律賓簾蛤","螳螂蝦","日本對蝦(大)","印度鐮齒魚","白對蝦(小)","鬼頭刀魚片","鱗馬鞭魚","鮟鱇斑海鯰",
                    "烏鯧","深海鱗角魚","斑帶石斑魚","鮸","干貝","瑤柱","鬚赤蝦","旗魚肚","鯊魚煙","台灣鎖管","海鱸蝦仁","斑點雞籠鯧","翻車魨腹肉","紅蝦仁","蟹腳肉","東方異腕蝦",
                    "蝦仁","相模後海螯蝦","大管鞭蝦","鴨血","真烏賊(大)","橫紋九刺鮨","斑鱵","鱷形叉尾鶴鱵","孟加拉笛鯛","大文蛤","大頭蝦仁","烏賊精囊","哈氏彷對蝦",
                    "刻花魷魚片","發泡魷魚","隆脊管鞭蝦","中國對蝦","斑點九刺鮨(去皮)","羅氏沼蝦","姬魚","石狗公","藍豬齒魚","金鱗魚","蛙形蟹","白蝦仁","翻車魨魚皮",
                    "日本龍蝦","鯊魚皮","鮑魚","魟魚","環紋簑鮋","克氏兔頭魨","赤鰭笛鯛","紅海參","雙髻鯊","黃擬烏尾鮗","黑烏參","藍點鸚哥魚","斑點九刺鮨(含皮)","希氏姬鯛",
                    "鱸魚","斑鰭飛魚","仿刺參","白海參","青星九刺鮨","大目鮪","斯氏長鰭烏魴","鮪魚生魚片","海蜇皮","雙髻鯊腹肉","馬拉巴笛鯛","橫紋鸚哥魚(含皮)","單角革單棘魨(含皮)",
                    "南美刺參","福氏鸚哥魚(去皮)","鯊魚切片","旗魚","鯊魚翅","蝦醬","魚露","鹽昆布","蝦油","梅乾菜","豆豉","辣蒜蓉醬","豆瓣醬","辣豆瓣醬","辣椒醬","鹹小卷",
                    "醃漬冬瓜","豆腐乳","菜脯","臘肉(腿肉)","榨菜","花椒粒","樹子（破布子）罐頭","麻婆醬","黑胡椒醬","醃漬花胡瓜","片狀肉乾(牛肉,辣味)","醃辣椒","豬肉脯",
                    "鹽漬小鱗脂眼鯡(鹹馧仔)","欖角(青橄欖)","豬肉酥","花瓜罐頭","酸菜","鹹菜心","煙燻甜辣椒片","干貝醬","切片火腿(雞肉)","條狀火腿(豬肉)","火腿片","三明治火腿片",
                    "XO醬傳奇","香菇麵筋罐頭","醃燻豬肝","醃漬越瓜","綠殼菜蛤干(淡菜)","醬油西瓜子","醬肘子","鹽酥葵瓜子(帶殼)","香筍鮪魚罐頭","鯖魚肉脯","煙燻豆皮","德國香腸",
                    "酸甘藍菜","泡菜","韓式泡菜","冷凍魷魚圈","清蒸蝦仁肉圓","臭豆腐","草菇罐頭","冬瓜糖磚","覆盆子糖漿","糖衣牛奶巧克力","芭樂果乾");
    $filter_kidney = array("牛肝","豬肝","豬肺","豬肝連","牛肚(蜂巢胃)","牛肚(瘤胃)","牛肚切片(瘤胃)","牛肚平均值","去骨鴨掌","豬大腸","豬小腸","豬心",
                    "豬肚","豬脾臟","豬腎","豬腦","醃燻豬肝","鴨胗","鴨腸","膽肝","鮟鱇魚肝","雞心(肉雞)","雞肝(肉雞)","雞胗(肉雞)","鵝心","鵝肝","鵝胗","鵝腸");

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
