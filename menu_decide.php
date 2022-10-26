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

    $search = array_search(min($now_category),$now_category);
    $recommend_dishID = array();
    $recommend_dishName = array();
    $recommend_method = array();

    if($search==0){ 
    #全榖雜糧量最少
        $query = "SELECT * FROM `ingredients` WHERE `NID`='1'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];

            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];

                $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                $result = $link->query($query);
                foreach ($result as $row){
                    $recommend_dish_ID=$row["ID"];
                    $recommend_dish_Name=$row["dishName"];
                    $recommend_dish_method=$row["method"];
                    array_push($recommend_dishID,$recommend_dish_ID);
                    array_push($recommend_dishName,$recommend_dish_Name);
                    array_push($recommend_method,$recommend_dish_method);
                }
            }
        }
    }
    else if($search==1){
    #蛋豆魚肉量最少
        $query = "SELECT * FROM `ingredients` WHERE `NID`='2'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];

            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];

                $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                $result = $link->query($query);
                foreach ($result as $row){
                    $recommend_dish_ID=$row["ID"];
                    $recommend_dish_Name=$row["dishName"];
                    array_push($recommend_dishID,$recommend_dish_ID);
                }
            }
        }

    }
    else if($search==2){
    #乳品類量最少
        $query = "SELECT * FROM `ingredients` WHERE `NID`='3'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];

            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];

                $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                $result = $link->query($query);
                foreach ($result as $row){
                    $recommend_dish_ID=$row["ID"];
                    $recommend_dish_Name=$row["dishName"];
                    array_push($recommend_dishID,$recommend_dish_ID);
                }
            }
        }
    }
    else if($search==3){
    #蔬菜類量最少
        $query = "SELECT * FROM `ingredients` WHERE `NID`='4'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];

            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];

                $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                $result = $link->query($query);
                foreach ($result as $row){
                    $recommend_dish_ID=$row["ID"];
                    $recommend_dish_Name=$row["dishName"];
                    array_push($recommend_dishID,$recommend_dish_ID);
                }
            }
        }
    }
    else if($search==4){
    #水果類量最少
        $query = "SELECT * FROM `ingredients` WHERE `NID`='5'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];

            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];

                $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                $result = $link->query($query);
                foreach ($result as $row){
                    $recommend_dish_ID=$row["ID"];
                    $recommend_dish_Name=$row["dishName"];
                    array_push($recommend_dishID,$recommend_dish_ID);
                }
            }
        }
    }
    else if($search==5){
    #油脂與堅果種子類量最少
        $query = "SELECT * FROM `ingredients` WHERE `NID`='6'";
        $result = $link->query($query);
        foreach ($result as $row){
            $recommend_ingredients_iID=$row["iID"];

            $query = "SELECT * FROM `recipe` WHERE `iID`='$recommend_ingredients_iID'";
            $result = $link->query($query);
            foreach ($result as $row){
                $recommend_recipe_dishID=$row["dishID"];

                $query = "SELECT * FROM `dish` WHERE `ID`='$recommend_recipe_dishID'";
                $result = $link->query($query);
                foreach ($result as $row){
                    $recommend_dish_ID=$row["ID"];
                    $recommend_dish_Name=$row["dishName"];
                    array_push($recommend_dishID,$recommend_dish_ID);
                }
            }
        }
    }
    $use_recommend_dishID = array_unique($recommend_dishID);
    $use_recommend_dishName = array_unique($recommend_dishName);
    $use_recommend_method = array_unique($recommend_method);
    $count_use_recommend_dishID = count($use_recommend_dishID);
    print_r($use_recommend_dishName);
    # 迴圈跑出推薦菜單的ID
    # foreach ($use_recommend_dishID as $use_recommend_dishID){
    #    echo $use_recommend_dishID."<br>";
    # }
    # 獲取陣列內有多少個
    # echo count($use_recommend_dishID);
    # 使用rand()去抓10個ID去推薦

?>
